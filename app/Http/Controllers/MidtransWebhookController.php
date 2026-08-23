<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        // Log payload untuk mempermudah debugging
        \Illuminate\Support\Facades\Log::info('Midtrans Webhook Payload:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload: order_id is missing'], 200);
        }

        // Mencari ID transaksi tersebut di database lokal kita
        $transaction = Transaction::with('event')->where('order_id', $orderId)->first();

        if (!$transaction) {
            // Mengembalikan status 200 agar tes koneksi Midtrans berhasil (menggunakan dummy order_id)
            return response()->json(['message' => 'Transaction not found, but webhook endpoint is active'], 200);
        }

        // Cegah proses berulang jika status sudah lunas/sukses
        if ($transaction->status === 'settlement' || $transaction->status === 'success') {
            return response()->json(['message' => 'Already processed']);
        }

        // Logika Penerjemahan Status Midtrans API
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $transaction->status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $transaction->status = 'success';
                $this->processSuccess($transaction);
            }
        } else if ($transactionStatus == 'settlement') {
            $transaction->status = 'settlement';
            $this->processSuccess($transaction);
        } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            // RELEASE TICKET (Lepaskan stok tiket yang sempat di-reserve)
            if ($transaction->status === 'Pending' || $transaction->status === 'pending') {
                if ($transaction->event) {
                    $transaction->event->increment('stock', 1);
                    Log::info("Tiket dilepaskan (+1) karena transaksi {$orderId} berstatus {$transactionStatus}.");
                }
            }
            $transaction->status = 'failed';
        } else if ($transactionStatus == 'pending') {
            $transaction->status = 'pending';
        }

        $transaction->save();
        return response()->json(['message' => 'OK']);
    }

    private function processSuccess(Transaction $transaction)
    {
        // Tiket sudah di-reserve saat user klik Checkout. 
        // Jadi kita tidak perlu memotong stok lagi di sini.
        
        // Mengirimkan email E-Ticket ke pelanggan
        try {
            \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\EventTicketMail($transaction));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email E-Ticket: ' . $e->getMessage());
        }
    }
}