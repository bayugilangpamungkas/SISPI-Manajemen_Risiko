@extends('layout.app')
@section('title', 'Manajemen Surat')

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- ========== HEADER SECTION ========== --}}
            <div class="section-header mb-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm mr-3 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700; color: #2c3e50;">
                                Manajemen Surat
                            </h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                Kelola surat administratif SPI Politeknik Negeri Malang
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- ========== ALERTS ========== --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-lg mr-3"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-lg mr-3"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- ========== ACTION BUTTON ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-envelope text-primary mr-2"></i>Kelola Surat Administratif
                            </h6>
                            <a href="{{ route('surat.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus mr-2"></i> Buat Surat Baru
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ========== FILTER SECTION ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-white">
                            <i class="fas fa-filter mr-2"></i>Filter Data Surat
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('surat.index') }}" id="filterForm">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-file-alt mr-1"></i> JENIS SURAT
                                    </label>
                                    <select name="jenis_surat" class="form-control"
                                        onchange="document.getElementById('filterForm').submit()">
                                        <option value="all" {{ $jenisSurat == 'all' ? 'selected' : '' }}>
                                            Semua Jenis
                                        </option>
                                        <option value="Pemberitahuan"
                                            {{ $jenisSurat == 'Pemberitahuan' ? 'selected' : '' }}>
                                            Pemberitahuan
                                        </option>
                                        <option value="Undangan" {{ $jenisSurat == 'Undangan' ? 'selected' : '' }}>
                                            Undangan
                                        </option>
                                        <option value="Permohonan" {{ $jenisSurat == 'Permohonan' ? 'selected' : '' }}>
                                            Permohonan
                                        </option>
                                        <option value="Lainnya" {{ $jenisSurat == 'Lainnya' ? 'selected' : '' }}>
                                            Lainnya
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-clipboard-check mr-1"></i> STATUS SURAT
                                    </label>
                                    <select name="status" class="form-control"
                                        onchange="document.getElementById('filterForm').submit()">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>
                                            Semua Status
                                        </option>
                                        <option value="Draft" {{ $status == 'Draft' ? 'selected' : '' }}>
                                            Draft
                                        </option>
                                        <option value="Final" {{ $status == 'Final' ? 'selected' : '' }}>
                                            Final
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-calendar-alt mr-1"></i> TAHUN
                                    </label>
                                    <select name="tahun" class="form-control"
                                        onchange="document.getElementById('filterForm').submit()">
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ========== DATA TABLE ========== --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-list-alt mr-2"></i>Daftar Surat
                            <span class="badge badge-light text-primary ml-2">{{ $surats->total() }} Surat</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center border-0">No</th>
                                        <th width="140" class="text-center border-0">Nomor Surat</th>
                                        <th width="120" class="text-center border-0">Jenis</th>
                                        <th class="border-0">Tujuan</th>
                                        <th class="border-0">Perihal</th>
                                        <th width="110" class="text-center border-0">Tanggal</th>
                                        <th width="100" class="text-center border-0">Status</th>
                                        <th width="120" class="text-center border-0">Referensi</th>
                                        <th width="200" class="text-center border-0">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($surats->currentPage() - 1) * $surats->perPage() + 1; @endphp
                                    @forelse ($surats as $surat)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border">{{ $no++ }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary p-2">{{ $surat->nomor_surat }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-info p-2">{{ $surat->jenis_surat }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="text-dark" style="line-height: 1.4;">
                                                    {{ $surat->tujuan_surat }}
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="text-dark" style="line-height: 1.4;">
                                                    {{ Str::limit($surat->perihal, 60) }}
                                                    @if (strlen($surat->perihal) > 60)
                                                        <i class="fas fa-info-circle text-info ml-1" data-toggle="tooltip"
                                                            title="{{ $surat->perihal }}"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="font-weight-bold text-dark">
                                                    {{ $surat->tanggal_surat->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($surat->status == 'Draft')
                                                    <span class="badge badge-warning p-2">
                                                        <i class="fas fa-edit mr-1"></i> Draft
                                                    </span>
                                                @else
                                                    <span class="badge badge-success p-2">
                                                        <i class="fas fa-check-circle mr-1"></i> Final
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary p-2">
                                                    {{ $surat->tipe_referensi }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center flex-wrap" style="gap: 5px;">
                                                    <a href="{{ route('surat.show', $surat->id) }}"
                                                        class="btn btn-sm btn-info" data-toggle="tooltip"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    {{-- @if ($surat->file_pdf)
                                                        <a href="{{ route('surat.download-pdf', $surat->id) }}"
                                                            class="btn btn-sm btn-success" data-toggle="tooltip"
                                                            title="Download PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @endif --}}

                                                    @if ($surat->status == 'Draft')
                                                        {{-- <a href="{{ route('surat.edit', $surat->id) }}"
                                                            class="btn btn-sm btn-warning" data-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a> --}}

                                                        <form action="{{ route('surat.finalize', $surat->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Finalisasi surat ini? Status akan berubah menjadi Final.')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-toggle="tooltip" title="Finalisasi">
                                                                <i class="fas fa-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('surat.destroy', $surat->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            data-toggle="tooltip" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                    <h6>Tidak Ada Data</h6>
                                                    <p class="mb-0">Belum ada data surat untuk filter yang dipilih</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($surats->hasPages())
                            <div class="card-footer bg-white border-top-0">
                                {{ $surats->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            border-radius: 0.5rem;
        }

        .table thead th {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .btn-group-vertical .btn {
            border-radius: 0.25rem !important;
            margin-bottom: 0.25rem;
        }

        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
