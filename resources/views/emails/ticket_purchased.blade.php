<!DOCTYPE html>
<html>
<head>
    <title>Tiket Berhasil Dibeli</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; text-align: center; }
        .barcode { margin: 30px 0; padding: 20px; background: #f8fafc; border-radius: 8px; border: 2px dashed #cbd5e1; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tiket Berhasil Dibeli!</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $transaction->name }}</strong>,</p>
            <p>Selamat! Pembayaran untuk tiket acara <strong>{{ $transaction->event->title }}</strong> telah berhasil diverifikasi.</p>
            
            <div class="barcode">
                <p style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">QR Code Tiket Anda (Tunjukkan saat Check-in)</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $transaction->order_id }}" alt="QR Code" width="200" height="200">
                <p style="font-size: 12px; margin-top: 10px; color: #64748b;">Order ID: {{ $transaction->order_id }}</p>
            </div>
            
            <p>Harap simpan email ini dan tunjukkan QR Code di atas saat kedatangan Anda di lokasi acara.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AmikomEventHub. All rights reserved.
        </div>
    </div>
</body>
</html>
