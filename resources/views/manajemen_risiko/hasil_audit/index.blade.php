@extends('layout.app')
@section('title', 'Hasil Audit')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <a href="{{ url('/manajemen-risiko') }}" class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <h1>Hasil Audit Manajemen Risiko</h1>
            </div>

            <div class="section-body">
                <h4 class="mb-3">
                    <span style="color: #6c757d;">Data Hasil Audit</span>
                </h4>

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

                {{-- FILTER SECTION --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form method="GET" action="{{ route('manajemen-risiko.hasil-audit.index') }}"
                                    id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">TAHUN ANGGARAN</label>
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

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">UNIT KERJA</label>
                                                <select name="unit_kerja" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $unitKerja == 'all' ? 'selected' : '' }}>
                                                        Semua Unit Kerja
                                                    </option>
                                                    @foreach ($unitKerjas as $uk)
                                                        <option value="{{ $uk->nama_unit_kerja }}"
                                                            {{ $unitKerja == $uk->nama_unit_kerja ? 'selected' : '' }}>
                                                            {{ $uk->nama_unit_kerja }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">AUDITOR</label>
                                                <select name="auditor" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $auditorFilter == 'all' ? 'selected' : '' }}>
                                                        Semua Auditor
                                                    </option>
                                                    @foreach ($auditors as $auditor)
                                                        <option value="{{ $auditor->id }}"
                                                            {{ $auditorFilter == $auditor->id ? 'selected' : '' }}>
                                                            {{ $auditor->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">&nbsp;</label>
                                                <div class="d-flex">
                                                    <a href="{{ route('manajemen-risiko.hasil-audit.index') }}"
                                                        class="btn btn-secondary btn-block">
                                                        <i class="fas fa-redo"></i> Reset Filter
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

                {{-- STATISTICS --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Total <strong>{{ $hasilAudits->total() }}</strong> hasil audit ditemukan
                            @if ($unitKerja != 'all')
                                untuk unit kerja <strong>{{ $unitKerja }}</strong>
                            @endif
                            pada tahun <strong>{{ $tahun }}</strong>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col" width="3%" class="text-center">No</th>
                                                <th scope="col" width="8%" class="text-center">Kode Risiko</th>
                                                <th scope="col" width="12%" class="text-left">Unit Kerja</th>
                                                <th scope="col" width="20%" class="text-left">Kegiatan</th>
                                                <th scope="col" width="12%" class="text-center">Auditor</th>
                                                <th scope="col" width="8%" class="text-center">Level Risiko</th>
                                                <th scope="col" width="10%" class="text-center">Status Konfirmasi</th>
                                                <th scope="col" width="10%" class="text-center">Tanggal Audit</th>
                                                <th scope="col" width="12%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = ($hasilAudits->currentPage() - 1) * $hasilAudits->perPage() + 1; @endphp
                                            @forelse ($hasilAudits as $hasil)
                                                <tr>
                                                    <td class="text-center">{{ $no++ }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">{{ $hasil->kode_risiko }}</span>
                                                    </td>
                                                    <td class="text-left">{{ $hasil->unit_kerja }}</td>
                                                    <td class="text-left">
                                                        {{ Str::limit($hasil->kegiatan, 50) }}
                                                        @if (strlen($hasil->kegiatan) > 50)
                                                            <i class="fas fa-info-circle text-info" data-toggle="tooltip"
                                                                title="{{ $hasil->kegiatan }}"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ $hasil->nama_pemonev }}</strong><br>
                                                        <small class="text-muted">NIP: {{ $hasil->nip_pemonev ?? '-' }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $levelBadge = match ($hasil->level_risiko) {
                                                                'HIGH' => 'badge-warning',
                                                                'MODERATE' => 'badge-info',
                                                                'LOW' => 'badge-success',
                                                                default => 'badge-secondary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $levelBadge }}">{{ $hasil->level_risiko }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="mb-1">
                                                            <small class="font-weight-bold">Auditee:</small>
                                                            @if ($hasil->status_konfirmasi_auditee)
                                                                <span
                                                                    class="badge badge-sm {{ $hasil->status_konfirmasi_auditee == 'disetujui' ? 'badge-success' : ($hasil->status_konfirmasi_auditee == 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                                                    {{ ucfirst($hasil->status_konfirmasi_auditee) }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-sm badge-secondary">-</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <small class="font-weight-bold">Auditor:</small>
                                                            @if ($hasil->status_konfirmasi_auditor)
                                                                <span
                                                                    class="badge badge-sm {{ $hasil->status_konfirmasi_auditor == 'disetujui' ? 'badge-success' : ($hasil->status_konfirmasi_auditor == 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                                                    {{ ucfirst($hasil->status_konfirmasi_auditor) }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-sm badge-secondary">-</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <small>{{ $hasil->created_at->format('d/m/Y') }}</small><br>
                                                        <small class="text-muted">{{ $hasil->created_at->format('H:i') }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('manajemen-risiko.hasil-audit.show', $hasil->id) }}"
                                                            class="btn btn-sm btn-primary mb-1" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                        <a href="{{ route('manajemen-risiko.hasil-audit.print', $hasil->id) }}"
                                                            class="btn btn-sm btn-danger mb-1" title="Cetak PDF"
                                                            target="_blank">
                                                            <i class="fas fa-file-pdf"></i> Cetak
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Tidak ada data hasil audit untuk filter yang dipilih.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- PAGINATION --}}
                                <div class="mt-3">
                                    {{ $hasilAudits->appends(request()->query())->links('pagination::bootstrap-4') }}
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
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
