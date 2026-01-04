@extends('layout.app')
@section('title', 'Manajemen Risiko')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url('/dashboard') }}" class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <h1>Manajemen Risiko</h1>
            </div>

            <div class="section-body">
                <h4 class="mb-3">
                    <span style="color: #6c757d;">Analisis dan</span>
                    <span style="color: #6777ef;">Clustering Risiko</span>
                </h4>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Statistics Cards --}}
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Risiko</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['total'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Risiko Tinggi</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['high_risk'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Risiko Sedang</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['middle_risk'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Risiko Rendah</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['low_risk'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form method="GET" action="{{ route('manajemen-risiko.index') }}" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">CLUSTER RISIKO</label>
                                                <select name="cluster" class="form-control"
                                                    onchange="document.getElementById('filterForm').submit()">
                                                    <option value="all" {{ $cluster == 'all' ? 'selected' : '' }}>Semua
                                                        Risiko</option>
                                                    <option value="high" {{ $cluster == 'high' ? 'selected' : '' }}>Risiko
                                                        Tinggi (EXTREME & HIGH)</option>
                                                    <option value="middle" {{ $cluster == 'middle' ? 'selected' : '' }}>
                                                        Risiko Sedang (MIDDLE)</option>
                                                    <option value="low" {{ $cluster == 'low' ? 'selected' : '' }}>Risiko
                                                        Rendah (LOW)</option>
                                                </select>
                                            </div>
                                        </div>

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

                                        <div class="col-md-4">
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

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold">&nbsp;</label>
                                                <a href="{{ route('manajemen-risiko.export') }}?cluster={{ $cluster }}&tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                                    class="btn btn-success btn-block">
                                                    <i class="fas fa-file-excel"></i> Export Excel
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mt-2">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th scope="col" width="3%">No</th>
                                                <th scope="col" width="12%">Unit</th>
                                                <th scope="col" width="15%">Kegiatan</th>
                                                <th scope="col" width="8%">Kategori</th>
                                                <th scope="col" width="15%">Judul</th>
                                                <th scope="col" width="6%">Skor</th>
                                                <th scope="col" width="10%">Tingkat Risiko</th>
                                                <th scope="col" width="8%">Status</th>
                                                <th scope="col" width="10%">Aksi</th>
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
                                                                {{ Str::limit($peta->kegiatan->judul, 35) }}
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
                                                        {{ Str::limit($peta->judul, 50) }}
                                                        @if ($peta->judul && strlen($peta->judul) > 50)
                                                            <i class="fas fa-info-circle text-info" data-toggle="tooltip"
                                                                title="{{ $peta->judul }}"></i>
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
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Ditelaah
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock"></i> Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                            class="btn btn-sm btn-primary" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if (!$peta->status_telaah)
                                                            <form
                                                                action="{{ route('manajemen-risiko.update-status', $peta->id) }}"
                                                                method="POST" style="display: inline;"
                                                                onsubmit="return confirm('Tandai risiko ini sebagai sudah ditelaah?')">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status_telaah"
                                                                    value="1">
                                                                <button type="submit" class="btn btn-sm btn-success"
                                                                    title="Tandai Selesai">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Auto-hide success alerts
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        });
    </script>
@endpush
