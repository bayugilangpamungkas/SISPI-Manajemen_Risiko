@extends('layout.app')
@section('title', 'Data Manajemen Risiko')

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- HEADER SECTION --}}
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ url('/dashboard') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1 class="mb-0">Data Manajemen Risiko</h1>
                        <p class="text-muted mb-0">Kelola hasil clustering dan pilih data yang ditampilkan di halaman
                            Manajemen Risiko</p>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- ALERTS --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- STATISTICS CARDS --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card card-statistic-1 shadow-sm">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Data</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['total'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card card-statistic-1 shadow-sm">
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

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card card-statistic-1 shadow-sm">
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

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card card-statistic-1 shadow-sm">
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

                {{-- UPLOAD & CLUSTERING SECTION --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                                    Upload & Clustering Data Risiko
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-cloud-upload-alt text-primary mr-1"></i>
                                            Upload File Excel
                                        </h6>
                                        <p class="text-muted mb-3">
                                            Upload file Excel yang berisi data risiko untuk di-import ke sistem.
                                        </p>

                                        <form id="uploadForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="fileUpload"
                                                        name="file" accept=".xlsx,.xls" required>
                                                    <label class="custom-file-label" for="fileUpload">
                                                        Pilih file Excel...
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted mt-1">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Format: .xlsx, .xls (Maksimal: 5MB)
                                                </small>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-primary" onclick="uploadFile()"
                                                    disabled id="btnUpload">
                                                    <i class="fas fa-upload mr-2"></i> Upload File
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="resetUpload()">
                                                    <i class="fas fa-redo mr-2"></i> Reset
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="runClustering()">
                                                    <i class="fas fa-play mr-2"></i> Jalankan Clustering
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-lg-4 mt-4 mt-lg-0">
                                        <div class="alert alert-info border-0">
                                            <h6 class="font-weight-bold mb-2">
                                                <i class="fas fa-lightbulb mr-1"></i> Panduan
                                            </h6>
                                            <ol class="mb-0 pl-3" style="font-size: 0.875rem;">
                                                <li class="mb-1">Siapkan file Excel dengan data risiko</li>
                                                <li class="mb-1">Pilih file dan klik "Upload File"</li>
                                                <li>Klik "Jalankan Clustering" untuk proses grouping otomatis</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE SECTION --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                                    <i class="fas fa-table mr-2"></i> Data Peta Risiko
                                </h6>
                            </div>
                            {{-- FILTER & DOWNLOAD SECTION --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <form method="GET" action="{{ route('manajemen-risiko.data') }}"
                                                id="filterForm">
                                                <div class="row">
                                                    {{-- Filter Columns --}}
                                                    <div class="col-lg-9 mb-3 mb-lg-0">
                                                        <div class="row">
                                                            <div class="col-md-3 mb-3">
                                                                <label
                                                                    class="form-label font-weight-bold small">TAHUN</label>
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
                                                                <label class="form-label font-weight-bold small">UNIT
                                                                    KERJA</label>
                                                                <select name="unit_kerja" class="form-control"
                                                                    onchange="document.getElementById('filterForm').submit()">
                                                                    <option value="all"
                                                                        {{ $unitKerja == 'all' ? 'selected' : '' }}>
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
                                                                <label
                                                                    class="form-label font-weight-bold small">KEGIATAN</label>
                                                                <select name="id_kegiatan" class="form-control"
                                                                    onchange="document.getElementById('filterForm').submit()">
                                                                    <option value="all"
                                                                        {{ $kegiatanId == 'all' ? 'selected' : '' }}>
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
                                                                <label class="form-label font-weight-bold small">TINGKAT
                                                                    RISIKO</label>
                                                                <select name="cluster" class="form-control"
                                                                    onchange="document.getElementById('filterForm').submit()">
                                                                    <option value="all"
                                                                        {{ $cluster == 'all' ? 'selected' : '' }}>
                                                                        Semua
                                                                    </option>
                                                                    <option value="high"
                                                                        {{ $cluster == 'high' ? 'selected' : '' }}>
                                                                        Tinggi
                                                                    </option>
                                                                    <option value="middle"
                                                                        {{ $cluster == 'middle' ? 'selected' : '' }}>
                                                                        Sedang
                                                                    </option>
                                                                    <option value="low"
                                                                        {{ $cluster == 'low' ? 'selected' : '' }}>
                                                                        Rendah
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Download Template Column --}}
                                                    <div class="col-lg-3 d-md-flex justify-content-md-end">
                                                        <div class="form-group mb-0">
                                                            <label class="form-label font-weight-bold small">DOWNLOAD
                                                                TEMPLATE DISINI!</label>
                                                            <div class="d-grid gap-2">
                                                                <a href="{{ route('manajemen-risiko.template.pdf') }}?tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                                                    class="btn btn-danger" title="Download Template PDF">
                                                                    <i class="fas fa-file-pdf mr-1"></i> Format PDF
                                                                </a>
                                                                <a href="{{ route('manajemen-risiko.template.excel') }}?tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                                                    class="btn btn-success"
                                                                    title="Download Template Excel">
                                                                    <i class="fas fa-file-excel mr-1"></i> Format Excel
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

                            {{-- ACTION BUTTONS SECTION --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body py-3">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 mb-3 mb-lg-0">
                                                    <h6 class="text-dark font-weight-bold mb-2">
                                                        <i class="fas fa-info-circle text-primary mr-1"></i> Petunjuk
                                                        Penggunaan
                                                    </h6>
                                                    <div class="text-muted">
                                                        <p class="mb-1 d-flex align-items-center">
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                            Centang data yang akan ditampilkan di halaman <strong>Manajemen
                                                                Risiko</strong>
                                                        </p>
                                                        <p class="mb-0 d-flex align-items-center">
                                                            <i class="fas fa-check text-success mr-2"></i>
                                                            Gunakan tombol "Pilih Semua" untuk memilih semua data sekaligus
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 d-md-flex justify-content-md-end">
                                                    <div class="form-group mb-0">
                                                        <div class="d-grid gap-2">
                                                            <button type="button" class="btn btn-primary"
                                                                onclick="showSelectedData()">
                                                                <i class="fas fa-eye mr-2"></i> Submit Manajemen
                                                                Risiko
                                                            </button>

                                                            <a href="{{ route('manajemen-risiko.index') }}"
                                                                class="btn btn-dark">
                                                                <i class="fas fa-arrow-right mr-2"></i> ke Manajemen Risiko
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <form id="selectionForm" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="50" class="text-center">
                                                        <input type="checkbox" id="selectAll"
                                                            onclick="toggleSelectAll(this)">
                                                    </th>
                                                    <th width="60" class="text-center">No</th>
                                                    <th>Unit Kerja</th>
                                                    <th width="100" class="text-center">Kode</th>
                                                    {{-- <th width="120" class="text-center">Kegiatan</th> --}} <th width="130" class="text-center">Kegiatan
                                                    </th>

                                                    <th width="120" class="text-center">Kategori</th>
                                                    <th class="text-center"> Judul Risiko</th>
                                                    <th width="90" class="text-center">Skor</th>
                                                    <th width="100" class="text-center">Tingkat</th>
                                                    <th width="80" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = ($petas->currentPage() - 1) * $petas->perPage() + 1;

                                                    // ✅ PERBAIKAN: Hitung semua unit kerja yang unik dari data petas
                                                    $uniqueUnits = collect();
                                                    foreach ($petas as $peta) {
                                                        if ($peta->jenis && !$uniqueUnits->contains($peta->jenis)) {
                                                            $uniqueUnits->push($peta->jenis);
                                                        }
                                                    }

                                                    // ✅ PERBAIKAN: Hitung jumlah kegiatan tampil per unit SEKARANG
                                                    $kegiatanTampilPerUnit = [];
                                                    foreach ($uniqueUnits as $unitName) {
                                                        $unitModel = \App\Models\UnitKerja::where(
                                                            'nama_unit_kerja',
                                                            $unitName,
                                                        )->first();
                                                        if ($unitModel) {
                                                            // Gunakan method dari Model Kegiatan sesuai dengan struktur Anda
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

                                                        // ✅ PERBAIKAN: Kode kegiatan - cek dengan benar
                                                        $kodeUnit = '-';
                                                        if ($peta->kegiatan) {
                                                            // Coba beberapa kemungkinan nama field
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
                                                            // Method 1: Gunakan array yang sudah dihitung
                                                            $jumlahKegiatanTampil =
                                                                $kegiatanTampilPerUnit[$peta->jenis] ?? 0;

                                                            // Method 2: Atau hitung langsung (fallback)
                                                            if (
                                                                $jumlahKegiatanTampil == 0 &&
                                                                isset($kegiatanTampilPerUnit[$peta->jenis])
                                                            ) {
                                                                // Hitung langsung jika tidak ada di array
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

                                                        //    ✅ PERBAIKAN: Hitung jumlah risiko di unit ini (optimasi query)
                                                        $jumlahRisikoUnit = 0;
                                                        $jumlahRisikoTerpilih = 0;
                                                        if ($peta->jenis) {
                                                            //   Gunakan caching atau hitung sekali per unit
                                                            static $risikoCountCache = [];
                                                            static $risikoTerpilihCache = [];

                                                            if (!isset($risikoCountCache[$peta->jenis])) {
                                                                $risikoCountCache[
                                                                    $peta->jenis
                                                                ] = \App\Models\Peta::where('jenis', $peta->jenis)
                                                                    ->whereYear('created_at', $tahun)
                                                                    ->count();
                                                            }
                                                            $jumlahRisikoUnit = $risikoCountCache[$peta->jenis];

                                                            // Hitung jumlah risiko yang sudah terpilih (tampil_manajemen_risiko = 1)
                                                            if (!isset($risikoTerpilihCache[$peta->jenis])) {
                                                                $risikoTerpilihCache[
                                                                    $peta->jenis
                                                                ] = \App\Models\Peta::where('jenis', $peta->jenis)
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
                                                        <td class="text-center align-middle">{{ $no++ }}</td>
                                                        <td class="align-middle">
                                                            <div class="font-weight-medium">{{ $peta->jenis }}</div>
                                                            <small class="text-muted">
                                                                {{ $jumlahRisikoUnit }} risiko
                                                            </small>
                                                            {{-- ✅ DEBUG: Tampilkan info debugging --}}
                                                            @if (config('app.debug'))
                                                                <br>
                                                                <small class="text-danger">
                                                                    Unit ID: {{ $unitKerjaModel->id ?? 'null' }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-secondary">{{ $kodeUnit }}</span>
                                                        </td>
                                                        {{-- <td class="text-center align-middle">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="fas fa-tasks text-primary mr-2"></i>
                                                                    {{-- ✅ INI YANG AKAN BERUBAH SETELAH UPDATE --}}
                                                        {{-- <span class="font-weight-bold"
                                                            style="font-size: 1.1rem; color: #1976d2;">
                                                            {{ $jumlahKegiatanTampil }}
                                                        </span>
                                                        @if ($totalKegiatanUnit > 0)
                                                            <small
                                                                class="text-muted ml-1">/{{ $totalKegiatanUnit }}</small>
                                                        @endif
                                    </div> --}}
                                                        {{-- <small class="text-muted">
                                        @if ($jumlahKegiatanTampil == 0)
                                            <span class="text-danger">Tidak ada kegiatan
                                                ditampilkan</span>
                                        @elseif($jumlahKegiatanTampil == 1)
                                            1 kegiatan ditampilkan
                                        @else
                                            {{ $jumlahKegiatanTampil }} kegiatan ditampilkan
                                        @endif
                                    </small>

                                    {{-- ✅ DEBUG: Tampilkan info perhitungan --}}
                                                        {{-- @if (config('app.debug'))
                                        <br>
                                        <small class="text-info" style="font-size: 10px;">
                                            (Dihitung:
                                            {{ \App\Models\Kegiatan::hitungKegiatanTampil($unitKerjaModel->id ?? 0, $tahun) }})
                                        </small>
                                    @endif --}}
                                                        {{-- </div> 
                            </td>  --}}
                                                        <td class="text-center align-middle">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                                    <span class="font-weight-bold"
                                                                        style="font-size: 1.1rem; color: #28a745;">
                                                                        {{ $jumlahRisikoTerpilih }}
                                                                    </span>
                                                                    @if ($jumlahRisikoUnit > 0)
                                                                        <small
                                                                            class="text-muted ml-1">/{{ $jumlahRisikoUnit }}</small>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted">
                                                                    @if ($jumlahRisikoTerpilih == 0)
                                                                        <span class="text-danger">Belum ada kegiatan
                                                                            dipilih</span>
                                                                    @elseif($jumlahRisikoTerpilih == 1)
                                                                        1 kegiatan dipilih
                                                                    @else
                                                                        {{ $jumlahRisikoTerpilih }} kegiatan dipilih
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-light border text-dark">
                                                                {{ $peta->kategori }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                <span class="text-truncate" style="max-width: 250px;">
                                                                    {{ $peta->judul }}
                                                                </span>
                                                                @if (strlen($peta->judul) > 60)
                                                                    <i class="fas fa-info-circle text-info ml-2"
                                                                        data-toggle="tooltip"
                                                                        title="{{ $peta->judul }}"></i>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="font-weight-bold" style="font-size: 1.1rem;">
                                                                {{ $skorTotal }}
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $peta->skor_kemungkinan }}×{{ $peta->skor_dampak }}
                                                            </small>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-{{ $badge }} px-3 py-2">
                                                                {{ $label }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <a href="{{ route('manajemen-risiko.detail-unit', ['unitKerja' => $peta->jenis, 'tahun' => $tahun]) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                                title="Pilih Kegiatan">
                                                                <i class="fas fa-list-ul mr-1"></i> Pilih Kegiatan
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-4">
                                                            <div class="empty-state">
                                                                <i
                                                                    class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                                                                <h5>Data Tidak Ditemukan</h5>
                                                                <p class="text-muted">
                                                                    Data Peta Risiko belum tersedia untuk filter yang
                                                                    dipilih.
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- PAGINATION --}}
                                    @if ($petas->hasPages())
                                        <div class="card-footer border-top py-3">
                                            <div class="d-flex justify-content-center">
                                                {{ $petas->appends(request()->query())->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 10px;
            border: 1px solid #e3e6f0;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }

        .card-statistic-1 {
            transition: transform 0.2s;
        }

        .card-statistic-1:hover {
            transform: translateY(-2px);
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .table th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-top: none;
            color: #6c757d;
        }

        .table td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .badge {
            font-weight: 500;
            font-size: 0.8rem;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }

        .empty-state {
            padding: 2rem;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
        }

        .form-control:focus {
            border-color: #6777ef;
            box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover',
                placement: 'top'
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);

            // Enable upload button when file is selected
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

            // Update select all checkbox based on individual checkboxes
            $('.data-checkbox').on('change', function() {
                const totalCheckboxes = $('.data-checkbox').length;
                const checkedCheckboxes = $('.data-checkbox:checked').length;

                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
        });

        // =============================================
        // FILE UPLOAD FUNCTIONS
        // =============================================
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

            // Validate file extension
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

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 5MB!',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            // Show confirmation dialog
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

            // Simulate upload progress
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

        // =============================================
        // CLUSTERING FUNCTIONS
        // =============================================
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

            // Simulate clustering progress
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
                // Scroll to table
                $('html, body').animate({
                    scrollTop: $('.card:last').offset().top - 100
                }, 500);
            });
        }

        // =============================================
        // CHECKBOX & SELECTION FUNCTIONS
        // =============================================
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


        // =============================================
        // MODAL FUNCTIONS
        // =============================================

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
                    // ✅ PERBAIKAN: Set form action dengan route yang benar
                    const form = document.getElementById('selectionForm');
                    form.action = '{{ route('manajemen-risiko.update-tampil') }}';
                    form.method = 'POST';

                    // ✅ PERBAIKAN: Tampilkan loading sebelum submit
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

                    // ✅ PERBAIKAN: Submit form
                    form.submit();
                }
            });
        }
    </script>
@endpush
