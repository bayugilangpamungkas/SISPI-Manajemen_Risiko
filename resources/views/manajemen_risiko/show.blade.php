@extends('layout.app')
@section('title', 'Detail Pemeriksaan Manajemen Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditee = in_array($user->Level->name ?? '', ['Auditee', 'PIC']);

        $statusAudit = $peta->status_audit ?? 'belum_ditugaskan';
        $statusLabel = $peta->status_audit_label ?? 'Belum Ditugaskan';
        $statusBadge = $peta->status_audit_badge ?? 'badge-secondary';
        $viewMode = $viewMode ?? 'read_only';
        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        if ($skorTotal >= 20) {
            $levelText = 'EXTREME';
            $levelBadge = 'bg-danger text-white';
        } elseif ($skorTotal >= 15) {
            $levelText = 'HIGH';
            $levelBadge = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
            $levelBadge = 'bg-info text-white';
        } else {
            $levelText = 'LOW';
            $levelBadge = 'bg-success text-white';
        }

        $questions = $questions ?? [];
        $responses = $responses ?? [];
        $penilaianAuditor = $penilaianAuditor ?? [];
    @endphp

    <div class="main-content">
        <section class="section">

            {{-- ── Header ── --}}
            <div class="section-header position-relative" style="padding-top:10px;padding-bottom:10px;">
                <a href="{{ $isAuditor ? route('manajemen-risiko.auditor.index') : ($isAuditee ? route('manajemen-risiko.auditee.index') : route('manajemen-risiko.index')) }}"
                    class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size:1.3rem"></i>
                </a>
                <div class="w-100 text-center px-5">
                    <h1 class="font-weight-bold mb-0" style="font-size:19px">PEMERIKSAAN MANAJEMEN RISIKO</h1>
                    <div style="font-size:15px;line-height:1.2;">
                        <span class="d-block">SATUAN PENGAWAS INTERNAL</span>
                        <span>POLITEKNIK NEGERI MALANG</span>
                    </div>
                </div>
            </div>

            <div class="section-body mt-4">

                {{-- ── Alert Status ── --}}
                <div class="alert alert-primary border-left-primary mb-4">
                    <div class="d-flex align-items-center">
                        <div>
                            <strong class="text-white">Status Pemeriksaan:</strong>
                            @php
                                $statusPemeriksaanLabel = '';
                                $statusPemeriksaanBadge = 'badge-secondary';
                                $statusPemeriksaanGuide = '';

                                $isRejectedByAuditee = false;
                                $rejectionInfo = null;
                                if ($peta->catatan_revisi) {
                                    try {
                                        $_revData = json_decode($peta->catatan_revisi, true);
                                        if (
                                            isset($_revData['status']) &&
                                            $_revData['status'] === 'rejected_by_auditee'
                                        ) {
                                            $isRejectedByAuditee = true;
                                            $rejectionInfo = $_revData;
                                        }
                                    } catch (\Exception $e) {
                                    }
                                }

                                if ($statusAudit === 'final') {
                                    $statusPemeriksaanLabel = 'Pemeriksaan Selesai';
                                    $statusPemeriksaanBadge = 'badge-dark';
                                    $statusPemeriksaanGuide =
                                        '✅ Pemeriksaan telah selesai dan data telah difinalisasi secara resmi.';
                                } elseif ($statusAudit === 'disetujui_auditee') {
                                    $statusPemeriksaanLabel = 'Menunggu Finalisasi Auditor';
                                    $statusPemeriksaanBadge = 'badge-info';
                                    $statusPemeriksaanGuide = $isAuditor
                                        ? '<strong class="text-white">→ AUDITOR:</strong> Unit Kerja sudah konfirmasi. Silakan finalisasi pemeriksaan.'
                                        : '<strong class="text-white">→ UNIT KERJA:</strong> Menunggu Auditor melakukan finalisasi.';
                                } elseif ($peta->status_konfirmasi_auditor === 'Not Completed') {
                                    if ($peta->status_konfirmasi_auditee) {
                                        $statusPemeriksaanLabel = 'Menunggu ACC Auditor';
                                        $statusPemeriksaanBadge = 'badge-warning';
                                        $statusPemeriksaanGuide = $isAuditor
                                            ? '<strong class="text-white">→ AUDITOR:</strong> Auditee telah mengirim tindak lanjut. Silakan review.'
                                            : '<strong class="text-white">→ UNIT KERJA:</strong> Tindak lanjut Anda sudah dikirim. Menunggu ACC Auditor.';
                                    } else {
                                        $statusPemeriksaanLabel = 'Menunggu Revisi Auditee';
                                        $statusPemeriksaanBadge = 'badge-danger';
                                        $statusPemeriksaanGuide = $isAuditee
                                            ? '<strong class="text-white">→ UNIT KERJA:</strong> Auditor meminta tindak lanjut. Silakan submit revisi.'
                                            : '<strong class="text-white">→ AUDITOR:</strong> Menunggu Auditee mengirim tindak lanjut.';
                                    }
                                } elseif (
                                    $peta->status_konfirmasi_auditor === 'Completed' &&
                                    !$peta->status_konfirmasi_auditee
                                ) {
                                    $statusPemeriksaanLabel = 'Menunggu Konfirmasi Auditee';
                                    $statusPemeriksaanBadge = 'badge-success';
                                    $statusPemeriksaanGuide = $isAuditee
                                        ? '<strong class="text-white">→ UNIT KERJA:</strong> Auditor telah menyelesaikan pemeriksaan. Silakan konfirmasi.'
                                        : '<strong class="text-white">→ AUDITOR:</strong> Menunggu konfirmasi akhir dari Unit Kerja.';
                                } elseif (
                                    $isRejectedByAuditee &&
                                    $peta->auditor_id &&
                                    !$peta->status_konfirmasi_auditor
                                ) {
                                    $statusPemeriksaanLabel = 'Perlu Perbaikan Auditor';
                                    $statusPemeriksaanBadge = 'badge-danger';
                                    if ($isAuditor) {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ AUDITOR:</strong> Unit Kerja menolak hasil audit. Silakan perbaiki dan submit ulang.';
                                    } elseif ($isAuditee) {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ UNIT KERJA:</strong> Penolakan Anda diterima. Menunggu Auditor memperbaiki.';
                                    } else {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ ADMIN:</strong> Audit ditolak Unit Kerja. Menunggu perbaikan Auditor.';
                                    }
                                } elseif ($peta->auditor_id && !$peta->status_konfirmasi_auditor) {
                                    $statusPemeriksaanLabel = 'Menunggu Pemeriksaan Auditor';
                                    $statusPemeriksaanBadge = 'badge-info';
                                    $statusPemeriksaanGuide = $isAuditor
                                        ? '<strong class="text-white">→ AUDITOR:</strong> Silakan input hasil audit untuk Unit Kerja.'
                                        : '<strong class="text-white">→ UNIT KERJA:</strong> Menunggu Auditor melakukan pemeriksaan.';
                                } else {
                                    $statusPemeriksaanLabel = 'Belum Ditugaskan';
                                    $statusPemeriksaanBadge = 'badge-secondary';
                                    $statusPemeriksaanGuide = 'Belum ada auditor yang ditugaskan untuk risiko ini.';
                                }
                            @endphp
                            <span class="badge {{ $statusPemeriksaanBadge }} p-2">{{ $statusPemeriksaanLabel }}</span><br>
                            <small class="text-white">{!! $statusPemeriksaanGuide !!}</small>
                        </div>
                    </div>
                </div>

                {{-- ── Info Risiko ── --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Informasi Risiko</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150">Unit Kerja</th>
                                        <td>: {{ $peta->jenis }}
                                            @php
                                                $namaUserUnitKerja = '-';
                                                if ($peta->jenis) {
                                                    $unitKerjaModel = \App\Models\UnitKerja::where(
                                                        'nama_unit_kerja',
                                                        $peta->jenis,
                                                    )->first();
                                                    if ($unitKerjaModel) {
                                                        $userUnitKerja = \App\Models\User::where(
                                                            'id_unit_kerja',
                                                            $unitKerjaModel->id,
                                                        )
                                                            ->whereHas(
                                                                'Level',
                                                                fn($q) => $q->whereIn('name', ['Auditee', 'PIC']),
                                                            )
                                                            ->first();
                                                        $namaUserUnitKerja =
                                                            $userUnitKerja?->name ??
                                                            (\App\Models\User::where(
                                                                'id_unit_kerja',
                                                                $unitKerjaModel->id,
                                                            )->first()?->name ??
                                                                '-');
                                                    }
                                                }
                                            @endphp
                                            <br><small class="text-muted"><i class="fas fa-user mr-1"></i>PIC: <strong
                                                    class="text-primary">{{ $namaUserUnitKerja }}</strong></small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kegiatan</th>
                                        <td>: {{ $peta->kegiatan->judul ?? $peta->judul }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kode Kegiatan</th>
                                        <td>:
                                            @php
                                                $kodeKegiatan = '-';
                                                if ($peta->kegiatan) {
                                                    $kodeKegiatan =
                                                        $peta->kegiatan->kode_regist ??
                                                        ($peta->kegiatan->id_kegiatan ??
                                                            ($peta->kegiatan->kode ??
                                                                'KEG-' .
                                                                    date('Y') .
                                                                    '-' .
                                                                    str_pad(
                                                                        $peta->kegiatan->id,
                                                                        3,
                                                                        '0',
                                                                        STR_PAD_LEFT,
                                                                    )));
                                                } elseif ($peta->id_kegiatan) {
                                                    $kg = \App\Models\Kegiatan::find($peta->id_kegiatan);
                                                    $kodeKegiatan = $kg
                                                        ? $kg->kode_regist ??
                                                            'KEG-' .
                                                                date('Y') .
                                                                '-' .
                                                                str_pad($kg->id, 3, '0', STR_PAD_LEFT)
                                                        : '-';
                                                }
                                            @endphp
                                            {{ $kodeKegiatan }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Pernyataan Risiko</th>
                                        <td>: {{ $peta->pernyataan ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150">Auditor</th>
                                        <td>: {{ $peta->auditor->name ?? 'Belum ditugaskan' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Level Risiko</th>
                                        <td>: <span class="badge {{ $levelBadge }} p-2">{{ $levelText }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Skor Total</th>
                                        <td>: {{ $skorTotal }} ({{ $peta->skor_kemungkinan }} x
                                            {{ $peta->skor_dampak }})</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Anggaran</th>
                                        <td>: {{ date('Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════════
                     SECTION 1 : AUDITOR — INPUT / EDIT HASIL AUDIT
                ════════════════════════════════════════ --}}
                @if ($isAuditor && $viewMode === 'input_questions')
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body p-4">

                            {{-- Banner penolakan Auditee --}}
                            @php
                                $auditorRejectionNote = null;
                                if ($peta->catatan_revisi) {
                                    try {
                                        $_arData = json_decode($peta->catatan_revisi, true);
                                        if (isset($_arData['status']) && $_arData['status'] === 'rejected_by_auditee') {
                                            $auditorRejectionNote = $_arData;
                                        }
                                    } catch (\Exception $e) {
                                    }
                                }
                            @endphp
                            @if ($auditorRejectionNote)
                                <div class="alert alert-danger mb-4">
                                    <h6 class="font-weight-bold mb-2">
                                        <i class="fas fa-times-circle mr-1"></i> Audit Ditolak — Perlu Perbaikan
                                    </h6>
                                    <small class="d-block mb-1">
                                        <strong>Ditolak oleh:</strong> {{ $auditorRejectionNote['rejected_by'] ?? '-' }}
                                        &nbsp;|&nbsp;
                                        <strong>Waktu:</strong> {{ isset($auditorRejectionNote['rejected_at']) ? date('d M Y, H:i', strtotime($auditorRejectionNote['rejected_at'])) : '-' }}
                                    </small>
                                    <div class="mt-2 p-2 bg-white border rounded text-dark" style="font-size:0.9rem;">
                                        {!! nl2br(e($auditorRejectionNote['catatan_penolakan'] ?? '-')) !!}
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                method="POST" id="formInputAudit">
                                @csrf @method('PUT')
                                <input type="hidden" name="action" value="input_audit_result">

                                <div class="alert alert-info border-0 shadow-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Panduan:</strong> Silakan input hasil pemeriksaan audit untuk Unit Kerja.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-shield-alt text-primary mr-1"></i>Pengendalian Risiko <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea name="pengendalian" class="form-control" rows="5" required
                                            placeholder="Deskripsi pengendalian risiko...">{{ old('pengendalian', $peta->pengendalian) }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-chart-line text-primary mr-1"></i>Strategi Mitigasi <span
                                                class="text-danger">*</span>
                                        </label>
                                        <select name="mitigasi" class="form-control" required>
                                            <option value="">-- Pilih Strategi Mitigasi --</option>
                                            <option value="Accept Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Accept Risk' ? 'selected' : '' }}>
                                                Terima Risiko</option>
                                            <option value="Share Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Share Risk' ? 'selected' : '' }}>
                                                Bagikan Risiko</option>
                                            <option value="Transfer Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Transfer Risk' ? 'selected' : '' }}>
                                                Transfer Risiko</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-2">
                                        <i class="fas fa-comment-alt text-primary mr-1"></i>Komentar Auditor <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea name="komentar_auditor" class="form-control" rows="5" required
                                        placeholder="Komentar, catatan, atau rekomendasi...">{{ old('komentar_auditor', $hasilAudit->komentar_1 ?? '') }}</textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-2">
                                        <i class="fas fa-clipboard-check text-primary mr-1"></i>Status Konfirmasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="status_konfirmasi_auditor" class="form-control" required
                                        id="selectStatusAuditor">
                                        <option value="">-- Pilih Status Konfirmasi --</option>
                                        <option value="Completed"
                                            {{ old('status_konfirmasi_auditor', $peta->status_konfirmasi_auditor) == 'Completed' ? 'selected' : '' }}>
                                            ✅ Selesai (Audit Selesai)</option>
                                        <option value="Not Completed"
                                            {{ old('status_konfirmasi_auditor', $peta->status_konfirmasi_auditor) == 'Not Completed' ? 'selected' : '' }}>
                                            ⚠️ Tindak Lanjut (Perlu Revisi)</option>
                                    </select>
                                </div>

                                <div class="alert alert-warning border-0 shadow-sm" id="alertNotCompleted"
                                    style="display:none;">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>PERHATIAN:</strong> Jika Anda memilih <strong>Tindak Lanjut</strong>, Auditee
                                    akan diminta melakukan revisi.
                                </div>

                                <div class="text-right mt-4 pt-3 border-top">
                                    <a href="{{ route('manajemen-risiko.auditor.index') }}"
                                        class="btn btn-secondary btn-lg px-5 mr-2">
                                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save mr-2"></i> Simpan Hasil Audit
                                    </button>
                                </div>
                            </form>

                        </div>{{-- /card-body --}}
                    </div>{{-- /card --}}

                    {{-- ════════════════════════════════════════
                     SECTION 1b : AUDITOR — REVIEW TINDAK LANJUT AUDITEE
                ════════════════════════════════════════ --}}
                @elseif ($isAuditor && $peta->auditorCanReviewFollowUp())
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 font-weight-bold"><i class="fas fa-tasks mr-2"></i>Review Hasil Tindak Lanjut
                                dari Auditee</h5>
                        </div>
                        <div class="card-body p-4">

                            {{-- Hasil audit sebelumnya (read-only) --}}
                            <div class="card bg-light mb-4 border-0">
                                <div class="card-header bg-secondary text-white py-3">
                                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-history mr-2"></i>Hasil Audit Anda
                                        (Sebelumnya)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold text-dark mb-2"><i
                                                    class="fas fa-shield-alt text-secondary mr-1"></i>Pengendalian
                                                Risiko</label>
                                            <div class="p-3 bg-white border rounded" style="min-height:100px;">
                                                {{ $peta->pengendalian ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold text-dark mb-2"><i
                                                    class="fas fa-chart-line text-secondary mr-1"></i>Mitigasi
                                                Risiko</label>
                                            <div class="p-3 bg-white border rounded">
                                                <span class="badge badge-info p-2"
                                                    style="font-size:14px;">{{ $peta->mitigasi ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark mb-2"><i
                                                class="fas fa-comment-alt text-secondary mr-1"></i>Komentar Auditor</label>
                                        <div class="p-3 bg-white border rounded" style="min-height:80px;">
                                            {!! nl2br(e($hasilAudit->komentar_1 ?? '-')) !!}</div>
                                    </div>
                                    <label class="font-weight-bold text-dark mb-2">Status Konfirmasi Auditor</label>
                                    <div><span class="badge badge-warning p-2" style="font-size:14px;">⚠️ Not Completed
                                            (Perlu Tindak Lanjut)</span></div>
                                </div>
                            </div>

                            {{-- Tindak lanjut dari Auditee --}}
                            @php $revisionNotes = $peta->revision_notes; @endphp
                            <div class="card border-primary mb-4 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-reply mr-2"></i>Tindak Lanjut dari
                                        Auditee</h6>
                                </div>
                                <div class="card-body">
                                    @if ($revisionNotes && isset($revisionNotes['catatan_tindak_lanjut']))
                                        <div class="mb-3">
                                            <label class="font-weight-bold text-dark mb-2"><i
                                                    class="fas fa-edit text-primary mr-1"></i>Catatan Tindak Lanjut</label>
                                            <div class="p-4 bg-light border rounded shadow-sm"
                                                style="min-height:150px;line-height:1.8;">
                                                {!! nl2br(e($revisionNotes['catatan_tindak_lanjut'])) !!}
                                            </div>
                                        </div>
                                        @if (!empty($revisionNotes['link_data_dukung']))
                                            <div class="mb-3">
                                                <label class="font-weight-bold text-dark mb-2"><i
                                                        class="fas fa-link text-primary mr-1"></i>Link Data Dukung</label>
                                                <div class="p-3 bg-light border rounded">
                                                    <a href="{{ $revisionNotes['link_data_dukung'] }}" target="_blank"
                                                        rel="noopener noreferrer" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Link
                                                    </a>
                                                    <small class="text-muted d-block mt-2">URL:
                                                        <code>{{ Str::limit($revisionNotes['link_data_dukung'], 60) }}</code></small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-3">
                                                <label class="font-weight-bold text-muted mb-2"><i
                                                        class="fas fa-link mr-1"></i>Link Data Dukung</label>
                                                <div class="p-3 bg-light border rounded"><small class="text-muted">Auditee
                                                        tidak melampirkan link data dukung.</small></div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-6">
                                                @php
                                                    $statusAuditee =
                                                        $revisionNotes['status_auditee'] ??
                                                        $peta->status_konfirmasi_auditee;
                                                    $badgeAuditee =
                                                        $statusAuditee === 'Completed' ? 'success' : 'warning';
                                                @endphp
                                                <label class="font-weight-bold text-dark mb-2">Status Auditee</label>
                                                <div><span class="badge badge-{{ $badgeAuditee }} p-2"
                                                        style="font-size:14px;">
                                                        {{ $statusAuditee === 'Completed' ? '✅ Completed' : '⏳ Not Completed' }}
                                                    </span></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-weight-bold text-dark mb-2">Waktu Submit</label>
                                                <div><span class="badge badge-secondary p-2" style="font-size:14px;">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ isset($revisionNotes['submitted_at']) ? date('d M Y, H:i', strtotime($revisionNotes['submitted_at'])) : '-' }}
                                                    </span></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info border-0"><i
                                                class="fas fa-info-circle mr-2"></i>Auditee telah submit tindak lanjut.
                                            Silakan review hasilnya.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Keputusan Auditor --}}
                            <div class="card border-success shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 font-weight-bold text-dark"><i
                                            class="fas fa-gavel text-success mr-2"></i>Keputusan Auditor</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                        method="POST" id="formReviewFollowUp">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="approve_follow_up">
                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-dark mb-2"><i
                                                    class="fas fa-balance-scale text-success mr-1"></i>Keputusan <span
                                                    class="text-danger">*</span></label>
                                            <select name="keputusan_auditor" class="form-control" required
                                                id="selectKeputusanAuditor">
                                                <option value="">-- Pilih Keputusan --</option>
                                                <option value="approve">✅ APPROVE (Setujui & Selesai)</option>
                                                <option value="reject">❌ REJECT (Minta Perbaikan Ulang)</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-success border-0 shadow-sm" id="alertApprove"
                                            style="display:none;">
                                            <i class="fas fa-check-circle mr-2"></i><strong>APPROVE:</strong> Status audit
                                            akan berubah menjadi <strong>SELESAI</strong>.
                                        </div>
                                        <div class="alert alert-danger border-0 shadow-sm" id="alertReject"
                                            style="display:none;">
                                            <i class="fas fa-times-circle mr-2"></i><strong>REJECT:</strong> Auditee akan
                                            diminta melakukan perbaikan ulang.
                                        </div>
                                        <div class="text-right mt-4 pt-3 border-top">
                                            <a href="{{ route('manajemen-risiko.auditor.index') }}"
                                                class="btn btn-secondary btn-lg px-5 mr-2">
                                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-success btn-lg px-5">
                                                <i class="fas fa-check mr-2"></i> Submit Keputusan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>{{-- /card-body --}}
                    </div>{{-- /card --}}

                    {{-- ════════════════════════════════════════
                     SECTION 2 : AUDITEE — KONFIRMASI / TINDAK LANJUT
                ════════════════════════════════════════ --}}
                @elseif ($isAuditee)
                    @php
                        $auditorHasSubmitted =
                            ($peta->pengendalian &&
                                $peta->mitigasi &&
                                in_array($peta->status_konfirmasi_auditor, ['Completed', 'Not Completed'])) ||
                            (isset($hasilAudit) && $hasilAudit && $hasilAudit->pengendalian && $hasilAudit->mitigasi);
                    @endphp

                    @if (!$auditorHasSubmitted)
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0 font-weight-bold"><i class="fas fa-hourglass-half mr-2"></i>Status
                                    Pemeriksaan Audit</h5>
                            </div>
                            <div class="card-body text-center py-5">
                                <i class="fas fa-clock text-info" style="font-size:4rem;"></i>
                                <h4 class="text-info font-weight-bold mt-3 mb-3">Menunggu Hasil Pemeriksaan dari Auditor
                                </h4>
                                <p class="text-muted mb-4" style="font-size:1.1rem;">
                                    Auditor <strong>{{ $peta->auditor->name ?? 'yang ditugaskan' }}</strong> sedang
                                    melakukan pemeriksaan.
                                </p>
                                <div class="alert alert-light border shadow-sm">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    Anda akan mendapat akses setelah Auditor menyelesaikan pemeriksaan.
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">

                                <div class="alert alert-success border-0 shadow-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <strong>Auditor telah menyelesaikan pemeriksaan!</strong>
                                    <p class="mb-0 mt-2">Silakan periksa hasil audit dari
                                        <strong>{{ $peta->auditor->name ?? 'Auditor' }}</strong> dan berikan konfirmasi.
                                    </p>
                                </div>

                                {{-- Ringkasan hasil audit (read-only) --}}
                                <div class="card bg-light mb-4 border-0">
                                    <div class="card-header bg-secondary text-white py-3">
                                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-file-alt mr-2"></i>Ringkasan
                                            Hasil Audit</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark mb-2"><i
                                                        class="fas fa-shield-alt text-secondary mr-1"></i>Pengendalian
                                                    Risiko</label>
                                                <div class="p-3 bg-white border rounded"
                                                    style="min-height:100px;line-height:1.6;">
                                                    {{ $peta->pengendalian ?? ($hasilAudit->pengendalian ?? '-') }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark mb-2"><i
                                                        class="fas fa-chart-line text-secondary mr-1"></i>Mitigasi
                                                    Risiko</label>
                                                <div class="p-3 bg-white border rounded">
                                                    @php
                                                        $mitigasi = $peta->mitigasi ?? ($hasilAudit->mitigasi ?? '-');
                                                        $mitigasiBadge = match ($mitigasi) {
                                                            'Accept Risk' => 'success',
                                                            'Share Risk' => 'info',
                                                            'Transfer Risk' => 'warning',
                                                            default => 'secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge badge-{{ $mitigasiBadge }} p-2"
                                                        style="font-size:14px;">{{ $mitigasi }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="font-weight-bold text-dark mb-2"><i
                                                    class="fas fa-comment-alt text-secondary mr-1"></i>Komentar
                                                Auditor</label>
                                            <div class="p-3 bg-white border rounded shadow-sm"
                                                style="min-height:100px;line-height:1.6;">{!! nl2br(e($hasilAudit->komentar_1 ?? '-')) !!}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-dark mb-2">Status Auditor</label>
                                                @php
                                                    $stAud = $peta->status_konfirmasi_auditor ?? '-';
                                                    $stAudBadge = match ($stAud) {
                                                        'Completed' => 'success',
                                                        'Not Completed' => 'warning',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <div class="mt-2"><span class="badge badge-{{ $stAudBadge }} p-2"
                                                        style="font-size:14px;">
                                                        @if ($stAud == 'Completed')
                                                            ✅ Completed
                                                        @elseif($stAud == 'Not Completed')
                                                            ⚠️ Not Completed
                                                        @else
                                                            {{ $stAud }}
                                                        @endif
                                                    </span></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-dark mb-2">Level Risiko</label>
                                                <div class="mt-2"><span class="badge {{ $levelBadge }} p-2"
                                                        style="font-size:14px;">{{ $hasilAudit->level_risiko ?? $levelText }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-dark mb-2">Skor Total</label>
                                                <div class="mt-2"><span class="badge badge-secondary p-2"
                                                        style="font-size:14px;">{{ $hasilAudit->skor_total ?? $skorTotal }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Aksi Auditee berdasarkan status Auditor --}}
                                @if ($peta->status_konfirmasi_auditor == 'Completed')
                                    <div class="card border-success shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h6 class="mb-0 font-weight-bold text-dark"><i
                                                    class="fas fa-check-circle text-success mr-2"></i>Konfirmasi Akhir Unit
                                                Kerja</h6>
                                        </div>
                                        <div class="card-body">
                                            @if ($statusAudit !== 'final' && !$peta->status_konfirmasi_auditee)
                                                <div class="alert alert-success border-0 shadow-sm mb-4">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Auditor menyatakan audit <strong>SELESAI</strong>. Silakan pilih
                                                    tindakan:
                                                </div>

                                                <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                                    <button type="button" class="btn btn-danger btn-lg px-5 mr-2"
                                                        data-toggle="modal" data-target="#modalTolakAudit">
                                                        <i class="fas fa-times-circle mr-2"></i> Tolak Audit
                                                    </button>
                                                    <form
                                                        action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menyetujui hasil audit ini?')">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="action" value="final_approval">
                                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                                            <i class="fas fa-check-double mr-2"></i> ACC Audit
                                                        </button>
                                                    </form>
                                                </div>

                                                <small class="text-muted d-block mt-3">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    <strong>ACC</strong>: Audit disetujui & dilanjutkan ke finalisasi.
                                                    &nbsp;|&nbsp;
                                                    <strong>Tolak</strong>: Meminta Auditor memperbaiki hasil audit.
                                                </small>
                                            @else
                                                <div class="alert alert-success border-0 shadow-sm text-center">
                                                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                                    <strong>Pemeriksaan Selesai!</strong>
                                                    <p class="mb-0 mt-2">Anda telah menyelesaikan konfirmasi untuk audit
                                                        ini.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($peta->status_konfirmasi_auditor == 'Not Completed')
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-header bg-primary text-white py-3">
                                            <h6 class="mb-0 font-weight-bold"><i
                                                    class="fas fa-exclamation-triangle mr-2"></i>Tindak Lanjut Diperlukan
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-warning border-0 shadow-sm mb-4">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Auditor meminta <strong>TINDAK LANJUT</strong>. Silakan isi form di bawah.
                                            </div>
                                            <form
                                                action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="action" value="submit_follow_up">
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-dark mb-2"><i
                                                            class="fas fa-edit text-primary mr-1"></i>Catatan Tindak Lanjut
                                                        <span class="text-danger">*</span></label>
                                                    <textarea name="catatan_tindak_lanjut" class="form-control" rows="6" required
                                                        placeholder="Jelaskan tindak lanjut yang telah/akan dilakukan..."></textarea>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-dark mb-2"><i
                                                            class="fas fa-link text-primary mr-1"></i>Link Data Dukung
                                                        (Opsional)</label>
                                                    <input type="url" name="link_data_dukung" class="form-control"
                                                        placeholder="https://drive.google.com/...">
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-dark mb-2"><i
                                                            class="fas fa-clipboard-check text-primary mr-1"></i>Status
                                                        Konfirmasi Auditee <span class="text-danger">*</span></label>
                                                    <select name="status_konfirmasi_auditee" class="form-control"
                                                        required>
                                                        <option value="">-- Pilih Status --</option>
                                                        <option value="Completed">✅ Completed (Tindak Lanjut Selesai)
                                                        </option>
                                                        <option value="Not Completed">⏳ Not Completed (Masih Dalam Proses)
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="text-right mt-4 pt-3 border-top">
                                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                                        <i class="fas fa-paper-plane mr-2"></i> Submit Tindak Lanjut
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning border-0 shadow-sm">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Status Tidak Lengkap.</strong>
                                        <p class="mb-0 mt-2">Auditor belum menentukan status konfirmasi. Silakan hubungi
                                            Auditor.</p>
                                    </div>
                                @endif

                            </div>{{-- /card-body --}}
                        </div>{{-- /card --}}
                    @endif

                    {{-- ════════════════════════════════════════
                     SECTION 3 : ADMIN / READ-ONLY
                ════════════════════════════════════════ --}}
                @else
                    <div class="card mb-4">
                        <div class="card-body">
                            @if ($peta->pengendalian || $peta->mitigasi || (isset($hasilAudit) && $hasilAudit))
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">Pengendalian Risiko</label>
                                                <div class="p-3 bg-white border rounded">
                                                    {{ $peta->pengendalian ?? ($hasilAudit->pengendalian ?? '-') }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">Mitigasi Risiko</label>
                                                <div class="p-3 bg-white border rounded">
                                                    {{ $peta->mitigasi ?? ($hasilAudit->mitigasi ?? '-') }}</div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="font-weight-bold text-dark">Komentar Auditor</label>
                                            <div class="p-3 bg-white border rounded shadow-sm"
                                                style="min-height:100px;line-height:1.6;">{!! nl2br(e($hasilAudit->komentar_1 ?? '-')) !!}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Status Auditor</label>
                                                <div class="mt-2"><span
                                                        class="badge badge-secondary p-2">{{ $peta->status_konfirmasi_auditor ?? '-' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Status Auditee</label>
                                                <div class="mt-2"><span
                                                        class="badge badge-secondary p-2">{{ $peta->status_konfirmasi_auditee ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada hasil
                                    pemeriksaan audit yang diinput.</div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ════════════════════════════════════════
                     TOMBOL CETAK (ADMIN — AUDIT FINAL)
                ════════════════════════════════════════ --}}
                @if ($isAdmin && $statusAudit === 'final')
                    <div class="card border-success mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-print"></i> Cetak Dokumen Hasil Audit</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-white">
                                <i class="fas fa-check-circle"></i> <strong>Audit Telah Final!</strong>
                                <p class="mb-0 mt-2">Klik tombol di bawah untuk mencetak dokumen hasil audit.</p>
                            </div>
                            <div class="row">
                                {{-- ✅ Kartu PDF — struktur div diperbaiki --}}
                                <div class="col-md-6 mb-3">
                                    <div class="card border-danger h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf text-danger mb-3" style="font-size:3rem;"></i>
                                            <h5 class="card-title">Cetak Format PDF</h5>
                                            <p class="card-text text-muted">Format resmi untuk arsip dan dokumentasi audit
                                            </p>
                                            <button onclick="cetakPDF({{ $peta->id }})"
                                                class="btn btn-danger btn-lg btn-block">
                                                <i class="fas fa-print mr-2"></i> Cetak PDF
                                            </button>
                                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i>
                                                Template: Lembar Monitoring Manajemen Risiko</small>
                                        </div>
                                    </div>
                                </div>
                                {{-- ✅ Kartu Excel — sekarang berada di dalam .row --}}
                                <div class="col-md-6 mb-3">
                                    <div class="card border-success h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-excel text-success mb-3" style="font-size:3rem;"></i>
                                            <h5 class="card-title">Cetak Format Excel</h5>
                                            <p class="card-text text-muted">Format untuk analisis dan pengolahan data lebih
                                                lanjut</p>
                                            <button onclick="cetakExcel({{ $peta->id }})"
                                                class="btn btn-success btn-lg btn-block">
                                                <i class="fas fa-print mr-2"></i> Cetak Excel
                                            </button>
                                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i>
                                                Template: Buku template.xlsx</small>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- /.row --}}
                        </div>{{-- /.card-body --}}
                    </div>{{-- /.card --}}
                @endif

                {{-- ════════════════════════════════════════
                     TOMBOL FINALISASI (AUDITOR)
                ════════════════════════════════════════ --}}
                @if ($isAuditor && $peta->canBeFinalized())
                    <div class="card border-success mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-lock"></i> Finalisasi Pemeriksaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert">
                                <i class="fas fa-check-circle"></i> <strong>Pemeriksaan siap difinalisasi!</strong>
                                <p class="mb-2 mt-2">Unit Kerja telah mengkonfirmasi hasil pemeriksaan.</p>
                                <ul class="mb-0">
                                    <li>Status: <span class="badge badge-info p-2">{{ $statusLabel }}</span></li>
                                    <li>Unit Kerja: <strong>{{ $peta->jenis }}</strong></li>
                                    <li>Auditor: <strong>{{ $peta->auditor->name ?? '-' }}</strong></li>
                                    <li>Kode: {{ $kodeKegiatan ?? '-' }}</li>
                                </ul>
                            </div>
                            <form action="{{ route('manajemen-risiko.finalisasi', $peta->id) }}" method="POST"
                                id="formFinalisasi">
                                @csrf
                                <div class="text-center">
                                    <button type="button" class="btn btn-success btn-lg px-5" data-toggle="modal"
                                        data-target="#modalKonfirmasiFinalisasi">
                                        <i class="fas fa-lock"></i> Finalisasi Pemeriksaan Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- ════════════════════════════════════════
                     RIWAYAT AKTIVITAS
                ════════════════════════════════════════ --}}
                @if ($peta->comment_prs->count() > 0)
                    <div class="card mb-4 mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Aktivitas Pemeriksaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline-wrapper">
                                @foreach ($peta->comment_prs->sortByDesc('created_at') as $index => $comment)
                                    <div class="timeline-item {{ $index === 0 ? 'timeline-item-latest' : '' }}">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 font-weight-bold text-primary">
                                                            {{ $comment->user->name ?? 'System' }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $comment->created_at->format('d M Y, H:i') }}
                                                            <span
                                                                class="ml-2">({{ $comment->created_at->diffForHumans() }})</span>
                                                        </small>
                                                    </div>
                                                    @php
                                                        $jenisBadge = 'secondary';
                                                        $jenisIcon = 'comment';
                                                        $jenisLabel = ucfirst($comment->jenis ?? 'Aktivitas');
                                                        if ($comment->jenis === 'analisis') {
                                                            $jenisBadge = 'info';
                                                            $jenisIcon = 'chart-line';
                                                            $jenisLabel = 'Analisis Audit';
                                                        } elseif ($comment->jenis === 'keuangan') {
                                                            $jenisBadge = 'success';
                                                            $jenisIcon = 'dollar-sign';
                                                            $jenisLabel = 'Keuangan';
                                                        } elseif ($comment->jenis === 'mitigasi') {
                                                            $jenisBadge = 'warning';
                                                            $jenisIcon = 'shield-alt';
                                                            $jenisLabel = 'Mitigasi';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $jenisBadge }} px-3 py-2">
                                                        <i
                                                            class="fas fa-{{ $jenisIcon }} mr-1"></i>{{ $jenisLabel }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="timeline-body mt-3">
                                                <div class="alert alert-light mb-0 border-left-{{ $jenisBadge }}">
                                                    <p class="mb-0" style="line-height:1.6;">{{ $comment->comment }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Badge info status akhir --}}
                @if (($isAdmin || $isAuditor) && $statusAudit === 'disetujui_auditee')
                    <div class="alert alert-primary mt-4 text-white">
                        <i class="fas fa-info-circle"></i>
                        @if ($isAuditor)
                            <strong>Status:</strong> Unit Kerja telah mengkonfirmasi. Silakan <strong>finalisasi
                                pemeriksaan</strong>.
                        @else
                            <strong>Status:</strong> Menunggu Auditor melakukan <strong>finalisasi pemeriksaan</strong>.
                        @endif
                    </div>
                @endif

                @if ($statusAudit === 'final')
                    <div class="alert alert-primary mt-4 text-white">
                        <i class="fas fa-lock"></i>
                        <strong>Pemeriksaan Telah Selesai!</strong> Difinalisasi pada
                        <strong>{{ $peta->waktu_telaah_spi ? date('d F Y, H:i', strtotime($peta->waktu_telaah_spi)) : '-' }}</strong>.
                        Semua data <span class="badge badge-dark">READ-ONLY</span>.
                    </div>
                @endif

            </div>{{-- /.section-body --}}
        </section>
    </div>{{-- /.main-content --}}

    {{-- ════════ MODAL: KONFIRMASI FINALISASI ════════ --}}
    @if (($isAdmin || $isAuditor) && $peta->canBeFinalized())
        <div class="modal fade" id="modalKonfirmasiFinalisasi" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-success">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-lock"></i> Konfirmasi Finalisasi Pemeriksaan</h5>
                        <button type="button" class="close text-white"
                            data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size:4rem;"></i>
                        </div>
                        <h5 class="text-center mb-3">Apakah Anda yakin ingin memfinalisasi pemeriksaan ini?</h5>
                        <div class="alert alert-warning">
                            <strong>Konsekuensi finalisasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Status berubah menjadi <span class="badge badge-dark">SELESAI</span></li>
                                <li>Semua data akan <strong>LOCKED</strong></li>
                                <li><strong>Proses TIDAK DAPAT dibatalkan</strong></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                class="fas fa-times"></i> Batal</button>
                        <button type="button" class="btn btn-success"
                            onclick="document.getElementById('formFinalisasi').submit();">
                            <i class="fas fa-lock"></i> Ya, Finalisasi Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════ MODAL: TOLAK AUDIT (AUDITEE) ════════ --}}
    @if ($isAuditee && $peta->status_konfirmasi_auditor == 'Completed' && $statusAudit !== 'final')
        <div class="modal fade" id="modalTolakAudit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-times-circle mr-2"></i> Konfirmasi
                            Penolakan</h5>
                        <button type="button" class="close text-white"
                            data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="{{ route('manajemen-risiko.auditee.reject-audit', $peta->id) }}" method="POST"
                        id="formTolakAudit">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="alert alert-danger border-0 shadow-sm mb-3">
                                <p class="mb-0"><strong>Perhatian:</strong> Dengan menolak, status kembali ke
                                    <span class="badge badge-warning text-dark">Perlu Perbaikan Auditor</span>.
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Alasan Penolakan <span
                                        class="text-danger">*</span></label>
                                <textarea name="catatan_penolakan" class="form-control" rows="5" required
                                    placeholder="Jelaskan secara spesifik bagian mana yang perlu diperbaiki oleh Auditor..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light border" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" id="btnSubmitTolak">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Penolakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        // Toggle alert Not Completed
        $('#selectStatusAuditor').on('change', function() {
            $(this).val() === 'Not Completed' ?
                $('#alertNotCompleted').slideDown() :
                $('#alertNotCompleted').slideUp();
        });
        $(document).ready(function() {
            if ($('#selectStatusAuditor').val() === 'Not Completed') $('#alertNotCompleted').show();
        });

        // Toggle alert keputusan Auditor
        $('#selectKeputusanAuditor').on('change', function() {
            const v = $(this).val();
            $('#alertApprove').toggle(v === 'approve');
            $('#alertReject').toggle(v === 'reject');
        });

        // Cetak PDF
        function cetakPDF(id) {
            window.open(`{{ route('manajemen-risiko.cetak-pdf', ':id') }}`.replace(':id', id), '_blank');
        }

        // Cetak Excel
        function cetakExcel(id) {
            window.open(`{{ route('manajemen-risiko.cetak-excel', ':id') }}`.replace(':id', id), '_blank');
        }
    </script>
@endpush

@push('styles')
    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }

        .border-left-secondary {
            border-left: 4px solid #858796 !important;
        }

        /* Timeline */
        .timeline-wrapper {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 50px;
            padding-bottom: 30px;
            border-left: 2px solid #e3e6f0;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -8px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #4e73df;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #e3e6f0;
        }

        .timeline-item-latest .timeline-marker {
            width: 20px;
            height: 20px;
            left: -10px;
            background-color: #1cc88a;
            box-shadow: 0 0 0 4px #d1f4e4;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(28, 200, 138, .7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(28, 200, 138, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(28, 200, 138, 0);
            }
        }

        .timeline-content {
            background: #fff;
            border-radius: 8px;
        }

        .timeline-header {
            padding: 15px 20px 10px;
            border-bottom: 1px solid #e3e6f0;
        }

        .timeline-body {
            padding: 0 20px 15px;
        }

        .timeline-body .alert {
            border-radius: 6px;
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
        }

        @media (max-width:768px) {
            .timeline-item {
                padding-left: 35px;
            }

            .timeline-header {
                padding: 12px 15px 8px;
            }

            .timeline-body {
                padding: 0 15px 12px;
            }
        }
    </style>
@endpush
