@extends('layouts.admin')
@section('page_title', 'Check-in Scanner')
@section('page_subtitle', 'Validasi tiket peserta untuk acara ' . $event->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8">
        <h3 class="text-xl font-bold mb-4">Arahkan Kamera ke QR Code Peserta</h3>
        <p class="text-slate-500 mb-6">Sistem akan otomatis mengecek validitas tiket. Pastikan kamera memiliki pencahayaan yang cukup.</p>
        
        <div id="reader" class="w-full max-w-lg mx-auto rounded-2xl overflow-hidden border-4 border-indigo-100"></div>
        
        <div id="result-box" class="hidden mt-8 p-6 rounded-2xl text-center shadow-sm">
            <h4 id="result-title" class="text-2xl font-black mb-2"></h4>
            <p id="result-desc" class="text-lg"></p>
            <button onclick="resumeScanning()" class="mt-6 px-6 py-2 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-900 transition">Scan Tiket Berikutnya</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner;
    
    function onScanSuccess(decodedText, decodedResult) {
        // Hentikan scan sementara
        html5QrcodeScanner.pause();
        
        // Memanggil API validasi
        fetch("{{ route('admin.scanner.verify', $event->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            const resultBox = document.getElementById('result-box');
            const resultTitle = document.getElementById('result-title');
            const resultDesc = document.getElementById('result-desc');
            
            resultBox.classList.remove('hidden', 'bg-emerald-100', 'text-emerald-800', 'bg-red-100', 'text-red-800', 'border', 'border-emerald-200', 'border-red-200');
            
            if (data.success) {
                resultBox.classList.add('bg-emerald-100', 'text-emerald-800', 'border', 'border-emerald-200');
                resultTitle.innerText = "Berhasil!";
                resultDesc.innerText = data.message;
            } else {
                resultBox.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
                resultTitle.innerText = "Ditolak!";
                resultDesc.innerText = data.message;
            }
        })
        .catch(err => {
            console.error("Error validasi:", err);
            html5QrcodeScanner.resume();
        });
    }

    function onScanFailure(error) {
        // Menangani peringatan namun tidak perlu dicetak
    }

    function resumeScanning() {
        document.getElementById('result-box').classList.add('hidden');
        html5QrcodeScanner.resume();
    }

    // Inisialisasi Scanner
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: {width: 250, height: 250} },
        false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
