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
        // Cegah akses form checkout jika event sudah selesai
        if (\Carbon\Carbon::parse($event->date)->isPast()) {
            return redirect()->route('events.show', $event->id)->with('error', 'Pendaftaran tidak dapat dilakukan karena event sudah selesai.');
        }

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

        // 2. Cegah Check-out Jika Tiket Habis atau Event Selesai
        if (\Carbon\Carbon::parse($event->date)->isPast()) {
            return back()->with('error', 'Mohon maaf, pendaftaran ditutup karena event sudah selesai.');
        }

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
                // c. Kirim E-Ticket via Email
                try {
                    \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\TicketPurchased($transaction));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email TicketPurchased Acara Gratis: ' . $e->getMessage());
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
        
        $ticketPrice = $event->price;
        $discountAmount = 0;

        // Cek Voucher
        if ($request->filled('voucher_code')) {
            $voucher = \App\Models\Voucher::where('code', strtoupper($request->voucher_code))
                ->where(function($query) use ($event) {
                    $query->whereNull('event_id')->orWhere('event_id', $event->id);
                })
                ->where(function($query) {
                    $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                })
                ->where(function($query) {
                    $query->whereNull('quota')->orWhere('quota', '>', 0);
                })
                ->first();

            if ($voucher) {
                $discountAmount = ($ticketPrice * $voucher->discount_percent) / 100;
                
                // Kurangi kuota jika ada
                if (!is_null($voucher->quota)) {
                    $voucher->decrement('quota', 1);
                }
            } else {
                return back()->with('error', 'Kode voucher tidak valid, tidak berlaku untuk event ini, sudah kadaluarsa, atau kuota habis.')->withInput();
            }
        }

        $totalPrice = ($ticketPrice - $discountAmount) + 5000; // Harga setelah diskon + biaya admin

        // 4. Merekam Transaksi Berbayar ke Database dengan Status Awal 'Pending'
        // RESERVASI TIKET: Potong stok tiket sekarang juga untuk menghindari race condition
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'event_id'       => $event->id,
                'order_id'       => $orderId,
                'customer_name'  => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_price'    => $totalPrice,
                'status'         => 'Pending',
            ]);

            // Reserve stok tiket langsung
            $event->decrement('stock', 1);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pendaftaran: ' . $e->getMessage());
        }

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
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minute',
                'duration'   => 15
            ]
        ];

        try {
            // Generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan Snap Token ke database
            $transaction->update(['snap_token' => $snapToken]);
            
            // Kirim email pengingat pembayaran
            try {
                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\UnpaidTicketReminder($transaction));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email UnpaidTicketReminder: ' . $e->getMessage());
            }
            
            // Redirect ke halaman pembayaran
            return redirect()->route('checkout.payment', $transaction->order_id);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
        }
    }

    /**
     * Mengecek dan menerapkan voucher secara asynchronous (AJAX)
     */
    public function applyVoucher(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $voucherCode = strtoupper($request->voucher_code);

        $voucher = \App\Models\Voucher::where('code', $voucherCode)
            ->where(function($query) use ($event) {
                $query->whereNull('event_id')->orWhere('event_id', $event->id);
            })
            ->where(function($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->where(function($query) {
                $query->whereNull('quota')->orWhere('quota', '>', 0);
            })
            ->first();

        if ($voucher) {
            $ticketPrice = $event->price;
            $discountAmount = ($ticketPrice * $voucher->discount_percent) / 100;
            $totalPrice = ($ticketPrice - $discountAmount) + 5000; // Harga setelah diskon + biaya layanan

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diterapkan!',
                'discount_percent' => $voucher->discount_percent,
                'discount_amount' => $discountAmount,
                'new_total_price' => $totalPrice,
                'new_total_price_formatted' => 'Rp ' . number_format($totalPrice, 0, ',', '.')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode voucher tidak valid, kadaluarsa, atau tidak berlaku untuk event ini.'
        ], 400);
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
                        
                        // Stok sudah dikurangi di awal saat klik Checkout (Reserve Ticket)
                        // Jadi di sini tidak perlu mengurangi stok lagi.
                        
                        try {
                            Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                        } catch (\Exception $e) {
                            Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
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