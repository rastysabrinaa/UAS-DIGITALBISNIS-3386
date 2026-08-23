<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Pastikan user adalah organizer event tersebut atau superadmin
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.scanner', compact('event'));
    }

    public function verify(Request $request, $eventId)
    {
        $orderId = $request->input('order_id');
        $event = Event::findOrFail($eventId);
        
        // Validasi otoritas
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak memindai tiket untuk acara ini.'
            ], 403);
        }

        $transaction = Transaction::where('order_id', $orderId)
                                  ->where('event_id', $eventId)
                                  ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau tidak valid untuk acara ini.'
            ], 404);
        }

        // Cek apakah pembayaran sukses
        if (strtolower($transaction->status) !== 'success' && strtolower($transaction->status) !== 'settlement') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket belum lunas (Status: ' . $transaction->status . ').'
            ], 400);
        }

        // Cek apakah sudah pernah discan
        if ($transaction->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket sudah digunakan sebelumnya!'
            ], 400);
        }

        // Tandai sebagai sudah digunakan
        $transaction->update(['is_used' => true]);

        // Generate PDF and send Email
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.certificate', compact('transaction'))
                ->setPaper('a4', 'landscape');
            $pdfContent = $pdf->output();

            \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\EventCertificate($transaction, $pdfContent));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim E-Certificate: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket Valid! Selamat datang, ' . $transaction->customer_name . '.',
            'customer_name' => $transaction->customer_name
        ]);
    }
}
