@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- ALERT PESAN SUKSES / ERROR --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER DASHBOARD ORGANIZER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            @if(auth()->user()->organization_logo)
                <img src="{{ asset('storage/' . auth()->user()->organization_logo) }}" alt="Logo" class="rounded-circle me-3 border" style="width: 55px; height: 55px; object-fit: cover;">
            @else
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 fs-4" style="width: 55px; height: 55px;">
                    {{ strtoupper(substr(auth()->user()->organization_name ?? auth()->user()->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h2 class="fw-bold mb-0">{{ auth()->user()->organization_name ?? auth()->user()->name }}</h2>
                <span class="text-muted small">Dashboard Analitik Penyelenggara</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Status Kelayakan Akun oleh Superadmin --}}
            @if(auth()->user()->status === 'approved')
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                    <i class="fas fa-check-circle me-1"></i> Terverifikasi
                </span>
            @elseif(auth()->user()->status === 'pending')
                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill">
                    <i class="fas fa-clock me-1"></i> Menunggu Verifikasi
                </span>
            @else
                <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                    <i class="fas fa-times-circle me-1"></i> Ditolak
                </span>
            @endif

            <a href="{{ route('organizer.events.create') }}" class="btn btn-primary fw-bold shadow-sm rounded-3">
                <i class="fas fa-plus me-1"></i> Buat Acara Baru
            </a>
        </div>
    </div>

    {{-- CARD ANALITIK PENDAPATAN & PENJUALAN --}}
    <div class="row g-3 mb-5">
        {{-- Total Pendapatan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3 h-100 position-relative overflow-hidden">
                <div class="card-body z-1">
                    <small class="text-white-50 fw-semibold text-uppercase">Total Pendapatan</small>
                    <h2 class="fw-bold display-6 mt-2 mb-0">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h2>
                </div>
                <i class="fas fa-wallet position-absolute end-0 bottom-0 text-white opacity-25 m-3" style="font-size: 4rem;"></i>
            </div>
        </div>

        {{-- Tiket Terjual --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3 h-100 position-relative overflow-hidden">
                <div class="card-body z-1">
                    <small class="text-white-50 fw-semibold text-uppercase">Tiket Terjual</small>
                    <h2 class="fw-bold display-6 mt-2 mb-0">{{ number_format($totalTicketsSold ?? 0, 0, ',', '.') }}</h2>
                </div>
                <i class="fas fa-ticket-alt position-absolute end-0 bottom-0 text-white opacity-25 m-3" style="font-size: 4rem;"></i>
            </div>
        </div>

        {{-- Total Acara --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-3 h-100 position-relative overflow-hidden">
                <div class="card-body z-1">
                    <small class="text-white-50 fw-semibold text-uppercase">Total Acara Dibuat</small>
                    <h2 class="fw-bold display-6 mt-2 mb-0">{{ $totalEvents ?? 0 }}</h2>
                </div>
                <i class="fas fa-calendar-alt position-absolute end-0 bottom-0 text-white opacity-25 m-3" style="font-size: 4rem;"></i>
            </div>
        </div>
    </div>

    {{-- TABEL TRANSAKSI TERBARU --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-history text-primary me-2"></i>Transaksi Masuk Terbaru</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Nama Pemesan</th>
                        <th scope="col">Acara</th>
                        <th scope="col">Total Pembayaran</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions ?? [] as $transaction)
                        <tr>
                            <td class="fw-bold font-monospace">{{ $transaction->order_id }}</td>
                            <td>
                                <div>{{ $transaction->customer_name }}</div>
                                <small class="text-muted">{{ $transaction->customer_email }}</small>
                            </td>
                            <td class="fw-semibold text-dark">{{ $transaction->event->title ?? '-' }}</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td>
                                @if(strtolower($transaction->status) === 'success')
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Lunas</span>
                                @elseif(strtolower($transaction->status) === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3">Pending</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3">Gagal</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $transaction->created_at ? $transaction->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada transaksi masuk untuk acara Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DAFTAR EVENT SAYA --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-0"><i class="fas fa-layer-group text-primary me-2"></i>Daftar Acara Anda</h5>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                @forelse($events ?? [] as $event)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-3 h-100 overflow-hidden shadow-sm">
                            @if($event->banner)
                                <img src="{{ asset('storage/' . $event->banner) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 160px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white text-center py-4">
                                    <i class="far fa-image fs-1 opacity-50"></i>
                                </div>
                            @endif
                            <div class="card-body p-3">
                                <span class="badge bg-primary mb-2">{{ $event->category->name ?? 'Umum' }}</span>
                                <h6 class="fw-bold text-dark text-truncate">{{ $event->title }}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="fw-bold text-success">
                                        {{ $event->price > 0 ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'Gratis' }}
                                    </span>
                                    <span class="badge bg-info text-dark font-monospace">
                                        Stok: {{ $event->stock }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <p class="mb-0">Anda belum memiliki acara. Klik tombol <strong>"Buat Acara Baru"</strong> untuk memulainya.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection