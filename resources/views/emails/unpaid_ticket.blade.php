<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Pembayaran Tiket</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .button { display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pengingat Pembayaran</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $transaction->name }}</strong>,</p>
            <p>Terima kasih telah memesan tiket untuk acara <strong>{{ $transaction->event->title }}</strong>.</p>
            <p>Kami menyadari bahwa Anda belum menyelesaikan pembayaran untuk Order ID: <strong>{{ $transaction->order_id }}</strong>.</p>
            <p>Segera selesaikan pembayaran Anda sebesar <strong>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong> agar tidak kehabisan tiket.</p>
            
            <a href="{{ route('checkout.payment', $transaction->order_id) }}" class="button">Bayar Sekarang</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AmikomEventHub. All rights reserved.
        </div>
    </div>
</body>
</html>
