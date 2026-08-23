<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Certificate</title>
    <style>
    <style>
        @page { 
            margin: 0; 
            size: A4 landscape; /* Landscape orientation is standard for certificates */
        }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #ffffff;
            color: #1e293b;
            text-align: center;
        }
        .cert-container {
            width: 100%;
            height: 100%;
            padding: 60px;
            box-sizing: border-box;
            border: 25px solid #1e3a8a;
            position: relative;
        }
        .cert-inner {
            border: 5px double #1e3a8a;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png');
        }
        .cert-logo {
            margin-bottom: 20px;
        }
        .cert-header {
            font-size: 55px;
            font-weight: 800;
            color: #1e3a8a;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 6px;
        }
        .cert-sub {
            font-size: 22px;
            color: #475569;
            margin-top: 30px;
            font-style: italic;
        }
        .cert-name {
            font-size: 50px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 45px;
            border-bottom: 2px solid #94a3b8;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 60%;
        }
        .cert-desc {
            font-size: 24px;
            margin-top: 40px;
            color: #334155;
        }
        .cert-event {
            font-size: 38px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 25px;
        }
        .cert-date {
            font-size: 20px;
            color: #475569;
            margin-top: 50px;
            line-height: 1.5;
        }
        .cert-footer {
            position: absolute;
            bottom: 80px;
            right: 120px;
        }
        .signature-line {
            width: 250px;
            border-bottom: 2px solid #1e293b;
            margin: 0 auto;
            margin-bottom: 10px;
        }
        .signature-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
        }
        .signature-role {
            font-size: 16px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="cert-inner">
            <div class="cert-logo">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MDAgNTAwIj48Y2lyY2xlIGN4PSIyNTAiIGN5PSIyNTAiIHI9IjIzMCIgZmlsbD0id2hpdGUiIHN0cm9rZT0iIzFzM2E4YSIgc3Ryb2tlLXdpZHRoPSI4Ii8+PHBhdGggZD0iTTE1MCwyNTAgYTEwMCwxMDAgMCAxLDEgMjAwLDAgYTEwMCwxMDAgMCAxLDEgLTIwMCwwIiBmaWxsPSJub25lIiBzdHJva2U9IiMxZTNhOGEiIHN0cm9rZS13aWR0aD0iMTUiLz48dGV4dCB4PSIyNTAiIHk9IjI3NCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjgwIiBmb250LXdlaWdodD0iYm9sZCIgZmlsbD0iIzFlM2E4YSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+QUVIPC90ZXh0Pjwvc3ZnPg==" width="100" alt="Logo">
            </div>
            
            <div class="cert-header">
                Certificate of Attendance
            </div>
            
            <div class="cert-sub">
                This is to certify that
            </div>
            
            <div class="cert-name">
                {{ $transaction->customer_name }}
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
                <div class="signature-role">Official Signature</div>
            </div>
        </div>
    </div>
</body>
</html>
