<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Transaction;
use App\Mail\EventTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Cegah Check-out Jika Tiket Habis
        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX (Unik)
        $orderId = 'TRX-' . time() . '-' . Str::random(5);

        // =========================================================================
        // LOGIKA KHUSUS: BYPASS TRANSAKSI JIKA ACARA GRATIS ($event->price == 0)
        // =========================================================================
        if ($event->price == 0) {
            try {
                DB::transaction(function () use ($request, $event, $orderId, &$transaction) {
                    // a. Merekam Transaksi dengan harga 0 & Status Langsung Success
                    $transaction = Transaction::create([
                        'event_id'       => $event->id,
                        'order_id'       => $orderId,
                        'customer_name'  => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone,
                        'total_price'    => 0,
                        'status'         => 'success', // Langsung aktif tanpa bayar
                    ]);

                    // b. Kurangi stok tiket saat itu juga
                    $event->decrement('stock', 1);
                });

                // c. Kirim E-Ticket via Email (Opsional/Try-Catch agar tidak menggagalkan flow)
                try {
                    Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email E-Ticket Acara Gratis: ' . $e->getMessage());
                }

                // d. Langsung redirect ke rute sukses (E-Ticket terbit)
                return redirect()->route('checkout.success', $transaction->order_id)
                                 ->with('success', 'Pendaftaran berhasil! E-Ticket Anda telah terbit.');

            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses pendaftaran acara gratis: ' . $e->getMessage());
            }
        }

        // =========================================================================
        // ALUR TRANSAKSI BERBAYAR (MIDTRANS SNAP)
        // =========================================================================
        
        $totalPrice = $event->price + 5000; // Menambahkan biaya admin (dummy)

        // 4. Merekam Transaksi Berbayar ke Database dengan Status Awal 'Pending'
        $transaction = Transaction::create([
            'event_id'       => $event->id,
            'order_id'       => $orderId,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price'    => $totalPrice,
            'status'         => 'Pending',
        ]);

        // --- INTEGRASI SNAP MIDTRANS ---
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false; // Mode Sandbox!
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email'      => $request->customer_email,
                'phone'      => $request->customer_phone,
            ],
        ];

        try {
            // Generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan Snap Token ke database
            $transaction->update(['snap_token' => $snapToken]);
            
            // Redirect ke halaman pembayaran
            return redirect()->route('checkout.payment', $transaction->order_id);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
        $categories = Category::all();

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function success($order_id)
    {
        $categories = Category::all();

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        
        // JIKA TRANSAKSI DARI ACARA GRATIS (Harga = 0 / Status sudah Success)
        if ($transaction->total_price == 0 || strtolower($transaction->status) === 'success') {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        // =========================================================================
        // PENGECEKAN STATUS MIDTRANS UTK TRANSAKSI BERBAYAR
        // =========================================================================
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        try {
            // Mengecek status pesanan secara mandiri ke API Midtrans
            $status = \Midtrans\Transaction::status($order_id);
            
            if ($status) {
                $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');
                
                // Jika API Midtrans mengonfirmasi transaksi sukses (settlement / capture)
                if (in_array($trx_status, ['settlement', 'capture'])) {
                    if (strtolower($transaction->status) === 'pending') {
                        $transaction->update(['status' => 'success']);
                        
                        // Kurangi stok jika belum berkurang
                        if ($transaction->event && $transaction->event->stock > 0) {
                            $transaction->event->decrement('stock', 1);
                            
                            try {
                                Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                            } catch (\Exception $e) {
                                Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}