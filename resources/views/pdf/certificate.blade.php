<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Certificate</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #ffffff;
            color: #333;
            text-align: center;
        }
        .cert-container {
            width: 100%;
            height: 100vh; /* Adjust if needed based on orientation */
            padding: 50px;
            box-sizing: border-box;
            border: 20px solid #0f172a;
            position: relative;
        }
        .cert-header {
            font-size: 50px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 50px;
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .cert-sub {
            font-size: 20px;
            color: #64748b;
            margin-top: 20px;
        }
        .cert-name {
            font-size: 45px;
            font-weight: bold;
            color: #4f46e5;
            margin-top: 40px;
            text-decoration: underline;
        }
        .cert-desc {
            font-size: 22px;
            margin-top: 30px;
            color: #333;
        }
        .cert-event {
            font-size: 35px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 20px;
        }
        .cert-date {
            font-size: 18px;
            color: #64748b;
            margin-top: 40px;
        }
        .cert-footer {
            margin-top: 80px;
        }
        .signature-line {
            width: 250px;
            border-bottom: 2px solid #0f172a;
            margin: 0 auto;
            margin-bottom: 10px;
        }
        .signature-name {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="cert-header">
            Certificate of Attendance
        </div>
        
        <div class="cert-sub">
            This is to certify that
        </div>
        
        <div class="cert-name">
            {{ $transaction->name }}
        </div>
        
        <div class="cert-desc">
            has successfully attended and participated in
        </div>
        
        <div class="cert-event">
            {{ $transaction->event->title }}
        </div>
        
        <div class="cert-date">
            Organized by AmikomEventHub on {{ \Carbon\Carbon::parse($transaction->event->date)->format('F d, Y') }}<br>
            at {{ $transaction->event->location }}
        </div>
        
        <div class="cert-footer">
            <div class="signature-line"></div>
            <div class="signature-name">AmikomEventHub Organizer</div>
        </div>
    </div>
</body>
</html>
