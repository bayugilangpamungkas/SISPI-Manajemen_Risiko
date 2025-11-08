@extends('layout.app')
@section('title', 'Berita Acara')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Berita Acara</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item active">Berita Acara</div>
                    </div>
                </div>
                <div class="section-header-button">
                    <a href="{{ route('berita-acara.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Tambah Berita Acara
                    </a>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Manajemen Berita Acara</h2>
                <p class="section-lead">Kelola notulen rapat beserta dokumen dan galeri pendukung.</p>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('berita-acara.index') }}" class="form-inline mb-4">
                            <div class="input-group mr-2" style="min-width: 260px;">
                                <input type="search" name="search" class="form-control" placeholder="Cari judul, lokasi, atau ringkasan" value="{{ $search }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            @if ($search)
                                <a href="{{ route('berita-acara.index') }}" class="btn btn-link">Reset</a>
                            @endif
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Lokasi</th>
                                        <th class="text-center">Dokumen</th>
                                        <th class="text-center">Gambar</th>
                                        <th>Diperbarui</th>
                                        <th style="width: 12%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($minutes as $minute)
                                        <tr>
                                            <td>{{ ($minutes->currentPage() - 1) * $minutes->perPage() + $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $minute->title }}</strong>
                                                @if ($minute->summary)
                                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($minute->summary, 80) }}</div>
                                                @endif
                                            </td>
                                            <td>{{ optional($minute->meeting_date)->format('d F Y') ?? '-' }}</td>
                                            <td>{{ $minute->location ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $minute->documents_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $minute->images_count }}</span>
                                            </td>
                                            <td>{{ $minute->updated_at->diffForHumans() }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('berita-acara.edit', $minute) }}" class="btn btn-sm btn-warning mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('berita-acara.destroy', $minute) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berita acara ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Belum ada berita acara.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $minutes->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
