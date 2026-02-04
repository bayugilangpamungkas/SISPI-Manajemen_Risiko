@extends('layout.app')
@section('title', 'Manajemen Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAuditee = !$isAdmin && !$isAuditor; // Role Auditee (Unit Kerja)
    @endphp

    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div
                class="section-header d-flex align-items-center {{ $isAuditor || $isAuditee ? 'justify-content-between' : '' }}">
                <div class="d-flex align-items-center">
                    <a href="{{ url('/manajemen-risiko') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>
                            @if ($isAuditor)
                                Review Risiko
                            @elseif ($isAuditee)
                                Monitoring Risiko
                            @else
                                Manajemen Risiko
                            @endif
                        </h1>
                        @if ($isAuditor)
                            <small class="text-muted">Auditor: {{ $user->name }}
                                ({{ $user->Level->name ?? 'N/A' }})</small>
                        @elseif ($isAuditee)
                            <small class="text-muted">Unit Kerja: {{ $user->unitKerja->nama_unit_kerja ?? 'N/A' }}</small>
                        @endif
                    </div>
                </div>

                {{-- Notifikasi untuk Auditor --}}
                @if ($isAuditor && isset($notificationCount) && $notificationCount > 0)
                    <div class="badge badge-danger badge-lg" style="font-size: 16px; padding: 10px 15px;">
                        <i class="fas fa-bell"></i> {{ $notificationCount }} Risiko Baru
                    </div>
                @endif

                {{-- Notifikasi untuk Auditee (Risiko Ditolak) --}}
                @if ($isAuditee && isset($statistics['rejected']) && $statistics['rejected'] > 0)
                    <div class="badge badge-warning badge-lg" style="font-size: 16px; padding: 10px 15px;">
                        <i class="fas fa-exclamation-triangle"></i> {{ $statistics['rejected'] }} Perlu Revisi
                    </div>
                @endif
            </div>

            <div class="section-body">
                <h4 class="mb-3">
                    <span style="color: #6c757d;">
                        @if ($isAuditor)
                            Review dan Analisis
                        @elseif ($isAuditee)
                            Input dan Monitoring
                        @else
                            Data Risiko
                        @endif
                    </span>
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
                                <form method="GET"
                                    action="{{ $isAuditee ? route('manajemen-risiko.auditee.index') : ($isAuditor ? route('manajemen-risiko.auditor.index') : route('manajemen-risiko.index')) }}"
                                    id="filterForm">
                                    <div class="row">
                                        @if ($isAdmin)
                                            {{-- Filter Cluster (hanya untuk Admin) --}}
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">CLUSTER RISIKO</label>
                                                    <select name="cluster" class="form-control"
                                                        onchange="document.getElementById('filterForm').submit()">
                                                        <option value="all" {{ $cluster == 'all' ? 'selected' : '' }}>
                                                            Semua
                                                        </option>
                                                        <option value="high" {{ $cluster == 'high' ? 'selected' : '' }}>
                                                            Tinggi
                                                        </option>
                                                        <option value="middle"
                                                            {{ $cluster == 'middle' ? 'selected' : '' }}>
                                                            Sedang
                                                        </option>
                                                        <option value="low" {{ $cluster == 'low' ? 'selected' : '' }}>
                                                            Rendah
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="{{ $isAdmin ? 'col-md-2' : ($isAuditee ? 'col-md-3' : 'col-md-3') }}">
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

                                        @if ($isAuditee)
                                            {{-- Filter Kegiatan (hanya untuk Auditee) --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">PILIH KEGIATAN</label>
                                                    <select name="id_kegiatan" class="form-control"
                                                        onchange="document.getElementById('filterForm').submit()">
                                                        <option value="all"
                                                            {{ ($kegiatanId ?? 'all') == 'all' ? 'selected' : '' }}>
                                                            Semua Kegiatan
                                                        </option>
                                                        @isset($kegiatans)
                                                            @foreach ($kegiatans as $kegiatan)
                                                                @if (is_object($kegiatan) || is_array($kegiatan))
                                                                    <option value="{{ $kegiatan->id ?? $kegiatan['id'] }}"
                                                                        {{ ($kegiatanId ?? '') == ($kegiatan->id ?? $kegiatan['id']) ? 'selected' : '' }}>
                                                                        {{ $kegiatan->judul ?? ($kegiatan['judul'] ?? 'Tanpa Judul') }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <div class="{{ $isAdmin ? 'col-md-3' : 'col-md-4' }}">
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
                                        @endif

                                        @if ($isAdmin)
                                            {{-- Filter Auditor (hanya untuk Admin) --}}
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">AUDITOR</label>
                                                    <select name="auditor" class="form-control"
                                                        onchange="document.getElementById('filterForm').submit()">
                                                        <option value="all"
                                                            {{ $auditorFilter == 'all' ? 'selected' : '' }}>
                                                            Semua
                                                        </option>
                                                        <option value="unassigned"
                                                            {{ $auditorFilter == 'unassigned' ? 'selected' : '' }}>
                                                            Belum Ditugaskan
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
                                        @else
                                            {{-- Filter Status Review (untuk Auditor & Auditee) --}}
                                            <div class="{{ $isAuditee ? 'col-md-3' : 'col-md-3' }}">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">STATUS
                                                        {{ $isAuditee ? '' : 'REVIEW' }}</label>
                                                    <select name="status_review" class="form-control"
                                                        onchange="document.getElementById('filterForm').submit()">
                                                        <option value="all"
                                                            {{ ($statusReview ?? 'all') == 'all' ? 'selected' : '' }}>
                                                            Semua
                                                        </option>
                                                        @if ($isAuditee)
                                                            <option value="approved"
                                                                {{ ($statusReview ?? '') == 'approved' ? 'selected' : '' }}>
                                                                Disetujui
                                                            </option>
                                                            <option value="rejected"
                                                                {{ ($statusReview ?? '') == 'rejected' ? 'selected' : '' }}>
                                                                Ditolak / Perlu Revisi
                                                            </option>
                                                            <option value="pending"
                                                                {{ ($statusReview ?? '') == 'pending' ? 'selected' : '' }}>
                                                                Menunggu Review
                                                            </option>
                                                        @else
                                                            <option value="reviewed"
                                                                {{ ($statusReview ?? '') == 'reviewed' ? 'selected' : '' }}>
                                                                Sudah Review
                                                            </option>
                                                            <option value="pending"
                                                                {{ ($statusReview ?? '') == 'pending' ? 'selected' : '' }}>
                                                                Belum Review
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="{{ $isAdmin ? 'col-md-3' : 'col-md-2' }}">
                                            <div class="form-group">
                                                <label class="font-weight-bold">&nbsp;</label>
                                                <div class="d-flex">
                                                    <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.export') : ($isAuditor ? route('manajemen-risiko.auditor.export') : route('manajemen-risiko.export')) }}?cluster={{ $cluster }}&tahun={{ $tahun }}&unit_kerja={{ $unitKerja }}"
                                                        class="btn btn-success d-inline-flex align-items-center justify-content-center mr-2"
                                                        style="height: 38px; padding: 0 15px; font-size: 0.875rem; line-height: 1; {{ $isAdmin ? 'mr-2' : '' }}">
                                                        <i class="fas fa-file-excel"></i> Export
                                                    </a>
                                                    @if ($isAdmin)
                                                        <a href="{{ route('manajemen-risiko.generate-report') }}?unit_kerja={{ $unitKerja }}&tahun={{ $tahun }}"
                                                            class="btn btn-primary btn-block">
                                                            <i class="fas fa-file-alt"></i> Generate Laporan
                                                        </a>
                                                    @endif
                                                </div>
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
                                                <th scope="col" width="10%" class="text-left">Unit Kerja</th>
                                                <th scope="col" width="{{ $isAuditee ? '10%' : '8%' }}"
                                                    class="text-center">
                                                    Kegiatan
                                                </th>
                                                <th scope="col" width="{{ $isAuditee ? '8%' : '7%' }}"
                                                    class="text-center">
                                                    Kategori
                                                </th>
                                                <th scope="col"
                                                    width="{{ $isAdmin ? '13%' : ($isAuditee ? '18%' : '15%') }}"
                                                    class="text-left">
                                                    Judul Risiko
                                                </th>
                                                @if ($isAdmin)
                                                    <th scope="col" width="10%" class="text-center">Auditor</th>
                                                @endif
                                                <th scope="col" width="{{ $isAuditee ? '6%' : '5%' }}"
                                                    class="text-center">
                                                    Skor
                                                </th>
                                                <th scope="col" width="7%" class="text-center">Tingkat</th>
                                                <th scope="col" width="{{ $isAuditee ? '8%' : '7%' }}"
                                                    class="text-center">
                                                    Status
                                                </th>
                                                <th scope="col" width="{{ $isAuditee ? '10%' : '11%' }}"
                                                    class="text-center">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = ($petas->currentPage() - 1) * $petas->perPage() + 1; @endphp
                                            @forelse ($petas as $peta)
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
                                                    <td class="text-left">
                                                        <strong>{{ $peta->jenis }}</strong><br>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        @php
                                                            static $risikoCountCache = [];
                                                            static $risikoTerpilihCache = [];

                                                            $unitName = $peta->jenis;

                                                            if (!isset($risikoCountCache[$unitName])) {
                                                                $risikoCountCache[$unitName] = \App\Models\Peta::where(
                                                                    'jenis',
                                                                    $unitName,
                                                                )
                                                                    ->whereYear('created_at', $tahun)
                                                                    ->count();
                                                            }

                                                            if (!isset($risikoTerpilihCache[$unitName])) {
                                                                $risikoTerpilihCache[
                                                                    $unitName
                                                                ] = \App\Models\Peta::where('jenis', $unitName)
                                                                    ->whereYear('created_at', $tahun)
                                                                    ->where('tampil_manajemen_risiko', 1)
                                                                    ->count();
                                                            }

                                                            $jumlahRisikoUnit = $risikoCountCache[$unitName];
                                                            $jumlahRisikoTerpilih = $risikoTerpilihCache[$unitName];
                                                        @endphp

                                                        <div class="d-flex flex-column align-items-center">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                                <span class="font-weight-bold"
                                                                    style="font-size: 1rem; color: #28a745;">
                                                                    {{ $jumlahRisikoTerpilih }}
                                                                </span>
                                                                @if ($jumlahRisikoUnit > 0)
                                                                    <small class="text-muted ml-1">
                                                                        /{{ $jumlahRisikoUnit }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                                @if ($jumlahRisikoTerpilih == 0)
                                                                    <span class="text-danger">Belum ada</span>
                                                                @else
                                                                    {{ $jumlahRisikoTerpilih }} Kegiatan
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">{{ $peta->kategori }}</span>
                                                    </td>
                                                    <td class="text-left">
                                                        {{ Str::limit($peta->judul, $isAdmin ? 40 : ($isAuditee ? 60 : 50)) }}
                                                        @if ($peta->judul && strlen($peta->judul) > ($isAdmin ? 40 : ($isAuditee ? 60 : 50)))
                                                            <i class="fas fa-info-circle text-info" data-toggle="tooltip"
                                                                title="{{ $peta->judul }}"></i>
                                                        @endif
                                                    </td>
                                                    @if ($isAdmin)
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
                                                    @endif
                                                    <td class="text-center">
                                                        <strong style="font-size: 16px;">{{ $skorTotal }}</strong><br>
                                                        <small class="text-muted">
                                                            {{ $peta->skor_kemungkinan }} × {{ $peta->skor_dampak }}
                                                        </small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $badgeClass }}" style="font-size: 12px;">
                                                            {{ $badgeText }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($peta->status_telaah)
                                                            <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.show-detail', $peta->id) : ($isAuditor ? route('manajemen-risiko.auditor.show-detail', $peta->id) : route('manajemen-risiko.show', $peta->id)) }}"
                                                                class="badge badge-success"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat detail hasil review">
                                                                <i class="fas fa-check"></i> Selesai
                                                            </a>
                                                        @elseif ($peta->koreksiPr == 'rejected')
                                                            <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.show-detail', $peta->id) : ($isAuditor ? route('manajemen-risiko.auditor.show-detail', $peta->id) : route('manajemen-risiko.show', $peta->id)) }}"
                                                                class="badge badge-danger"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat alasan penolakan">
                                                                <i class="fas fa-times"></i> Ditolak
                                                            </a>
                                                        @elseif ($peta->koreksiPr == 'submitted')
                                                            <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.show-detail', $peta->id) : ($isAuditor ? route('manajemen-risiko.auditor.show-detail', $peta->id) : route('manajemen-risiko.show', $peta->id)) }}"
                                                                class="badge badge-info"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat detail pengiriman">
                                                                <i class="fas fa-paper-plane"></i> Menunggu
                                                            </a>
                                                        @else
                                                            <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.show-detail', $peta->id) : ($isAuditor ? route('manajemen-risiko.auditor.show-detail', $peta->id) : route('manajemen-risiko.show', $peta->id)) }}"
                                                                class="badge badge-warning"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                title="Klik untuk melihat detail">
                                                                <i class="fas fa-clock"></i> Pending
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($isAdmin)
                                                            {{-- Admin: Hanya tombol Ubah Auditor --}}
                                                            @if ($peta->auditor)
                                                                <button class="btn btn-sm btn-info mb-1"
                                                                    data-toggle="modal"
                                                                    data-target="#assignAuditorModal{{ $peta->id }}"
                                                                    title="Ubah Auditor">
                                                                    <i class="fas fa-user-edit"></i> Ubah
                                                                </button>
                                                            @else
                                                                <span class="badge badge-secondary">
                                                                    <i class="fas fa-user-slash"></i> Belum ada auditor
                                                                </span>
                                                            @endif
                                                        @else
                                                            {{-- Auditor & Auditee: Tombol Detail tetap ada --}}
                                                            <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.show-detail', $peta->id) : ($isAuditor ? route('manajemen-risiko.auditor.show-detail', $peta->id) : route('manajemen-risiko.show', $peta->id)) }}"
                                                                class="btn btn-sm btn-primary mb-1" title="Detail">
                                                                <i class="fas fa-eye"></i> Detail
                                                            </a>

                                                            @if ($isAuditor)
                                                                {{-- Auditor actions --}}
                                                                @if ($peta->koreksiPr == 'submitted' && !$peta->status_telaah)
                                                                    <button class="btn btn-sm btn-success mb-1"
                                                                        data-toggle="modal"
                                                                        data-target="#approveModal{{ $peta->id }}"
                                                                        title="Setujui">
                                                                        <i class="fas fa-check-circle"></i> ACC
                                                                    </button>
                                                                    <button class="btn btn-sm btn-danger mb-1"
                                                                        data-toggle="modal"
                                                                        data-target="#rejectModal{{ $peta->id }}"
                                                                        title="Tolak">
                                                                        <i class="fas fa-times-circle"></i> Tolak
                                                                    </button>
                                                                @elseif (!$peta->template_sent_at)
                                                                    <span class="badge badge-secondary"
                                                                        title="Isi template terlebih dahulu">
                                                                        <i class="fas fa-info-circle"></i> Belum Kirim
                                                                        Template
                                                                    </span>
                                                                @elseif (!$peta->koreksiPr || $peta->koreksiPr == 'rejected')
                                                                    <span class="badge badge-warning"
                                                                        title="Menunggu Auditee mengerjakan">
                                                                        <i class="fas fa-clock"></i> Menunggu Auditee
                                                                    </span>
                                                                @elseif ($peta->status_telaah)
                                                                    <span class="badge badge-success"
                                                                        title="Sudah disetujui">
                                                                        <i class="fas fa-check"></i> Sudah ACC
                                                                    </span>
                                                                @endif
                                                            @elseif ($isAuditee)
                                                                {{-- Auditee actions --}}
                                                                @if (!$peta->auditor_id)
                                                                    <span class="badge badge-secondary"
                                                                        title="Belum ada auditor">
                                                                        <i class="fas fa-user-slash"></i> Belum Ditugaskan
                                                                    </span>
                                                                @elseif (!$peta->template_sent_at)
                                                                    <span class="badge badge-warning"
                                                                        title="Menunggu template dari Auditor">
                                                                        <i class="fas fa-hourglass-half"></i> Menunggu
                                                                        Template
                                                                    </span>
                                                                @elseif ($peta->status_telaah)
                                                                    <span class="badge badge-success"
                                                                        title="Sudah disetujui Auditor">
                                                                        <i class="fas fa-check-double"></i> Selesai
                                                                    </span>
                                                                @elseif ($peta->koreksiPr == 'submitted')
                                                                    <span class="badge badge-info"
                                                                        title="Menunggu review Auditor">
                                                                        <i class="fas fa-paper-plane"></i> Dikirim
                                                                    </span>
                                                                @elseif ($peta->koreksiPr == 'rejected')
                                                                    <button class="btn btn-sm btn-danger mb-1"
                                                                        onclick="window.location.href='{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}'"
                                                                        title="Perlu revisi">
                                                                        <i class="fas fa-exclamation-triangle"></i>
                                                                        Perbaiki
                                                                    </button>
                                                                @else
                                                                    <button class="btn btn-sm btn-success mb-1"
                                                                        onclick="window.location.href='{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}'"
                                                                        title="Kerjakan tugas">
                                                                        <i class="fas fa-edit"></i> Kerjakan
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ $isAdmin ? '10' : '9' }}" class="text-center">
                                                        <div
                                                            class="alert {{ $isAuditee || $isAuditor ? 'alert-info' : 'alert-warning' }} mb-0">
                                                            <i
                                                                class="fas {{ $isAuditee || $isAuditor ? 'fa-info-circle' : 'fa-exclamation-triangle' }}"></i>
                                                            @if ($isAuditee)
                                                                Tidak ada data risiko untuk unit kerja Anda.
                                                            @elseif ($isAuditor)
                                                                Tidak ada risiko yang ditugaskan kepada Anda.
                                                            @else
                                                                Data risiko tidak tersedia untuk filter yang dipilih.
                                                            @endif
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

    {{-- MODALS --}}
    @foreach ($petas as $peta)
        {{-- MODAL KOMENTAR --}}
        <div class="modal fade" id="modalKomentar{{ $peta->id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-comments"></i> Komentar Auditor - {{ $peta->judul }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group">
                            @forelse ($peta->comment_prs as $comment)
                                <div
                                    class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm border">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-primary">
                                            <b>{{ $comment->user->name ?? 'Auditor' }}</b>
                                        </h6>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-dark">{{ $comment->komentar }}</p>
                                </div>
                            @empty
                                <p class="text-center text-muted">Belum ada rincian komentar.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ADMIN MODAL --}}
        @if ($isAdmin)
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
                                    <strong>Info:</strong> Auditor yang ditugaskan akan melakukan review terhadap risiko
                                    ini.
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
        @endif

        {{-- AUDITOR MODALS --}}
        @if ($isAuditor)
            {{-- APPROVE MODAL --}}
            <div class="modal fade" id="approveModal{{ $peta->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-check-circle"></i> Setujui Risiko
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('manajemen-risiko.auditor.approve', $peta->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p><strong>Apakah Anda yakin data risiko ini sudah sesuai?</strong></p>
                                <hr>
                                <p><strong>Unit:</strong> {{ $peta->jenis }}</p>
                                <p><strong>Judul:</strong> {{ $peta->judul }}</p>
                                <div class="form-group">
                                    <label class="font-weight-bold">Catatan (Opsional)</label>
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Tambahkan catatan review..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Ya, Setujui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- REJECT MODAL --}}
            <div class="modal fade" id="rejectModal{{ $peta->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-times-circle"></i> Tolak Risiko
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('manajemen-risiko.auditor.reject', $peta->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p><strong>Apakah Anda yakin menolak risiko ini?</strong></p>
                                <hr>
                                <p><strong>Unit:</strong> {{ $peta->jenis }}</p>
                                <p><strong>Judul:</strong> {{ $peta->judul }}</p>
                                <div class="form-group">
                                    <label class="font-weight-bold">Alasan Penolakan (Wajib)</label>
                                    <textarea name="comment" class="form-control" rows="3" required placeholder="Berikan alasan penolakan..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Ya, Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
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
