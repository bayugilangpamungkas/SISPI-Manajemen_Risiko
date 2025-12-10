@extends('layout.app')
@section('title', 'Berita Acara')

@php($isWelcomePage = true)

@push('style')
    <style>
        body {
            background: #f7fafc;
        }

@include('components.minute-card-styles')

        #app .main-wrapper {
            display: block;
            padding-left: 0;
        }

        .main-footer {
            margin-left: 0;
        }

        .welcome-berita-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .welcome-berita-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 120px 0 80px;
        }

        .welcome-berita-hero .hero-content {
            max-width: 720px;
            margin: 0 auto;
        }

        .welcome-berita-hero .btn {
            border-radius: 999px;
            padding: 10px 28px;
            border-width: 2px;
        }

        .welcome-berita-main {
            flex: 1;
        }

        @media (max-width: 768px) {
            .welcome-berita-hero {
                padding: 80px 0 60px;
            }

            .welcome-berita-hero .display-4 {
                font-size: 2.5rem;
            }
        }
    </style>
@endpush

@section('main')
    <div class="welcome-berita-wrapper">
        <header class="welcome-berita-hero">
            <div class="container">
                <div class="hero-content text-center">
                    <h1 class="display-4">Seluruh Berita Acara</h1>
                    <p class="lead">Telusuri riwayat lengkap kegiatan, dokumen pendukung, serta dokumentasi visual.</p>
                    <a href="{{ url('/welcome') }}" class="btn btn-outline-light mt-3">
                        <i class="fas fa-chevron-left mr-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </header>

        <main class="welcome-berita-main py-5">
            <div class="container">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('welcome.berita-acara') }}">
                            <div class="form-row">
                                <div class="form-group col-md-4 mb-3">
                                    <label for="search">Pencarian</label>
                                    <input id="search" type="text" name="search" class="form-control"
                                           value="{{ $filters['search'] ?? '' }}" placeholder="Judul, ringkasan, lokasi">
                                </div>
                                <div class="form-group col-md-3 mb-3">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input id="start_date" type="date" name="start_date" class="form-control"
                                           value="{{ $filters['start_date'] ?? '' }}">
                                </div>
                                <div class="form-group col-md-3 mb-3">
                                    <label for="end_date">Tanggal Selesai</label>
                                    <input id="end_date" type="date" name="end_date" class="form-control"
                                           value="{{ $filters['end_date'] ?? '' }}">
                                </div>
                                <div class="form-group col-md-2 d-flex align-items-end mb-3">
                                    <div class="btn-group btn-block">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search mr-1"></i>Cari
                                        </button>
                                        <a href="{{ route('welcome.berita-acara') }}" class="btn btn-outline-secondary">
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    @forelse ($minutes as $minute)
                        <div class="col-md-6 col-lg-4 mb-4">
                            @include('components.minute-card', ['minute' => $minute])
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                Tidak menemukan berita acara sesuai filter yang dipilih.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center">
                    {{ $minutes->appends($filters ?? [])->links() }}
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    @include('components.minute-card-script')
@endpush
