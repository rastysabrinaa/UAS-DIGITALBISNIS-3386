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

    @php
        $isPast = \Carbon\Carbon::parse($event->date)->isPast();
        $imagePath = $event->poster_path ?? $event->banner;
    @endphp

    {{-- INFORMASI EVENT --}}
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                {{-- Banner/Gambar Event --}}
                @if($imagePath && Storage::disk('public')->exists($imagePath))
                    <img src="{{ asset('storage/' . $imagePath) }}" class="card-img-top {{ $isPast ? 'grayscale' : '' }}" alt="{{ $event->title }}" style="max-height: 420px; object-fit: cover;">
                @else
                    <div class="bg-dark text-white text-center py-5 rounded-top">
                        <h3 class="mb-0 fw-bold">{{ $event->title }}</h3>
                    </div>
                @endif

                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ $event->category->name ?? 'Umum' }}</span>
                        
                        <!-- Badge Status Event -->
                        <span class="badge {{ $isPast ? 'bg-secondary' : 'bg-success' }} rounded-pill px-3 py-2">
                            {{ $isPast ? 'Acara Selesai' : 'Mendatang' }}
                        </span>

                        @if(($event->reviews ?? collect())->count() > 0)
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                ★ {{ number_format($event->reviews->avg('rating'), 1) }} ({{ $event->reviews->count() }} ulasan)
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="card-title fw-bold mb-3 display-6">{{ $event->title }}</h1>
                    
                    {{-- Profil Penyelenggara/Organizer --}}
                    @if($event->organizer)
                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3 border">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px;">
                                {{ strtoupper(substr($event->organizer->name ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <small class="text-muted d-block">Diselenggarakan Oleh:</small>
                                <a href="{{ route('organizer.profile', $event->organizer_id) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $event->organizer->name ?? 'Penyelenggara' }} <i class="fas fa-external-link-alt ms-1 small text-primary"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <h5 class="fw-bold border-bottom pb-2">Deskripsi Acara</h5>
                    <p class="card-text text-secondary lh-lg" style="white-space: pre-line;">{{ $event->description }}</p>
                </div>
            </div>
        </div>

        {{-- SIDEBAR DETAIL WAKTU & TIKET --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Detail Pelaksanaan</h5>
                    
                    <div class="mb-3 d-flex align-items-start gap-2">
                        <i class="far fa-calendar-alt text-primary fs-5 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Tanggal & Waktu:</small>
                            <strong>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y - H:i') }} WIB</strong>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-start gap-2">
                        <i class="fas fa-map-marker-alt text-danger fs-5 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Lokasi:</small>
                            <strong>{{ $event->location }}</strong>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-start gap-2">
                        <i class="fas fa-tag text-success fs-5 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Harga Tiket:</small>
                            <h4 class="text-primary fw-bold mb-0">
                                {{ $event->price > 0 ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'Gratis' }}
                            </h4>
                        </div>
                    </div>

                    <div class="mb-4 d-flex align-items-start gap-2">
                        <i class="fas fa-ticket-alt text-warning fs-5 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Sisa Stok Tiket:</small>
                            <span class="badge bg-info text-dark font-monospace px-2 py-1">{{ $event->stock ?? 0 }} Tiket Tersedia</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Tombol Beli Tiket --}}
                    @if($isPast)
                        <button class="btn btn-secondary btn-lg w-100 fw-bold rounded-3" disabled>
                            Acara Telah Selesai
                        </button>
                    @elseif(($event->stock ?? 0) > 0)
                        <form action="{{ url('/checkout/' . $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3">
                                <i class="fas fa-shopping-cart me-2"></i>Beli Tiket Sekarang
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary btn-lg w-100 fw-bold rounded-3" disabled>
                            Tiket Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5 opacity-25">

    {{-- ====================================================== --}}
    {{-- BAGIAN ULASAN DAN PENILAIAN BINTANG (REVIEW SECTION)   --}}
    {{-- ====================================================== --}}
    <div class="row">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4">Ulasan & Testimoni Peserta</h3>

            {{-- SUMMARY RINGKASAN RATING --}}
            @php
                $reviews = $event->reviews ?? collect();
                $totalReviews = $reviews->count();
                $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;
            @endphp

            @if($totalReviews > 0)
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center border-end">
                            <h1 class="display-3 fw-bold text-dark mb-0">{{ $avgRating }}</h1>
                            <div class="text-warning fs-4 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    {!! $i <= round($avgRating) ? '&#9733;' : '<span class="text-muted opacity-25">&#9733;</span>' !!}
                                @endfor
                            </div>
                            <small class="text-muted">Dari total {{ $totalReviews }} ulasan</small>
                        </div>
                        <div class="col-md-8 ps-md-4 mt-3 mt-md-0">
                            @for($star = 5; $star >= 1; $star--)
                                @php
                                    $countStar = $reviews->where('rating', $star)->count();
                                    $percentage = $totalReviews > 0 ? ($countStar / $totalReviews) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-1">
                                    <small class="fw-bold text-secondary me-2" style="width: 50px;">{{ $star }} ★</small>
                                    <div class="progress flex-grow-1 me-3" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small class="text-muted" style="width: 30px;">{{ $countStar }}</small>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            @endif

            {{-- 1. FORM KIRIM ULASAN --}}
            @if($isPast)
                @auth
                    <div class="card shadow-sm border-0 rounded-4 mb-5">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3"><i class="far fa-comment-dots text-primary me-2"></i>Beri Ulasan untuk Acara Ini</h5>
                            
                            <form action="{{ url('/events/' . $event->id . '/reviews') }}" method="POST">
                                @csrf
                                
                                {{-- Pilih Rating Bintang --}}
                                <div class="mb-3">
                                    <label for="rating" class="form-label fw-semibold">Rating Bintang</label>
                                    <select name="rating" id="rating" class="form-select rounded-3 @error('rating') is-invalid @enderror" required>
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
                                    <label for="comment" class="form-label fw-semibold">Ulasan / Testimoni Anda</label>
                                    <textarea name="comment" id="comment" rows="3" 
                                        class="form-control rounded-3 @error('comment') is-invalid @enderror" 
                                        placeholder="Bagikan pengalamanmu mengikuti acara ini..." required></textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-success fw-bold px-4 rounded-3">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Ulasan
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning rounded-3 shadow-sm mb-4">
                        <i class="fas fa-lock me-2"></i>Silakan <a href="{{ route('login') }}" class="alert-link">login terlebih dahulu</a> untuk memberikan ulasan pada acara ini.
                    </div>
                @endauth
            @else
                <div class="alert alert-info rounded-3 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i>Ulasan dan penilaian akan dibuka setelah acara ini selesai dilaksanakan.
                </div>
            @endif

            {{-- 2. DAFTAR ULASAN --}}
            <div class="review-list">
                @forelse($reviews as $review)
                    <div class="card shadow-sm border-0 rounded-4 mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    {{-- Avatar Inisial Nama --}}
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($review->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">{{ $review->user->name ?? 'Pengguna' }}</strong>
                                        <small class="text-muted">{{ $review->created_at ? $review->created_at->diffForHumans() : '-' }}</small>
                                    </div>
                                </div>
                                {{-- Menampilkan Bintang --}}
                                <div class="text-warning fs-5">
                                    @for($i = 1; $i <= 5; $i++)
                                        {!! $i <= $review->rating ? '&#9733;' : '<span class="text-muted opacity-25">&#9733;</span>' !!}
                                    @endfor
                                </div>
                            </div>
                            <p class="card-text text-secondary mb-0 bg-light p-3 rounded-3" style="white-space: pre-line;">{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-light rounded-4 text-muted border">
                        <i class="far fa-comments fs-1 d-block mb-2 text-secondary"></i>
                        <p class="mb-0 fw-semibold">Belum ada ulasan untuk acara ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection