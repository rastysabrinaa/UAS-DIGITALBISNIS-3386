@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- ALERT PESAN SUKSES / ERROR --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- INFORMASI EVENT --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                {{-- Banner/Gambar Event --}}
                @if($event->banner)
                    <img src="{{ asset('storage/' . $event->banner) }}" class="card-img-top" alt="{{ $event->title }}" style="max-height: 400px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white text-center py-5 rounded-top">
                        <h3 class="mb-0">{{ $event->title }}</h3>
                    </div>
                @endif

                <div class="card-body p-4">
                    <span class="badge bg-primary mb-2">{{ $event->category->name ?? 'Umum' }}</span>
                    <h1 class="card-title fw-bold mb-3">{{ $event->title }}</h1>
                    
                    {{-- Profil Penyelenggara/Organizer --}}
                    @if($event->organizer)
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                            <div class="ms-2">
                                <small class="text-muted d-block">Diselenggarakan Oleh:</small>
                                <a href="{{ route('organizer.profile', $event->organizer_id) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $event->organizer->name ?? 'Penyelenggara' }} <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <h5 class="fw-bold">Deskripsi Acara</h5>
                    <p class="card-text text-secondary" style="white-space: pre-line;">{{ $event->description }}</p>
                </div>
            </div>
        </div>

        {{-- SIDEBAR DETAIL WAKTU & TIKET --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Detail Pelaksanaan</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Tanggal & Waktu:</small>
                        <strong>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y - H:i') }} WIB</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Lokasi:</small>
                        <strong>{{ $event->location }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Harga Tiket:</small>
                        <h4 class="text-primary fw-bold mb-0">
                            {{ $event->price > 0 ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'Gratis' }}
                        </h4>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Sisa Sisa Stok:</small>
                        <span class="badge bg-info text-dark font-monospace">{{ $event->stock ?? 0 }} Tiket</span>
                    </div>

                    <hr>

                    {{-- Tombol Beli Tiket --}}
                    @if(($event->stock ?? 0) > 0)
                        <a href="{{ route('checkout.create', $event->id) }}" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3">
                            <i class="fas fa-shopping-cart me-2"></i>Beli Tiket Sekarang
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg w-100 fw-bold rounded-3" disabled>
                            Tiket Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    {{-- BAGIAN ULASAN DAN PENILAIAN BINTANG --}}
    <div class="row">
        <div class="col-md-8">
            <h3 class="fw-bold mb-4">Ulasan & Testimoni Peserta</h3>

            {{-- 1. FORM KIRIM ULASAN --}}
            @auth
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3">Beri Ulasan untuk Acara Ini</h5>
                        
                        <form action="{{ url('/events/' . $event->id . '/reviews') }}" method="POST">
                            @csrf
                            
                            {{-- Pilih Rating Bintang --}}
                            <div class="mb-3">
                                <label for="rating" class="form-label font-weight-bold">Rating Bintang</label>
                                <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Penilaian --</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Bagus)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 - Bagus)</option>
                                    <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                    <option value="2">⭐⭐ (2 - Kurang)</option>
                                    <option value="1">⭐ (1 - Sangat Buruk)</option>
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Pesan Komentar --}}
                            <div class="mb-3">
                                <label for="comment" class="form-label font-weight-bold">Ulasan / Testimoni Anda</label>
                                <textarea name="comment" id="comment" rows="3" 
                                    class="form-control @error('comment') is-invalid @enderror" 
                                    placeholder="Bagikan pengalamanmu mengikuti acara ini..." required></textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success font-weight-bold">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Ulasan
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-4">
                    Silakan <a href="{{ route('login') }}" class="alert-link">login terlebih dahulu</a> untuk memberikan ulasan pada acara ini.
                </div>
            @endauth

            {{-- 2. DAFTAR ULASAN --}}
            <div class="review-list">
                @forelse($event->reviews ?? [] as $review)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="d-block">{{ $review->user->name ?? 'Pengguna' }}</strong>
                                    <small class="text-muted">{{ $review->created_at ? $review->created_at->diffForHumans() : '-' }}</small>
                                </div>
                                {{-- Menampilkan Bintang --}}
                                <div class="text-warning fs-5">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            ★
                                        @else
                                            <span class="text-muted">☆</span>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="card-text text-secondary mb-0">{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 bg-light rounded text-muted">
                        <p class="mb-0">Belum ada ulasan untuk acara ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection