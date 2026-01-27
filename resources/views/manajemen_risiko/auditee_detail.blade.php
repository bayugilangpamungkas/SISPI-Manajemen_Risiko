@extends('layout.app')
@section('title', 'Detail Penugasan Risiko - Auditee')

@section('main')
    @php
        $user = Auth::user();
        $isAuditee = in_array($user->Level->name ?? '', ['Auditee', 'PIC']);

        // Get Kode Unit
        $unitKerjaModel = \App\Models\UnitKerja::where('nama_unit_kerja', $peta->jenis)->first();
        $kodeUnit = $unitKerjaModel ? $unitKerjaModel->kode_unit : null;

        // Skor Total
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

        // Decode template data dan auditee response
        $templateData = is_string($peta->template_data)
            ? json_decode($peta->template_data, true)
            : $peta->template_data ?? [];
        $auditeeResponse = is_string($peta->auditee_response)
            ? json_decode($peta->auditee_response, true)
            : $peta->auditee_response ?? [];

        // Cek status
        $isRevisi = $peta->koreksiPr == 'rejected';
        $isSubmitted = $peta->koreksiPr == 'submitted';
        $isApproved = $peta->status_telaah == 1;
        $isReadOnly = $isSubmitted || $isApproved;
    @endphp

    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('manajemen-risiko.auditee.index') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Detail Penugasan Risiko</h1>
                        <small class="text-muted">Unit Kerja: {{ $peta->jenis }} ({{ $kodeUnit ?? '-' }})</small>
                    </div>
                </div>
                <span class="badge {{ $badgeClass }}" style="font-size: 16px; padding: 10px 20px;">
                    <i class="fas fa-shield-alt"></i> {{ $badgeText }} - Skor: {{ $skorTotal }}
                </span>
            </div>

            <div class="section-body">
                {{-- ALERTS --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- INFO PANEL: Status --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div
                            class="card border-0 shadow-sm {{ $isApproved ? 'bg-success' : ($isRevisi ? 'bg-warning' : 'bg-light') }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-2 {{ $isApproved || $isRevisi ? 'text-white' : '' }}">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Status Pengerjaan</strong>
                                        </h5>
                                        <div class="d-flex align-items-center flex-wrap">
                                            @if ($isApproved)
                                                <span class="badge badge-light badge-lg mr-2" style="font-size: 14px;">
                                                    <i class="fas fa-check-circle text-success"></i> <strong>Disetujui
                                                        Auditor</strong>
                                                </span>
                                                <small class="text-white">
                                                    <i class="far fa-clock"></i>
                                                    {{ $peta->waktu_telaah_spi ? \Carbon\Carbon::parse($peta->waktu_telaah_spi)->format('d M Y, H:i') : '-' }}
                                                </small>
                                            @elseif($isSubmitted)
                                                <span class="badge badge-info badge-lg" style="font-size: 14px;">
                                                    <i class="fas fa-paper-plane"></i> Menunggu Persetujuan Auditor
                                                </span>
                                            @elseif($isRevisi)
                                                <span class="badge badge-danger badge-lg mr-2" style="font-size: 14px;">
                                                    <i class="fas fa-exclamation-triangle"></i> <strong>Diminta
                                                        Revisi</strong>
                                                </span>
                                                <small class="text-white">Segera perbaiki sesuai catatan Auditor!</small>
                                            @elseif($peta->template_sent_at)
                                                <span class="badge badge-primary badge-lg" style="font-size: 14px;">
                                                    <i class="fas fa-envelope-open-text"></i> Dikirim Auditor - Silakan
                                                    Dikerjakan
                                                </span>
                                            @else
                                                <span class="badge badge-secondary badge-lg" style="font-size: 14px;">
                                                    <i class="fas fa-clock"></i> Menunggu Template dari Auditor
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if ($peta->auditor)
                                            <div class="{{ $isApproved || $isRevisi ? 'text-white' : 'text-muted' }}">
                                                <small><strong>Auditor Penugasan:</strong></small><br>
                                                <strong class="d-block mt-1">
                                                    <i class="fas fa-user-tie"></i> {{ $peta->auditor->name }}
                                                </strong>
                                                <small>{{ $peta->auditor->Level->name ?? 'Auditor' }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ALERT: Catatan Revisi dari Auditor --}}
                @if ($isRevisi)
                    @php
                        $lastRevision = $peta
                            ->comment_prs()
                            ->where('jenis', 'analisis')
                            ->orderBy('created_at', 'desc')
                            ->first();
                    @endphp
                    @if ($lastRevision)
                        <div class="alert alert-danger border-danger shadow-sm">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> Catatan Revisi dari Auditor
                            </h5>
                            <hr>
                            <p class="mb-2"><strong>{{ $lastRevision->user->name }}</strong> -
                                <small class="text-muted">{{ $lastRevision->created_at->format('d M Y, H:i') }}</small>
                            </p>
                            <div class="bg-white p-3 rounded">
                                <p class="mb-0 text-dark">"{{ $lastRevision->comment }}"</p>
                            </div>
                            <hr>
                            <p class="mb-0">
                                <i class="fas fa-info-circle"></i> Mohon perbaiki data sesuai catatan di atas, kemudian
                                kirim ulang ke Auditor.
                            </p>
                        </div>
                    @endif
                @endif

                <div class="row">
                    {{-- COLUMN 1: Data dari Auditor & Form Auditee --}}
                    <div class="col-lg-8">
                        {{-- CARD 1: Informasi Risiko --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="text-white mb-0">
                                    <i class="fas fa-clipboard-list"></i> Informasi Risiko
                                </h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <th width="30%" class="bg-light">Unit Kerja</th>
                                        <td><strong>{{ $peta->jenis }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Kode Unit</th>
                                        <td><span class="badge badge-secondary">{{ $kodeUnit ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Kode Registrasi</th>
                                        <td><span class="badge badge-light">{{ $peta->kode_regist }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Kegiatan</th>
                                        <td>
                                            @if ($peta->kegiatan)
                                                <strong>{{ $peta->kegiatan->judul }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Kategori Risiko</th>
                                        <td><span class="badge badge-dark">{{ $peta->kategori }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Judul Risiko</th>
                                        <td><strong>{{ $peta->judul }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Pernyataan Risiko</th>
                                        <td>{{ $peta->pernyataan ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- CARD 2: Template Penilaian dari Auditor (READ-ONLY) --}}
                        @if (!empty($templateData) && $peta->template_sent_at)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-info text-white">
                                    <h4 class="text-white mb-0">
                                        <i class="fas fa-file-alt"></i> Template Penilaian dari Auditor (Read-Only)
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Informasi:</strong> Data di bawah ini telah diisi oleh
                                        <strong>Auditor</strong>.
                                        Anda dapat melihatnya sebagai referensi untuk mengisi bagian Anda.
                                    </div>

                                    <div class="table-responsive">
                                        @if ($kodeUnit == 'BPKU')
                                            {{-- Tampilan Tabel BPKU --}}
                                            <h5 class="mb-3 text-primary">
                                                <i class="fas fa-award"></i> Penilaian Risiko BPKU
                                            </h5>
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-light">
                                                    <tr class="text-center">
                                                        <th width="5%">No</th>
                                                        <th>Aspek Mutu</th>
                                                        <th>Standar Akreditasi</th>
                                                        <th>Risiko</th>
                                                        <th>Rekomendasi</th>
                                                        <th>Prioritas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($templateData['aspek_mutu'] ?? [] as $index => $item)
                                                        @if (!empty($item))
                                                            <tr>
                                                                <td class="text-center">{{ $index + 1 }}</td>
                                                                <td>{{ $item }}</td>
                                                                <td>{{ $templateData['standar_akreditasi'][$index] ?? '-' }}
                                                                </td>
                                                                <td>{{ $templateData['risiko'][$index] ?? '-' }}</td>
                                                                <td>{{ $templateData['rekomendasi'][$index] ?? '-' }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge badge-{{ $templateData['level_prioritas'][$index] == 'Segera' ? 'danger' : ($templateData['level_prioritas'][$index] == 'Tinggi' ? 'warning' : 'secondary') }}">
                                                                        {{ $templateData['level_prioritas'][$index] ?? '-' }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @elseif ($kodeUnit == 'WADIR1' || $kodeUnit == 'WADIR-1')
                                            {{-- Tampilan Tabel Wadir 1 --}}
                                            <h5 class="mb-3 text-primary">
                                                <i class="fas fa-graduation-cap"></i> Penilaian Risiko Wadir 1 (Akademik)
                                            </h5>
                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-light">
                                                    <tr class="text-center">
                                                        <th width="5%">No</th>
                                                        <th>Aspek Akademik</th>
                                                        <th>Prodi Terdampak</th>
                                                        <th>Risiko</th>
                                                        <th>Dampak</th>
                                                        <th>Strategi Mitigasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($templateData['aspek_akademik'] ?? [] as $index => $item)
                                                        @if (!empty($item))
                                                            <tr>
                                                                <td class="text-center">{{ $index + 1 }}</td>
                                                                <td>{{ $item }}</td>
                                                                <td>{{ $templateData['prodi_terdampak'][$index] ?? '-' }}
                                                                </td>
                                                                <td>{{ $templateData['risiko'][$index] ?? '-' }}</td>
                                                                <td>{{ $templateData['dampak'][$index] ?? '-' }}</td>
                                                                <td>{{ $templateData['strategi_mitigasi'][$index] ?? '-' }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            {{-- Tampilan Tabel Umum --}}
                                            <h5 class="mb-3 text-primary">
                                                <i class="fas fa-clipboard-list"></i> Lembar Monitoring dan Evaluasi
                                                Manajemen Risiko
                                            </h5>
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr class="text-center bg-light">
                                                        <th rowspan="2" width="5%" class="align-middle">No</th>
                                                        <th colspan="2" width="40%" class="bg-success text-white">
                                                            PENGENDALIAN</th>
                                                        <th rowspan="2" width="35%"
                                                            class="align-middle bg-success text-white">MITIGASI RISIKO</th>
                                                        <th rowspan="2" width="20%"
                                                            class="align-middle bg-success text-white">KOMENTAR</th>
                                                    </tr>
                                                    <tr class="text-center bg-light">
                                                        <th width="22%" class="bg-success text-white">Menerima Risiko
                                                        </th>
                                                        <th width="18%" class="bg-success text-white">Status Konfirmasi
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($templateData['menerima_risiko'] ?? [] as $index => $item)
                                                        @if (!empty($item))
                                                            <tr>
                                                                <td class="text-center align-middle">{{ $index + 1 }}
                                                                </td>
                                                                <td class="bg-success-light">{{ $item }}</td>
                                                                <td class="bg-success-light">
                                                                    {{ $templateData['status_konfirmasi'][$index] ?? '-' }}
                                                                </td>
                                                                <td class="bg-success-light">{!! nl2br(e($templateData['mitigasi_risiko'][$index] ?? '-')) !!}</td>
                                                                <td class="bg-success-light">
                                                                    {{ $templateData['komentar'][$index] ?? '-' }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>

                                    {{-- Kesimpulan Auditor --}}
                                    @if (!empty($templateData['kesimpulan']) || !empty($templateData['rekomendasi_tindak_lanjut']))
                                        <hr>
                                        <h6 class="font-weight-bold"><i class="fas fa-clipboard-check"></i> Kesimpulan
                                            Auditor</h6>
                                        @if (!empty($templateData['kesimpulan']))
                                            <div class="mb-2">
                                                <strong>Kesimpulan Umum:</strong>
                                                <p class="bg-light p-2 rounded">{{ $templateData['kesimpulan'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($templateData['rekomendasi_tindak_lanjut']))
                                            <div class="mb-2">
                                                <strong>Rekomendasi Tindak Lanjut:</strong>
                                                <p class="bg-light p-2 rounded">
                                                    {{ $templateData['rekomendasi_tindak_lanjut'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($templateData['catatan_tambahan']))
                                            <div class="alert alert-warning">
                                                <strong><i class="fas fa-sticky-note"></i> Catatan untuk Anda:</strong>
                                                <p class="mb-0">{{ $templateData['catatan_tambahan'] }}</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- CARD 3: Form Input Auditee (Tindak Lanjut) --}}
                        @if ($peta->template_sent_at)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header {{ $isReadOnly ? 'bg-secondary' : 'bg-success' }} text-white">
                                    <h4 class="text-white mb-0">
                                        <i class="fas fa-edit"></i> Form Tindak Lanjut Auditee
                                        @if ($isReadOnly)
                                            <span class="badge badge-light text-dark float-right">
                                                <i class="fas fa-lock"></i> Sudah
                                                {{ $isApproved ? 'Disetujui' : 'Dikirim' }}
                                            </span>
                                        @endif
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @if (!$isReadOnly)
                                        <div class="alert alert-success">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Petunjuk:</strong> Lengkapi form di bawah ini berdasarkan template yang
                                            telah diisi oleh Auditor.
                                            Setelah selesai, klik tombol <strong>"Kirim ke Auditor"</strong>.
                                        </div>
                                    @else
                                        <div class="alert alert-secondary">
                                            <i class="fas fa-lock"></i>
                                            <strong>Status:</strong> Form ini sudah
                                            {{ $isApproved ? 'disetujui dan' : '' }} tidak dapat diubah.
                                            Data Anda telah dikirim ke Auditor.
                                        </div>
                                    @endif

                                    <form action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                        method="POST" enctype="multipart/form-data" id="formAuditee">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label class="font-weight-bold">1. Rencana Tindak Lanjut <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="rencana_tindak_lanjut" class="form-control @error('rencana_tindak_lanjut') is-invalid @enderror"
                                                rows="4" required {{ $isReadOnly ? 'readonly' : '' }}
                                                placeholder="Jelaskan rencana tindak lanjut untuk mengelola risiko ini...">{{ old('rencana_tindak_lanjut', $auditeeResponse['rencana_tindak_lanjut'] ?? '') }}</textarea>
                                            <small class="text-muted">Contoh: Melakukan sosialisasi, pelatihan, perbaikan
                                                SOP, dll.</small>
                                            @error('rencana_tindak_lanjut')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">2. Penanggung Jawab <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="penanggung_jawab"
                                                class="form-control @error('penanggung_jawab') is-invalid @enderror"
                                                required {{ $isReadOnly ? 'readonly' : '' }}
                                                value="{{ old('penanggung_jawab', $auditeeResponse['penanggung_jawab'] ?? '') }}"
                                                placeholder="Nama penanggung jawab pelaksanaan">
                                            <small class="text-muted">Sebutkan nama lengkap dan jabatan penanggung
                                                jawab.</small>
                                            @error('penanggung_jawab')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">3. Target Waktu Mulai <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" name="target_waktu_mulai"
                                                        class="form-control @error('target_waktu_mulai') is-invalid @enderror"
                                                        required {{ $isReadOnly ? 'readonly' : '' }}
                                                        value="{{ old('target_waktu_mulai', $auditeeResponse['target_waktu_mulai'] ?? '') }}">
                                                    @error('target_waktu_mulai')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">4. Target Waktu Selesai <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" name="target_waktu_selesai"
                                                        class="form-control @error('target_waktu_selesai') is-invalid @enderror"
                                                        required {{ $isReadOnly ? 'readonly' : '' }}
                                                        value="{{ old('target_waktu_selesai', $auditeeResponse['target_waktu_selesai'] ?? '') }}">
                                                    @error('target_waktu_selesai')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">5. Anggaran yang Dibutuhkan</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" name="anggaran"
                                                    class="form-control @error('anggaran') is-invalid @enderror"
                                                    {{ $isReadOnly ? 'readonly' : '' }}
                                                    value="{{ old('anggaran', $auditeeResponse['anggaran'] ?? '') }}"
                                                    placeholder="0">
                                            </div>
                                            <small class="text-muted">Kosongkan jika tidak membutuhkan anggaran.</small>
                                            @error('anggaran')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">6. Indikator Keberhasilan <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="indikator_keberhasilan" class="form-control @error('indikator_keberhasilan') is-invalid @enderror"
                                                rows="3" required {{ $isReadOnly ? 'readonly' : '' }}
                                                placeholder="Jelaskan indikator yang digunakan untuk mengukur keberhasilan tindak lanjut...">{{ old('indikator_keberhasilan', $auditeeResponse['indikator_keberhasilan'] ?? '') }}</textarea>
                                            <small class="text-muted">Contoh: Persentase peserta pelatihan, jumlah SOP yang
                                                diperbaiki, dll.</small>
                                            @error('indikator_keberhasilan')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">7. Keterangan Tambahan</label>
                                            <textarea name="keterangan_tambahan" class="form-control @error('keterangan_tambahan') is-invalid @enderror"
                                                rows="3" {{ $isReadOnly ? 'readonly' : '' }} placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan_tambahan', $auditeeResponse['keterangan_tambahan'] ?? '') }}</textarea>
                                            @error('keterangan_tambahan')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if (!$isReadOnly)
                                            <div class="form-group">
                                                <label class="font-weight-bold">8. Bukti Pendukung (Upload File)</label>
                                                <div class="custom-file">
                                                    <input type="file" name="bukti_pendukung"
                                                        class="custom-file-input @error('bukti_pendukung') is-invalid @enderror"
                                                        id="buktiFile"
                                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                                    <label class="custom-file-label" for="buktiFile">Pilih file...</label>
                                                </div>
                                                <small class="text-muted">Format: PDF, Word, Excel, atau Gambar. Maks:
                                                    5MB</small>
                                            </div>
                                        @endif

                                        @if (!empty($auditeeResponse['bukti_pendukung']))
                                            <div class="alert alert-info">
                                                <i class="fas fa-file"></i> <strong>File Terupload:</strong>
                                                <a href="{{ asset('storage/bukti_auditee/' . $auditeeResponse['bukti_pendukung']) }}"
                                                    target="_blank" class="alert-link">
                                                    {{ $auditeeResponse['bukti_pendukung'] }}
                                                </a>
                                            </div>
                                        @endif

                                        <hr class="my-4">

                                        @if (!$isReadOnly)
                                            <div class="d-flex justify-content-between">
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="fas fa-paper-plane"></i> Kirim ke Auditor
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="window.location.href='{{ route('manajemen-risiko.auditee.index') }}'">
                                                    <i class="fas fa-times"></i> Batal
                                                </button>
                                            </div>
                                        @else
                                            <div class="alert alert-success text-center">
                                                <i class="fas fa-check-circle"></i> <strong>Data telah dikirim ke
                                                    Auditor</strong>
                                                <br><small>{{ $peta->koreksiPr_at ? \Carbon\Carbon::parse($peta->koreksiPr_at)->format('d M Y, H:i') : '-' }}</small>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- Jika belum ada template dari Auditor --}}
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-hourglass-half fa-3x mb-3"></i>
                                <h5>Menunggu Template dari Auditor</h5>
                                <p class="mb-0">Template penugasan belum dikirimkan oleh Auditor. Silakan cek kembali
                                    nanti.</p>
                            </div>
                        @endif

                        {{-- CARD 4: Riwayat Komunikasi --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-dark text-white">
                                <h4 class="text-white mb-0">
                                    <i class="fas fa-comments"></i> Riwayat Komunikasi
                                </h4>
                            </div>
                            <div class="card-body">
                                @if ($peta->comment_prs->count() > 0)
                                    <div class="timeline">
                                        @foreach ($peta->comment_prs->sortByDesc('created_at') as $comment)
                                            <div class="alert alert-light border mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div>
                                                        <strong
                                                            class="{{ $comment->user->id == $user->id ? 'text-success' : 'text-primary' }}">
                                                            <i class="fas fa-user-circle"></i>
                                                            {{ $comment->user->id == $user->id ? 'Anda' : $comment->user->name }}
                                                        </strong>
                                                        <span
                                                            class="badge badge-{{ $comment->jenis == 'analisis' ? 'primary' : 'info' }} ml-2">
                                                            {{ ucfirst($comment->jenis) }}
                                                        </span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock"></i>
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                <p class="mb-0">{{ $comment->comment }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-secondary text-center">
                                        <i class="fas fa-info-circle"></i> Belum ada komunikasi untuk risiko ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- COLUMN 2: Sidebar --}}
                    <div class="col-lg-4">
                        {{-- CARD: Skor Risiko --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-danger text-white">
                                <h4 class="text-white mb-0"><i class="fas fa-chart-bar"></i> Skor Risiko</h4>
                            </div>
                            <div class="card-body text-center">
                                <h1
                                    class="display-3 font-weight-bold text-{{ $skorTotal >= 15 ? 'danger' : ($skorTotal >= 10 ? 'warning' : 'success') }}">
                                    {{ $skorTotal }}
                                </h1>
                                <p class="text-muted mb-3">
                                    {{ $peta->skor_kemungkinan }} (Kemungkinan) × {{ $peta->skor_dampak }} (Dampak)
                                </p>
                                <span class="badge {{ $badgeClass }}" style="font-size: 18px; padding: 10px 20px;">
                                    {{ $badgeText }}
                                </span>
                            </div>
                        </div>

                        {{-- CARD: Progress Status --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="text-white mb-0"><i class="fas fa-tasks"></i> Status Progress</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Template Dikirim Auditor
                                        @if ($peta->template_sent_at)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Anda Sudah Mengisi
                                        @if (!empty($auditeeResponse))
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i></span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Dikirim ke Auditor
                                        @if ($isSubmitted || $isApproved)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Disetujui Auditor
                                        @if ($isApproved)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- CARD: Info Auditor --}}
                        @if ($peta->auditor)
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h4 class="text-white mb-0"><i class="fas fa-user-tie"></i> Info Auditor</h4>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-user-circle fa-4x text-info"></i>
                                        <h5 class="mt-2 mb-0">{{ $peta->auditor->name }}</h5>
                                        <small class="text-muted">{{ $peta->auditor->Level->name ?? 'Auditor' }}</small>
                                    </div>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><i class="fas fa-envelope"></i></td>
                                            <td>{{ $peta->auditor->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-calendar"></i></td>
                                            <td>
                                                <small>Ditugaskan: {{ $peta->updated_at->format('d M Y') }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
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
        .bg-success-light {
            background-color: #d4edda !important;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }

        .table thead th {
            vertical-align: middle;
            font-weight: 600;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
        }

        .custom-file-input:disabled~.custom-file-label {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        .alert-light {
            border-left: 4px solid #007bff;
        }

        .timeline {
            position: relative;
        }

        .timeline .alert {
            margin-bottom: 15px;
            padding: 15px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Custom file input label
        document.querySelector('#buktiFile')?.addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
            var label = e.target.nextElementSibling;
            label.textContent = fileName;
        });

        // Form validation before submit
        document.getElementById('formAuditee')?.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Kirim ke Auditor?',
                text: 'Pastikan semua data telah diisi dengan benar',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Kirim!',
                cancelButtonText: 'Cek Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Auto-hide alerts
        setTimeout(() => {
            $('.alert-success, .alert-danger').fadeOut('slow');
        }, 5000);
    </script>
@endpush
