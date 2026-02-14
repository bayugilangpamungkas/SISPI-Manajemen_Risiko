@extends('layout.app')
@section('title', 'Manajemen Surat')

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ url('/dashboard') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Manajemen Surat</h1>
                        <small class="text-muted">Kelola surat administratif SPI</small>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- ALERTS --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif


                {{-- FILTER & CREATE BUTTON --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Daftar Surat</h5>
                                    <a href="{{ route('surat.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Buat Surat Baru
                                    </a>
                                </div>

                                <form method="GET" action="{{ route('surat.index') }}" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">JENIS SURAT</label>
                                                <select name="jenis_surat" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $jenisSurat == 'all' ? 'selected' : '' }}>
                                                        Semua
                                                    </option>
                                                    <option value="Pemberitahuan"
                                                        {{ $jenisSurat == 'Pemberitahuan' ? 'selected' : '' }}>
                                                        Pemberitahuan
                                                    </option>
                                                    <option value="Undangan"
                                                        {{ $jenisSurat == 'Undangan' ? 'selected' : '' }}>
                                                        Undangan
                                                    </option>
                                                    <option value="Permohonan"
                                                        {{ $jenisSurat == 'Permohonan' ? 'selected' : '' }}>
                                                        Permohonan
                                                    </option>
                                                    <option value="Lainnya"
                                                        {{ $jenisSurat == 'Lainnya' ? 'selected' : '' }}>
                                                        Lainnya
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">STATUS</label>
                                                <select name="status" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>
                                                        Semua
                                                    </option>
                                                    <option value="Draft" {{ $status == 'Draft' ? 'selected' : '' }}>
                                                        Draft
                                                    </option>
                                                    <option value="Final" {{ $status == 'Final' ? 'selected' : '' }}>
                                                        Final
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">TAHUN</label>
                                                <select name="tahun" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    @foreach ($years as $year)
                                                        <option value="{{ $year }}"
                                                            {{ $tahun == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mt-2">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col" width="3%" class="text-center">No</th>
                                                <th scope="col" width="12%" class="text-center">Nomor Surat</th>
                                                <th scope="col" width="10%" class="text-center">Jenis</th>
                                                <th scope="col" width="15%" class="text-left">Tujuan</th>
                                                <th scope="col" width="20%" class="text-left">Perihal</th>
                                                <th scope="col" width="10%" class="text-center">Tanggal</th>
                                                <th scope="col" width="8%" class="text-center">Status</th>
                                                <th scope="col" width="12%" class="text-center">Referensi</th>
                                                <th scope="col" width="10%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = ($surats->currentPage() - 1) * $surats->perPage() + 1; @endphp
                                            @forelse ($surats as $surat)
                                                <tr>
                                                    <td class="text-center">{{ $no++ }}</td>
                                                    <td class="text-center">
                                                        <strong>{{ $surat->nomor_surat }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">{{ $surat->jenis_surat }}</span>
                                                    </td>
                                                    <td class="text-left">{{ $surat->tujuan_surat }}</td>
                                                    <td class="text-left">
                                                        {{ Str::limit($surat->perihal, 50) }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $surat->tanggal_surat->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($surat->status == 'Draft')
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-edit"></i> Draft
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Final
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">
                                                            {{ $surat->tipe_referensi }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('surat.show', $surat->id) }}"
                                                            class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if ($surat->file_pdf)
                                                            <a href="{{ route('surat.download-pdf', $surat->id) }}"
                                                                class="btn btn-sm btn-success mb-1" title="Download PDF">
                                                                <i class="fas fa-file-pdf"></i>
                                                            </a>
                                                        @endif

                                                        @if ($surat->status == 'Draft')
                                                            <a href="{{ route('surat.edit', $surat->id) }}"
                                                                class="btn btn-sm btn-warning mb-1" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <form action="{{ route('surat.finalize', $surat->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Finalisasi surat ini? Status akan berubah menjadi Final.')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-primary mb-1"
                                                                    title="Finalisasi">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </button>
                                                            </form>

                                                            <form action="{{ route('surat.destroy', $surat->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger mb-1"
                                                                    title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="fas fa-info-circle"></i>
                                                            Belum ada data surat untuk filter yang dipilih.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- PAGINATION --}}
                                <div class="mt-3">
                                    {{ $surats->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
