@extends('layout.app')
@section('title', 'Manajemen Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAuditee = !$isAdmin && !$isAuditor;
    @endphp

    <div class="main-content">
        <section class="section">
            {{-- ========== HEADER SECTION ========== --}}
            <div class="section-header mb-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <a href="{{ url('/manajemen-risiko') }}" class="btn btn-light btn-sm mr-3 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700; color: #2c3e50;">
                                @if ($isAuditor)
                                    Pemeriksaan Risiko
                                @elseif ($isAuditee)
                                    Pemantauan Risiko
                                @else
                                    Manajemen Risiko
                                @endif
                            </h1>
                            @if ($isAuditor)
                                <small class="text-muted d-block" style="font-size: 0.875rem;">
                                    <i class="fas fa-user-tie mr-1"></i> Auditor: <strong>{{ $user->name }}</strong>
                                    <span class="badge badge-info ml-2">{{ $user->Level->name ?? 'N/A' }}</span>
                                </small>
                            @elseif ($isAuditee)
                                <small class="text-muted d-block" style="font-size: 0.875rem;">
                                    <i class="fas fa-building mr-1"></i> Unit Kerja:
                                    <strong>{{ $user->unitKerja->nama_unit_kerja ?? 'N/A' }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>

                    {{-- Notifikasi Badge --}}
                    @if ($isAuditor && isset($notificationCount) && $notificationCount > 0)
                        <div class="badge badge-danger p-3 shadow-sm" style="font-size: 0.95rem;">
                            <i class="fas fa-bell mr-1"></i> {{ $notificationCount }} Penugasan Baru
                        </div>
                    @endif

                    @if ($isAuditee && isset($statistics['rejected']) && $statistics['rejected'] > 0)
                        <div class="badge badge-warning p-3 shadow-sm" style="font-size: 0.95rem;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $statistics['rejected'] }} Perlu Perbaikan
                        </div>
                    @endif
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

                {{-- ========== FILTER SECTION ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-white">
                            <i class="fas fa-filter mr-2"></i>Filter Data Risiko
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="GET"
                            action="{{ $isAuditee ? route('manajemen-risiko.auditee.index') : ($isAuditor ? route('manajemen-risiko.auditor.index') : route('manajemen-risiko.index')) }}"
                            id="filterForm">
                            <div class="row">
                                @if ($isAdmin)
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                            <i class="fas fa-layer-group mr-1"></i> CLUSTER RISIKO
                                        </label>
                                        <select name="cluster" class="form-control"
                                            onchange="document.getElementById('filterForm').submit()">
                                            <option value="all" {{ $cluster == 'all' ? 'selected' : '' }}>Semua</option>
                                            <option value="high" {{ $cluster == 'high' ? 'selected' : '' }}>Tinggi
                                            </option>
                                            <option value="middle" {{ $cluster == 'middle' ? 'selected' : '' }}>Sedang
                                            </option>
                                            <option value="low" {{ $cluster == 'low' ? 'selected' : '' }}>Rendah
                                            </option>
                                        </select>
                                    </div>
                                @endif

                                <div class="{{ $isAdmin ? 'col-md-2' : ($isAuditee ? 'col-md-3' : 'col-md-3') }} mb-3">
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

                                @if ($isAuditee)
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                            <i class="fas fa-tasks mr-1"></i> PILIH KEGIATAN
                                        </label>
                                        <select name="id_kegiatan" class="form-control"
                                            onchange="document.getElementById('filterForm').submit()">
                                            <option value="all" {{ ($kegiatanId ?? 'all') == 'all' ? 'selected' : '' }}>
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
                                @else
                                    <div class="{{ $isAdmin ? 'col-md-3' : 'col-md-4' }} mb-3">
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
                                @endif

                                @if ($isAdmin)
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                            <i class="fas fa-user-tie mr-1"></i> AUDITOR
                                        </label>
                                        <select name="auditor" class="form-control"
                                            onchange="document.getElementById('filterForm').submit()">
                                            <option value="all" {{ $auditorFilter == 'all' ? 'selected' : '' }}>Semua
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
                                @else
                                    <div class="{{ $isAuditee ? 'col-md-3' : 'col-md-3' }} mb-3">
                                        <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                            <i class="fas fa-clipboard-check mr-1"></i> STATUS
                                            {{ $isAuditee ? 'PEMANTAUAN' : 'PEMERIKSAAN' }}
                                        </label>
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
                                                    Perlu Perbaikan
                                                </option>
                                                <option value="pending"
                                                    {{ ($statusReview ?? '') == 'pending' ? 'selected' : '' }}>
                                                    Menunggu Verifikasi
                                                </option>
                                            @else
                                                <option value="reviewed"
                                                    {{ ($statusReview ?? '') == 'reviewed' ? 'selected' : '' }}>
                                                    Sudah Diperiksa
                                                </option>
                                                <option value="pending"
                                                    {{ ($statusReview ?? '') == 'pending' ? 'selected' : '' }}>
                                                    Belum Diperiksa
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ========== DATA TABLE ========== --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-list-alt mr-2"></i>Daftar Risiko
                            <span class="badge badge-light text-primary ml-2">{{ $petas->total() }} Data</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" width="50" class="text-center border-0">No</th>
                                        <th scope="col" width="12%" class="border-0">Unit Kerja</th>
                                        <th scope="col" width="{{ $isAuditee ? '10%' : '8%' }}"
                                            class="text-center border-0">Kegiatan</th>
                                        <th scope="col" width="{{ $isAuditee ? '8%' : '7%' }}"
                                            class="text-center border-0">Kategori</th>
                                        <th scope="col" width="{{ $isAdmin ? '13%' : ($isAuditee ? '18%' : '15%') }}"
                                            class="border-0">Judul Risiko</th>
                                        @if ($isAdmin)
                                            <th scope="col" width="12%" class="text-center border-0">Auditor</th>
                                        @endif
                                        <th scope="col" width="{{ $isAuditee ? '6%' : '5%' }}"
                                            class="text-center border-0">Skor</th>
                                        <th scope="col" width="8%" class="text-center border-0">Tingkat</th>
                                        <th scope="col" width="10%" class="text-center border-0">Status</th>
                                        <th scope="col" width="{{ $isAuditee ? '12%' : '13%' }}"
                                            class="text-center border-0">Aksi</th>
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
                                        @endphp

                                        <tr>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border">{{ $no++ }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold text-dark">{{ $peta->jenis }}</div>
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
                                                        $risikoTerpilihCache[$unitName] = \App\Models\Peta::where(
                                                            'jenis',
                                                            $unitName,
                                                        )
                                                            ->whereYear('created_at', $tahun)
                                                            ->where('tampil_manajemen_risiko', 1)
                                                            ->count();
                                                    }

                                                    $jumlahRisikoUnit = $risikoCountCache[$unitName];
                                                    $jumlahRisikoTerpilih = $risikoTerpilihCache[$unitName];
                                                @endphp

                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge badge-success" style="font-size: 0.95rem;">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        {{ $jumlahRisikoTerpilih ?? 0 }}
                                                        @if (($jumlahRisikoUnit ?? 0) > 0)
                                                            <small>/{{ $jumlahRisikoUnit }}</small>
                                                        @endif
                                                    </span>
                                                    <small class="text-muted mt-1" style="font-size: 0.75rem;">
                                                        Kegiatan
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary">{{ $peta->kategori }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="text-dark" style="line-height: 1.4;">
                                                    {{ Str::limit($peta->judul, $isAdmin ? 40 : ($isAuditee ? 60 : 50)) }}
                                                    @if ($peta->judul && strlen($peta->judul) > ($isAdmin ? 40 : ($isAuditee ? 60 : 50)))
                                                        <i class="fas fa-info-circle text-info ml-1" data-toggle="tooltip"
                                                            title="{{ $peta->judul }}"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            @if ($isAdmin)
                                                <td class="text-center align-middle">
                                                    @if ($peta->auditor)
                                                        <div class="badge badge-info p-2">
                                                            <i class="fas fa-user mr-1"></i> {{ $peta->auditor->name }}
                                                        </div>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                                            data-target="#assignAuditorModal{{ $peta->id }}">
                                                            <i class="fas fa-user-plus mr-1"></i> Tugaskan
                                                        </button>
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-center align-middle">
                                                <div class="font-weight-bold text-dark" style="font-size: 1.1rem;">
                                                    {{ $skorTotal }}
                                                </div>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $peta->skor_kemungkinan }} × {{ $peta->skor_dampak }}
                                                </small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge {{ $badgeClass }} p-2" style="font-size: 0.85rem;">
                                                    {{ $badgeText }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($isAdmin)
                                                    {{-- Admin: Status bisa diklik untuk lihat detail proses audit --}}
                                                    @if ($peta->status_telaah)
                                                        <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                            class="badge badge-success p-2" style="text-decoration: none;"
                                                            data-toggle="tooltip"
                                                            title="Klik untuk lihat detail proses audit">
                                                            <i class="fas fa-check-circle mr-1"></i> Selesai
                                                        </a>
                                                    @elseif ($peta->koreksiPr == 'rejected')
                                                        <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                            class="badge badge-danger p-2" style="text-decoration: none;"
                                                            data-toggle="tooltip"
                                                            title="Klik untuk lihat detail proses audit">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i> Perlu
                                                            Perbaikan
                                                        </a>
                                                    @elseif ($peta->koreksiPr == 'submitted')
                                                        <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                            class="badge badge-info p-2" style="text-decoration: none;"
                                                            data-toggle="tooltip"
                                                            title="Klik untuk lihat detail proses audit">
                                                            <i class="fas fa-paper-plane mr-1"></i> Menunggu
                                                        </a>
                                                    @else
                                                        <a href="{{ route('manajemen-risiko.show', $peta->id) }}"
                                                            class="badge badge-warning p-2" style="text-decoration: none;"
                                                            data-toggle="tooltip"
                                                            title="Klik untuk lihat detail proses audit">
                                                            <i class="fas fa-clock mr-1"></i> Proses
                                                        </a>
                                                    @endif
                                                @else
                                                    {{-- Auditor & Auditee: Status biasa (tidak clickable) --}}
                                                    @if ($peta->status_telaah)
                                                        <span class="badge badge-success p-2">
                                                            <i class="fas fa-check-circle mr-1"></i> Selesai
                                                        </span>
                                                    @elseif ($peta->koreksiPr == 'rejected')
                                                        <span class="badge badge-danger p-2">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i> Perlu
                                                            Perbaikan
                                                        </span>
                                                    @elseif ($peta->koreksiPr == 'submitted')
                                                        <span class="badge badge-info p-2">
                                                            <i class="fas fa-paper-plane mr-1"></i> Menunggu
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning p-2">
                                                            <i class="fas fa-clock mr-1"></i> Proses
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($isAdmin)
                                                    {{-- Admin: Hanya tombol Ubah Auditor --}}
                                                    <div class="d-flex justify-content-center">
                                                        @if ($peta->auditor)
                                                            <button class="btn btn-sm btn-info" data-toggle="modal"
                                                                data-target="#assignAuditorModal{{ $peta->id }}"
                                                                title="Ubah Auditor">
                                                                <i class="fas fa-user-edit"></i>
                                                            </button>
                                                        @else
                                                            <span class="badge badge-warning p-2" data-toggle="tooltip"
                                                                title="Belum Ditugaskan">
                                                                <i class="fas fa-user-slash"></i> Belum Ditugaskan
                                                            </span>
                                                        @endif
                                                    </div>
                                                @elseif ($isAuditor)
                                                    {{-- Auditor: Tombol Detail + Actions --}}
                                                    <div class="d-flex justify-content-center flex-wrap"
                                                        style="gap: 5px;">
                                                        <a href="{{ route('manajemen-risiko.auditor.show-detail', $peta->id) }}"
                                                            class="btn btn-sm btn-info" data-toggle="tooltip"
                                                            title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if ($peta->koreksiPr == 'submitted' && !$peta->status_telaah)
                                                            <button class="btn btn-sm btn-success" data-toggle="modal"
                                                                data-target="#approveModal{{ $peta->id }}"
                                                                title="Setujui">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" data-toggle="modal"
                                                                data-target="#rejectModal{{ $peta->id }}"
                                                                title="Minta Revisi">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @elseif ($isAuditee)
                                                    {{-- Auditee: Tombol Detail + Status Actions --}}
                                                    <div class="d-flex justify-content-center flex-wrap"
                                                        style="gap: 5px;">

                                                        @if (!$peta->auditor_id)
                                                            {{-- Belum ada auditor yang ditugaskan --}}
                                                            <span class="badge badge-secondary p-2" data-toggle="tooltip"
                                                                title="Belum ada auditor yang ditugaskan">
                                                                <i class="fas fa-user-slash"></i>
                                                            </span>
                                                        @elseif ($peta->koreksiPr == 'rejected')
                                                            {{-- Auditor minta perbaikan (OLD WORKFLOW) --}}
                                                            <a href="{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}"
                                                                class="btn btn-sm btn-warning" data-toggle="tooltip"
                                                                title="Lakukan Perbaikan">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @elseif ($peta->status_konfirmasi_auditor == 'Completed' && $peta->status_konfirmasi_auditee != 'Completed')
                                                            {{-- ✅ NEW WORKFLOW: Auditor sudah selesai, auditee perlu approve --}}
                                                            <a href="{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}"
                                                                class="btn btn-sm btn-success" data-toggle="tooltip"
                                                                title="Konfirmasi Hasil Audit">
                                                                <i class="fas fa-check-double"></i>
                                                            </a>
                                                        @elseif ($peta->status_konfirmasi_auditor == 'Not Completed' && $peta->status_konfirmasi_auditee != 'Completed')
                                                            {{-- ✅ NEW WORKFLOW: Auditor belum selesai, auditee perlu tindak lanjut --}}
                                                            <a href="{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}"
                                                                class="btn btn-sm btn-warning" data-toggle="tooltip"
                                                                title="Submit Tindak Lanjut">
                                                                <i class="fas fa-tasks"></i>
                                                            </a>
                                                        @elseif ($peta->status_konfirmasi_auditee == 'Completed')
                                                            {{-- ✅ NEW WORKFLOW: Auditee sudah selesai konfirmasi --}}
                                                            <a href="{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}"
                                                                class="btn btn-sm btn-success" data-toggle="tooltip"
                                                                title="Anda sudah konfirmasi">
                                                                <i class="fas fa-check-circle"></i>
                                                            </a>
                                                        @elseif ($peta->pengendalian && $peta->mitigasi)
                                                            {{-- ✅ NEW WORKFLOW: Auditor sudah input hasil audit --}}
                                                            <a href="{{ route('manajemen-risiko.auditee.show-detail', $peta->id) }}"
                                                                class="btn btn-sm btn-primary" data-toggle="tooltip"
                                                                title="Lihat Proses Audit">
                                                                <i class="fas fa-tasks"></i>
                                                            </a>
                                                        @else
                                                            {{-- Menunggu input dari Auditor --}}
                                                            <span class="badge badge-info p-2" data-toggle="tooltip"
                                                                title="Menunggu Auditor melakukan pemeriksaan">
                                                                <i class="fas fa-hourglass-half"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $isAdmin ? '10' : '9' }}" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                    <h6>Tidak Ada Data</h6>
                                                    <p class="mb-0">
                                                        @if ($isAuditee)
                                                            Tidak ada data risiko untuk unit kerja Anda
                                                        @elseif ($isAuditor)
                                                            Tidak ada penugasan risiko untuk Anda saat ini
                                                        @else
                                                            Data risiko tidak tersedia untuk filter yang dipilih
                                                        @endif
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($petas->hasPages())
                        <div class="card-footer bg-white border-top-0">
                            {{ $petas->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
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
                            <i class="fas fa-comments"></i> Catatan Auditor - {{ $peta->judul }}
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
                                <i class="fas fa-check-circle"></i> Setujui Penugasan Risiko
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('manajemen-risiko.auditor.approve', $peta->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p><strong>Apakah Anda yakin data penugasan risiko ini sudah sesuai dan memadai?</strong>
                                </p>
                                <hr>
                                <p><strong>Unit Kerja:</strong> {{ $peta->jenis }}</p>
                                <p><strong>Judul Risiko:</strong> {{ $peta->judul }}</p>
                                <div class="form-group">
                                    <label class="font-weight-bold">Catatan Verifikasi (Opsional)</label>
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Tambahkan catatan verifikasi..."></textarea>
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
                                <i class="fas fa-times-circle"></i> Minta Perbaikan Data Risiko
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('manajemen-risiko.auditor.reject', $peta->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p><strong>Apakah Anda yakin data penugasan risiko ini memerlukan perbaikan?</strong></p>
                                <hr>
                                <p><strong>Unit Kerja:</strong> {{ $peta->jenis }}</p>
                                <p><strong>Judul Risiko:</strong> {{ $peta->judul }}</p>
                                <div class="form-group">
                                    <label class="font-weight-bold">Catatan Perbaikan (Wajib Diisi) <span
                                            class="text-danger">*</span></label>
                                    <textarea name="comment" class="form-control" rows="3" required
                                        placeholder="Jelaskan perbaikan yang diperlukan..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Ya, Minta Perbaikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

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
