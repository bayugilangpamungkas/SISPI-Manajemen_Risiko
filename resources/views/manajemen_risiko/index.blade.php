@extends('layout.app')
@section('title', 'Manajemen Risiko')

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
                        <h1>Manajemen Risiko</h1>
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

                {{-- STATISTICS CARDS (Admin Only) --}}
                <div class="row mb-3">
                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Ditugaskan</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['assigned_auditor'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-secondary">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Ditelaah</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['reviewed'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FILTER SECTION (Admin Only) --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form method="GET" action="{{ route('manajemen-risiko.index') }}" id="filterForm">
                                    <div class="row">
                                        {{-- Filter Cluster --}}
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">CLUSTER RISIKO</label>
                                                <select name="cluster" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $cluster == 'all' ? 'selected' : '' }}>
                                                        Semua</option>
                                                    <option value="high" {{ $cluster == 'high' ? 'selected' : '' }}>
                                                        Tinggi</option>
                                                    <option value="middle" {{ $cluster == 'middle' ? 'selected' : '' }}>
                                                        Sedang</option>
                                                    <option value="low" {{ $cluster == 'low' ? 'selected' : '' }}>
                                                        Rendah</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Filter Tahun --}}
                                        <div class="col-md-2">
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

                                        {{-- Filter Unit Kerja --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">UNIT KERJA</label>
                                                <select name="unit_kerja" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $unitKerja == 'all' ? 'selected' : '' }}>
                                                        Semua Unit Kerja</option>
                                                    @foreach ($unitKerjas as $uk)
                                                        <option value="{{ $uk->nama_unit_kerja }}"
                                                            {{ $unitKerja == $uk->nama_unit_kerja ? 'selected' : '' }}>
                                                            {{ $uk->nama_unit_kerja }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Filter Auditor --}}
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="font-weight-bold">AUDITOR</label>
                                                <select name="auditor" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $auditorFilter == 'all' ? 'selected' : '' }}>
                                                        Semua</option>
                                                    <option value="unassigned"
                                                        {{ $auditorFilter == 'unassigned' ? 'selected' : '' }}>Belum
                                                        Ditugaskan</option>
                                                    @foreach ($auditors as $auditor)
                                                        <option value="{{ $auditor->id }}"
                                                            {{ $auditorFilter == $auditor->id ? 'selected' : '' }}>
                                                            {{ $auditor->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">&nbsp;</label>
                                                <div class="d-flex">
                                                    <a href="{{ route('manajemen-risiko.export') }}?cluster={{ $cluster }}&tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                                        class="btn btn-success mr-2">
                                                        <i class="fas fa-file-excel"></i> Export
                                                    </a>
                                                    <a href="{{ route('manajemen-risiko.generate-report') }}?unit_kerja={{ $unitKerja }}&tahun={{ $tahun }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-file-alt"></i> Generate Laporan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE (Admin Only) --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mt-2">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th scope="col" width="3%">No</th>
                                                <th scope="col" width="10%">Unit Kerja</th>
                                                <th scope="col" width="12%">Kegiatan</th>
                                                <th scope="col" width="7%">Kategori</th>
                                                <th scope="col" width="13%">Judul Risiko</th>
                                                <th scope="col" width="10%">Auditor</th>
                                                <th scope="col" width="5%">Skor</th>
                                                <th scope="col" width="8%">Tingkat</th>
                                                <th scope="col" width="7%">Status</th>
                                                <th scope="col" width="8%">Komentar</th>
                                                <th scope="col" width="12%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = ($petas->currentPage() - 1) * $petas->perPage() + 1; @endphp
                                            @forelse($petas as $peta)
                                                @php
                                                    $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

                                                    if ($skorTotal >= 20) {
                                                        $badgeClass = 'badge-danger';
                                                        $badgeText = 'Extreme';
                                                    } elseif ($skorTotal >= 15) {
                                                        $badgeClass = 'badge-warning';
                                                        $badgeText = 'High';
                                                    } elseif ($skorTotal >= 10) {
                                                        $badgeClass = 'badge-info';
                                                        $badgeText = 'Moderate';
                                                    } else {
                                                        $badgeClass = 'badge-success';
                                                        $badgeText = 'Low';
                                                    }

                                                    $jumlahKomentar = $peta->comment_prs->count();
                                                @endphp

                                                <tr>
                                                    <td class="text-center">{{ $no++ }}</td>
                                                    <td class="text-center">
                                                        <strong>{{ $peta->jenis }}</strong><br>
                                                        <small class="text-muted">{{ $peta->kode_regist }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($peta->kegiatan)
                                                            <span class="badge badge-primary badge-pill"
                                                                data-toggle="tooltip"
                                                                title="{{ $peta->kegiatan->judul }}">
                                                                {{ Str::limit($peta->kegiatan->judul, 30) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted text-center d-block">
                                                                <small><i class="fas fa-minus-circle"></i> Tidak
                                                                    ada</small>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">{{ $peta->kategori }}</span>
                                                    </td>
                                                    <td>
                                                        {{ Str::limit($peta->judul, 40) }}
                                                        @if ($peta->judul && strlen($peta->judul) > 40)
                                                            <i class="fas fa-info-circle text-info" data-toggle="tooltip"
                                                                title="{{ $peta->judul }}"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($peta->auditor)
                                                            <span class="badge badge-info">
                                                                <i class="fas fa-user"></i> {{ $peta->auditor->name }}
                                                            </span>
                                                        @else
                                                            <button class="btn btn-sm btn-outline-secondary"
                                                                data-toggle="modal"
                                                                data-target="#assignAuditorModal{{ $peta->id }}">
                                                                <i class="fas fa-user-plus"></i> Tugaskan
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <strong style="font-size: 16px;">{{ $skorTotal }}</strong><br>
                                                        <small class="text-muted">{{ $peta->skor_kemungkinan }} ×
                                                            {{ $peta->skor_dampak }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $badgeClass }}" style="font-size: 12px;">
                                                            {{ $badgeText }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($peta->status_telaah)
                                                            <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                                class="badge badge-success"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat detail hasil review">
                                                                <i class="fas fa-check"></i> Selesai
                                                            </a>
                                                        @elseif($peta->koreksiPr == 'rejected')
                                                            <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                                class="badge badge-danger"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat alasan penolakan">
                                                                <i class="fas fa-times"></i> Ditolak
                                                            </a>
                                                        @elseif($peta->koreksiPr == 'submitted')
                                                            <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                                class="badge badge-info"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat detail pengiriman">
                                                                <i class="fas fa-paper-plane"></i> Menunggu
                                                            </a>
                                                        @else
                                                            <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                                class="badge badge-warning"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat detail">
                                                                <i class="fas fa-clock"></i> Pending
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($jumlahKomentar > 0)
                                                            <span class="badge badge-primary badge-pill"
                                                                style="font-size: 13px;" data-toggle="tooltip"
                                                                title="Klik detail untuk melihat {{ $jumlahKomentar }} komentar">
                                                                <i class="fas fa-comments"></i> {{ $jumlahKomentar }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary badge-pill"
                                                                style="font-size: 12px;">
                                                                <i class="fas fa-comment-slash"></i> Tidak ada
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                            class="btn btn-sm btn-primary mb-1" title="Detail">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>

                                                        @if ($peta->auditor)
                                                            <button class="btn btn-sm btn-info mb-1" data-toggle="modal"
                                                                data-target="#assignAuditorModal{{ $peta->id }}"
                                                                title="Ubah Auditor">
                                                                <i class="fas fa-user-edit"></i> Ubah
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-warning mb-1"
                                                                data-toggle="modal"
                                                                data-target="#assignAuditorModal{{ $peta->id }}"
                                                                title="Tugaskan Auditor">
                                                                <i class="fas fa-user-plus"></i> Tugaskan
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Data risiko tidak tersedia untuk filter yang dipilih.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- PAGINATION --}}
                                <div class="mt-3">
                                    {{ $petas->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- MODAL ASSIGN AUDITOR (Admin Only) --}}
    @foreach ($petas as $peta)
        <div class="modal fade" id="assignAuditorModal{{ $peta->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-tie"></i> Tugaskan Auditor
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('manajemen-risiko.assign-auditor', $peta->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Pilih Auditor</label>
                                <select name="auditor_id" class="form-control" required>
                                    <option value="">-- Pilih Auditor --</option>
                                    @foreach ($auditors as $auditor)
                                        <option value="{{ $auditor->id }}"
                                            {{ $peta->auditor_id == $auditor->id ? 'selected' : '' }}>
                                            {{ $auditor->name }} ({{ $auditor->Level->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-info">
                                <strong>Info:</strong> Auditor yang ditugaskan akan melakukan review terhadap risiko ini.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Auto-hide success alerts
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
