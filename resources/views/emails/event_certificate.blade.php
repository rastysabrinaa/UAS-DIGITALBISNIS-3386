<!DOCTYPE html>
<html>
<head>
    <title>E-Certificate Kehadiran</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #0f172a; color: #fbbf24; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>E-Certificate Kehadiran</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $transaction->name }}</strong>,</p>
            <p>Terima kasih telah hadir dan berpartisipasi dalam acara <strong>{{ $transaction->event->title }}</strong>.</p>
            <p>Sebagai bentuk apresiasi kami, terlampir E-Certificate resmi kehadiran Anda pada email ini (dalam format PDF).</p>
            <p>Semoga ilmu dan pengalaman yang didapatkan bermanfaat!</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AmikomEventHub. All rights reserved.
        </div>
    </div>
</body>
</html>
