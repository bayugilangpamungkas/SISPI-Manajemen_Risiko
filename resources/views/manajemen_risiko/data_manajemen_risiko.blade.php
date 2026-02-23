@extends('layout.app')
@section('title', 'Data Manajemen Risiko')

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
                                Data Manajemen Risiko
                            </h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                Kelola hasil clustering dan pilih data yang ditampilkan di halaman Manajemen Risiko
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

                {{-- ========== STATISTICS CARDS ========== --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon text-white">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="stat-number text-white">{{ $statistics['total'] }}</div>
                                        <div class="stat-label text-white">Total Data</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon text-white">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="stat-number text-white">{{ $statistics['high_risk'] }}</div>
                                        <div class="stat-label text-white">Risiko Tinggi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon text-white">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="stat-number text-white">{{ $statistics['middle_risk'] }}</div>
                                        <div class="stat-label text-white">Risiko Sedang</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stat-icon text-white">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="stat-number text-white">{{ $statistics['low_risk'] }}</div>
                                        <div class="stat-label text-white">Risiko Rendah</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== UPLOAD SECTION ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white border-0 py-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-cloud-upload-alt mr-2"></i>Upload & Clustering Data Risiko
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-lg-8 mb-4 mb-lg-0">
                                <h6 class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-file-excel text-success mr-2"></i>Upload File Excel
                                </h6>
                                <p class="text-muted mb-3" style="font-size: 0.875rem;">
                                    Upload file Excel yang berisi data risiko untuk di-import ke sistem.
                                </p>

                                <form id="uploadForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="fileUpload" name="file"
                                                accept=".xlsx,.xls" required>
                                            <label class="custom-file-label" for="fileUpload">
                                                Pilih file Excel...
                                            </label>
                                        </div>
                                        <small class="form-text text-muted mt-2">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Format yang didukung: .xlsx, .xls (Maksimal: 5MB)
                                        </small>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-primary" onclick="uploadFile()" disabled
                                            id="btnUpload">
                                            <i class="fas fa-upload mr-2"></i> Upload File
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetUpload()">
                                            <i class="fas fa-redo mr-2"></i> Reset
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="runClustering()">
                                            <i class="fas fa-play mr-2"></i> Jalankan Clustering
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-4">
                                <div class="alert alert-info border-0 shadow-sm mb-0">
                                    <h6 class="font-weight-bold mb-3">
                                        <i class="fas fa-lightbulb mr-2"></i>Panduan Cepat
                                    </h6>
                                    <ol class="mb-0 pl-3" style="font-size: 0.875rem; line-height: 1.8;">
                                        <li class="mb-2">Siapkan file Excel dengan data risiko</li>
                                        <li class="mb-2">Pilih file dan klik "Upload File"</li>
                                        <li>Klik "Jalankan Clustering" untuk proses grouping otomatis</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== FILTER SECTION ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-white">
                            <i class="fas fa-filter mr-2"></i>Filter Data Risiko
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('manajemen-risiko.data') }}" id="filterForm">
                            <div class="row">
                                <div class="col-lg-9 mb-3 mb-lg-0">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                                <i class="fas fa-calendar-alt mr-1"></i> TAHUN
                                            </label>
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

                                        <div class="col-md-3 mb-3">
                                            <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                                <i class="fas fa-building mr-1"></i> UNIT KERJA
                                            </label>
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

                                        <div class="col-md-3 mb-3">
                                            <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                                <i class="fas fa-tasks mr-1"></i> KEGIATAN
                                            </label>
                                            <select name="id_kegiatan" class="form-control"
                                                onchange="document.getElementById('filterForm').submit()">
                                                <option value="all" {{ $kegiatanId == 'all' ? 'selected' : '' }}>
                                                    Semua Kegiatan
                                                </option>
                                                @foreach ($kegiatans as $kegiatan)
                                                    <option value="{{ $kegiatan->id }}"
                                                        {{ $kegiatanId == $kegiatan->id ? 'selected' : '' }}>
                                                        {{ Str::limit($kegiatan->judul, 35) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                                <i class="fas fa-layer-group mr-1"></i> TINGKAT RISIKO
                                            </label>
                                            <select name="cluster" class="form-control"
                                                onchange="document.getElementById('filterForm').submit()">
                                                <option value="all" {{ $cluster == 'all' ? 'selected' : '' }}>Semua
                                                </option>
                                                <option value="high" {{ $cluster == 'high' ? 'selected' : '' }}>Tinggi
                                                </option>
                                                <option value="middle" {{ $cluster == 'middle' ? 'selected' : '' }}>Sedang
                                                </option>
                                                <option value="low" {{ $cluster == 'low' ? 'selected' : '' }}>Rendah
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-download mr-1"></i> DOWNLOAD TEMPLATE
                                    </label>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('manajemen-risiko.template.pdf') }}?tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                            class="btn btn-danger btn-block">
                                            <i class="fas fa-file-pdf mr-1"></i> Format PDF
                                        </a>
                                        <a href="{{ route('manajemen-risiko.template.excel') }}?tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                            class="btn btn-success btn-block">
                                            <i class="fas fa-file-excel mr-1"></i> Format Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ========== ACTION BUTTONS ========== --}}
                {{-- <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <h6 class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>Petunjuk Penggunaan
                                </h6>
                                <div class="text-muted" style="font-size: 0.875rem;">
                                    <p class="mb-1 d-flex align-items-start">
                                        <i class="fas fa-check text-success mr-2 mt-1"></i>
                                        <span>Centang data yang akan ditampilkan di halaman <strong>Manajemen
                                                Risiko</strong></span>
                                    </p>
                                    <p class="mb-0 d-flex align-items-start">
                                        <i class="fas fa-check text-success mr-2 mt-1"></i>
                                        <span>Klik tombol "Pilih Kegiatan" untuk memilih kegiatan per unit kerja</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="showSelectedData()">
                                        <i class="fas fa-paper-plane mr-2"></i> Submit ke Manajemen Risiko
                                    </button>
                                    <a href="{{ route('manajemen-risiko.index') }}" class="btn btn-dark btn-lg">
                                        <i class="fas fa-arrow-right mr-2"></i> Lihat Halaman Manajemen Risiko
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- ========== DATA TABLE ========== --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-table mr-2"></i>Data Peta Risiko
                            <span class="badge badge-light text-primary ml-2">{{ $petas->total() }} Data</span>
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <form id="selectionForm" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="50" class="text-center border-0">
                                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                                            </th>
                                            <th width="60" class="text-center border-0">No</th>
                                            <th class="border-0">Unit Kerja</th>
                                            <th width="120" class="text-center border-0">Kode Kegiatan</th>
                                            <th width="130" class="text-center border-0">Kegiatan</th>
                                            <th width="120" class="text-center border-0">Kategori</th>
                                            <th class="text-center border-0">Judul Risiko</th>
                                            <th width="90" class="text-center border-0">Skor</th>
                                            <th width="100" class="text-center border-0">Tingkat</th>
                                            <th width="150" class="text-center border-0">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = ($petas->currentPage() - 1) * $petas->perPage() + 1;
                                            $uniqueUnits = collect();
                                            foreach ($petas as $peta) {
                                                if ($peta->jenis && !$uniqueUnits->contains($peta->jenis)) {
                                                    $uniqueUnits->push($peta->jenis);
                                                }
                                            }
                                            $kegiatanTampilPerUnit = [];
                                            foreach ($uniqueUnits as $unitName) {
                                                $unitModel = \App\Models\UnitKerja::where(
                                                    'nama_unit_kerja',
                                                    $unitName,
                                                )->first();
                                                if ($unitModel) {
                                                    $jumlahKegiatanTampil = \App\Models\Kegiatan::hitungKegiatanTampil(
                                                        $unitModel->id,
                                                        $tahun,
                                                    );
                                                    $kegiatanTampilPerUnit[$unitName] = $jumlahKegiatanTampil;
                                                }
                                            }
                                        @endphp

                                        @forelse($petas as $peta)
                                            @php
                                                $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

                                                if ($skorTotal >= 20) {
                                                    $badge = 'danger';
                                                    $label = 'Extreme';
                                                } elseif ($skorTotal >= 15) {
                                                    $badge = 'warning';
                                                    $label = 'High';
                                                } elseif ($skorTotal >= 10) {
                                                    $badge = 'info';
                                                    $label = 'Moderate';
                                                } else {
                                                    $badge = 'success';
                                                    $label = 'Low';
                                                }

                                                $unitKerjaModel = \App\Models\UnitKerja::where(
                                                    'nama_unit_kerja',
                                                    $peta->jenis,
                                                )->first();

                                                $kodeUnit = '-';
                                                if ($peta->kegiatan) {
                                                    if (!empty($peta->kegiatan->id_kegiatan)) {
                                                        $kodeUnit = $peta->kegiatan->id_kegiatan;
                                                    } elseif (!empty($peta->kegiatan->kode)) {
                                                        $kodeUnit = $peta->kegiatan->kode;
                                                    } elseif (!empty($peta->kegiatan->id)) {
                                                        $kodeUnit = $peta->kegiatan->id;
                                                    }
                                                }

                                                $jumlahKegiatanTampil = 0;
                                                $totalKegiatanUnit = 0;

                                                if ($unitKerjaModel) {
                                                    $jumlahKegiatanTampil = $kegiatanTampilPerUnit[$peta->jenis] ?? 0;

                                                    if (
                                                        $jumlahKegiatanTampil == 0 &&
                                                        isset($kegiatanTampilPerUnit[$peta->jenis])
                                                    ) {
                                                        $jumlahKegiatanTampil = \App\Models\Kegiatan::hitungKegiatanTampil(
                                                            $unitKerjaModel->id,
                                                            $tahun,
                                                        );
                                                    }

                                                    $totalKegiatanUnit = \App\Models\Kegiatan::where(
                                                        'id_unit_kerja',
                                                        $unitKerjaModel->id,
                                                    )->count();
                                                }

                                                $jumlahRisikoUnit = 0;
                                                $jumlahRisikoTerpilih = 0;
                                                if ($peta->jenis) {
                                                    static $risikoCountCache = [];
                                                    static $risikoTerpilihCache = [];

                                                    if (!isset($risikoCountCache[$peta->jenis])) {
                                                        $risikoCountCache[$peta->jenis] = \App\Models\Peta::where(
                                                            'jenis',
                                                            $peta->jenis,
                                                        )
                                                            ->whereYear('created_at', $tahun)
                                                            ->count();
                                                    }
                                                    $jumlahRisikoUnit = $risikoCountCache[$peta->jenis];

                                                    if (!isset($risikoTerpilihCache[$peta->jenis])) {
                                                        $risikoTerpilihCache[$peta->jenis] = \App\Models\Peta::where(
                                                            'jenis',
                                                            $peta->jenis,
                                                        )
                                                            ->whereYear('created_at', $tahun)
                                                            ->where('tampil_manajemen_risiko', 1)
                                                            ->count();
                                                    }
                                                    $jumlahRisikoTerpilih = $risikoTerpilihCache[$peta->jenis];
                                                }
                                            @endphp

                                            <tr>
                                                <td class="text-center align-middle">
                                                    <input type="checkbox" name="selected_ids[]"
                                                        value="{{ $peta->id }}" class="data-checkbox">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-light border">{{ $no++ }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark">{{ $peta->jenis }}</div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-secondary">{{ $kodeUnit }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span class="badge badge-success" style="font-size: 0.95rem;">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            {{ $jumlahRisikoTerpilih }}
                                                            @if ($jumlahRisikoUnit > 0)
                                                                <small>/{{ $jumlahRisikoUnit }}</small>
                                                            @endif
                                                        </span>
                                                        <small class="text-muted mt-1" style="font-size: 0.75rem;">
                                                            Kegiatan
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-light border">{{ $peta->kategori }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="text-dark" style="line-height: 1.4;">
                                                        {{ Str::limit($peta->judul, 60) }}
                                                        @if (strlen($peta->judul) > 60)
                                                            <i class="fas fa-info-circle text-info ml-1"
                                                                data-toggle="tooltip" title="{{ $peta->judul }}"></i>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="font-weight-bold text-dark" style="font-size: 1.1rem;">
                                                        {{ $skorTotal }}
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        {{ $peta->skor_kemungkinan }}×{{ $peta->skor_dampak }}
                                                    </small>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-{{ $badge }} p-2"
                                                        style="font-size: 0.85rem;">
                                                        {{ $label }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('manajemen-risiko.detail-unit', ['unitKerja' => $peta->jenis, 'tahun' => $tahun]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-list-ul mr-1"></i> Pilih Kegiatan
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                        <h6>Tidak Ada Data</h6>
                                                        <p class="mb-0">Data Peta Risiko tidak tersedia untuk filter yang
                                                            dipilih</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($petas->hasPages())
                                <div class="card-footer bg-white border-top-0">
                                    {{ $petas->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        /* Statistics Cards */
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Card Styling */
        .card {
            border-radius: 0.5rem;
        }

        /* Table Styling */
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

        /* Badge Styling */
        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Button Styling */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
        }

        /* Form Control */
        .form-control {
            border-radius: 0.375rem;
        }

        /* Gap Utility */
        .gap-2 {
            gap: 0.5rem;
        }

        .d-grid {
            display: grid;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover',
                placement: 'top'
            });

            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);

            $('#fileUpload').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                const $label = $(this).siblings('.custom-file-label');

                if (fileName) {
                    $label.addClass('selected').html(fileName);
                    $('#btnUpload').prop('disabled', false);
                } else {
                    $label.removeClass('selected').html('Pilih file Excel...');
                    $('#btnUpload').prop('disabled', true);
                }
            });

            $('.data-checkbox').on('change', function() {
                const totalCheckboxes = $('.data-checkbox').length;
                const checkedCheckboxes = $('.data-checkbox:checked').length;

                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
        });

        function uploadFile() {
            const fileInput = document.getElementById('fileUpload');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Belum Dipilih',
                    text: 'Silakan pilih file Excel terlebih dahulu!',
                    confirmButtonColor: '#6777ef',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const allowedExtensions = /(\.xlsx|\.xls)$/i;
            if (!allowedExtensions.exec(file.name)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Valid',
                    text: 'Hanya file Excel (.xlsx, .xls) yang diperbolehkan!',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 5MB!',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Upload',
                html: `
                <div class="text-left">
                    <p>Anda akan mengupload file:</p>
                    <div class="alert alert-light">
                        <i class="fas fa-file-excel text-success mr-2"></i>
                        <strong>${file.name}</strong><br>
                        <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    </div>
                    <p class="text-muted small">File akan diproses dan data risiko akan di-import ke sistem.</p>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-upload mr-1"></i> Upload',
                cancelButtonText: 'Batal',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    simulateUpload();
                }
            });
        }

        function simulateUpload() {
            const $btnUpload = $('#btnUpload');
            const originalText = $btnUpload.html();

            $btnUpload.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;

                if (progress >= 100) {
                    clearInterval(interval);

                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Berhasil!',
                            html: `
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p>File berhasil diupload dan diproses.</p>
                                <small class="text-muted">Data risiko telah di-import ke sistem.</small>
                            </div>
                        `,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Lanjutkan'
                        }).then(() => {
                            resetUpload();
                            window.location.reload();
                        });
                    }, 500);
                }
            }, 200);
        }

        function resetUpload() {
            $('#fileUpload').val('');
            $('.custom-file-label').removeClass('selected').html('Pilih file Excel...');
            $('#btnUpload').prop('disabled', true).html('<i class="fas fa-upload mr-1"></i> Upload File');
        }

        function runClustering() {
            Swal.fire({
                title: 'Jalankan Clustering',
                html: `
                <div class="text-left">
                    <h6 class="font-weight-bold mb-3">Konfigurasi Clustering</h6>
                    
                    <div class="alert alert-light border mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-cogs text-primary mr-2"></i>
                            <strong>Metode:</strong> Total Skor Risiko
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            <strong>Jumlah Cluster:</strong> 3 (Tinggi, Sedang, Rendah)
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="text-muted small">
                        <p class="mb-1"><i class="fas fa-info-circle mr-1"></i> Clustering akan mengelompokkan unit kerja berdasarkan tingkat risiko tertinggi.</p>
                        <p class="mb-0"><i class="fas fa-clock mr-1"></i> Proses ini memerlukan waktu beberapa saat.</p>
                    </div>
                </div>
            `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-play mr-1"></i> Mulai Clustering',
                cancelButtonText: 'Batal',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    startClusteringProcess();
                }
            });
        }

        function startClusteringProcess() {
            Swal.fire({
                title: 'Sedang Memproses...',
                html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mb-1">Sedang menjalankan clustering data...</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            `,
                showConfirmButton: false,
                allowOutsideClick: false,
                width: '450px'
            });

            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                $('.progress-bar').css('width', progress + '%');

                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        showClusteringResults();
                    }, 500);
                }
            }, 300);
        }

        function showClusteringResults() {
            Swal.fire({
                icon: 'success',
                title: 'Clustering Berhasil!',
                html: `
                <div class="text-left">
                    <h6 class="font-weight-bold mb-3">Hasil Clustering</h6>
                    
                    <div class="alert alert-danger mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Cluster 1 - Risiko Tinggi</strong>
                            <span class="badge badge-light">15 Unit</span>
                        </div>
                        <small class="mb-0">Unit kerja dengan skor risiko tertinggi</small>
                    </div>
                    
                    <div class="alert alert-warning mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Cluster 2 - Risiko Sedang</strong>
                            <span class="badge badge-light">30 Unit</span>
                        </div>
                        <small class="mb-0">Unit kerja dengan skor risiko sedang</small>
                    </div>
                    
                    <div class="alert alert-success mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Cluster 3 - Risiko Rendah</strong>
                            <span class="badge badge-light">48 Unit</span>
                        </div>
                        <small class="mb-0">Unit kerja dengan skor risiko rendah</small>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="text-muted small">
                        <i class="fas fa-info-circle mr-1"></i>
                        Data hasil clustering dapat dilihat di tabel di bawah.
                    </div>
                </div>
            `,
                confirmButtonColor: '#6777ef',
                confirmButtonText: '<i class="fas fa-table mr-1"></i> Lihat Hasil',
                width: '550px'
            }).then(() => {
                $('html, body').animate({
                    scrollTop: $('.card:last').offset().top - 100
                }, 500);
            });
        }

        function toggleSelectAll(source) {
            $('.data-checkbox').prop('checked', source.checked);
        }

        function selectAll() {
            $('.data-checkbox').prop('checked', true);
            $('#selectAll').prop('checked', true);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Semua data telah dipilih',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        }

        function deselectAll() {
            $('.data-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);

            Swal.fire({
                icon: 'info',
                title: 'Berhasil',
                text: 'Semua pilihan telah dibatalkan',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        }

        function showSelectedData() {
            const selectedCount = $('.data-checkbox:checked').length;

            if (selectedCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak ada data dipilih',
                    text: 'Silakan pilih minimal 1 data untuk ditampilkan di Manajemen Risiko!',
                    confirmButtonColor: '#6777ef',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Submit Manajemen Risiko',
                html: `
                <div class="text-center">
                    <div class="alert alert-light mb-3">
                        <i class="fas fa-paper-plane fa-2x text-primary mb-2"></i>
                        <h5 class="mb-1">Submit ${selectedCount} Data ke Manajemen Risiko</h5>
                        <small class="text-muted">Data yang dipilih akan ditampilkan di halaman Manajemen Risiko dan Anda akan diarahkan ke sana</small>
                    </div>
                    <p class="text-muted small">Apakah Anda yakin ingin melanjutkan?</p>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Submit & Redirect',
                cancelButtonText: 'Batal',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('selectionForm');
                    form.action = '{{ route('manajemen-risiko.update-tampil') }}';
                    form.method = 'POST';

                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Mohon tunggu, sedang menyimpan data...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });
        }
    </script>
@endpush
