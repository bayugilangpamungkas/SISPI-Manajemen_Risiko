@extends('layout.app')
@section('title', 'Detail Pemeriksaan Manajemen Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditee = in_array($user->Level->name ?? '', ['Auditee', 'PIC']);

        // Status audit dari model helper
        $statusAudit = $peta->status_audit ?? 'belum_ditugaskan';
        $statusLabel = $peta->status_audit_label ?? 'Belum Ditugaskan';
        $statusBadge = $peta->status_audit_badge ?? 'badge-secondary';

        // View mode ditentukan di controller
        $viewMode = $viewMode ?? 'read_only';

        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // LEVEL RISIKO
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

        // Decode pertanyaan dan jawaban
        $questions = $questions ?? [];
        $responses = $responses ?? [];
        $penilaianAuditor = $penilaianAuditor ?? [];
    @endphp

    <div class="main-content">
        <section class="section">
            {{-- Header --}}
            <div class="section-header position-relative" style="padding-top: 10px; padding-bottom: 10px;">
                <a href="{{ $isAuditor ? route('manajemen-risiko.auditor.index') : ($isAuditee ? route('manajemen-risiko.auditee.index') : route('manajemen-risiko.index')) }}"
                    class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <div class="w-100 text-center px-5">
                    <h1 class="font-weight-bold mb-0" style="font-size:19px">
                        PEMERIKSAAN MANAJEMEN RISIKO
                    </h1>
                    <div style="font-size:15px; line-height: 1.2;">
                        <span class="d-block">SATUAN PENGAWAS INTERNAL</span>
                        <span>POLITEKNIK NEGERI MALANG</span>
                    </div>
                </div>
            </div>

            <div class="section-body mt-4">
                {{-- Alert Status --}}
                <div class="alert alert-primary border-left-primary mb-4">
                    <div class="d-flex align-items-center">
                        <div>
                            <strong class="text-white">Status Pemeriksaan:</strong>
                            @php
                                // ✅ SINKRONISASI STATUS PEMERIKSAAN BERDASARKAN WORKFLOW TINDAK LANJUT
                                $statusPemeriksaanLabel = '';
                                $statusPemeriksaanBadge = 'badge-secondary';
                                $statusPemeriksaanGuide = '';

                                // 1. CEK STATUS FINAL
                                if ($statusAudit === 'final') {
                                    $statusPemeriksaanLabel = 'Pemeriksaan Selesai';
                                    $statusPemeriksaanBadge = 'badge-dark';
                                    $statusPemeriksaanGuide =
                                        '✅ Pemeriksaan telah selesai dan data telah difinalisasi secara resmi.';
                                }
                                // 2. CEK STATUS DISETUJUI AUDITEE (MENUNGGU FINALISASI AUDITOR)
                                elseif ($statusAudit === 'disetujui_auditee') {
                                    $statusPemeriksaanLabel = 'Menunggu Finalisasi Auditor';
                                    $statusPemeriksaanBadge = 'badge-info';
                                    if ($isAuditor) {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ AUDITOR:</strong> Unit Kerja sudah konfirmasi. Silakan finalisasi pemeriksaan untuk menyelesaikan proses.';
                                    } else {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ UNIT KERJA:</strong> Menunggu Auditor untuk melakukan finalisasi pemeriksaan.';
                                    }
                                }
                                // 3. CEK WORKFLOW TINDAK LANJUT (NEW)
                                elseif ($peta->status_konfirmasi_auditor === 'Not Completed') {
                                    // 3a. JIKA AUDITEE SUDAH SUBMIT TINDAK LANJUT
                                    if ($peta->status_konfirmasi_auditee) {
                                        $statusPemeriksaanLabel = 'Menunggu ACC Auditor';
                                        $statusPemeriksaanBadge = 'badge-warning';
                                        if ($isAuditor) {
                                            $statusPemeriksaanGuide =
                                                '<strong class="text-white">→ AUDITOR:</strong> Auditee telah mengirim tindak lanjut. Silakan review dan berikan ACC atau minta revisi ulang.';
                                        } else {
                                            $statusPemeriksaanGuide =
                                                '<strong class="text-white">→ UNIT KERJA:</strong> Tindak lanjut Anda sudah dikirim. Menunggu ACC dari Auditor.';
                                        }
                                    }
                                    // 3b. JIKA AUDITEE BELUM SUBMIT TINDAK LANJUT
                                    else {
                                        $statusPemeriksaanLabel = 'Menunggu Revisi Auditee';
                                        $statusPemeriksaanBadge = 'badge-danger';
                                        if ($isAuditee) {
                                            $statusPemeriksaanGuide =
                                                '<strong class="text-white">→ UNIT KERJA:</strong> Auditor meminta tindak lanjut. Silakan lakukan perbaikan dan submit hasil revisi Anda.';
                                        } else {
                                            $statusPemeriksaanGuide =
                                                '<strong class="text-white">→ AUDITOR:</strong> Menunggu Auditee mengirim hasil tindak lanjut / revisi.';
                                        }
                                    }
                                }
                                // 4. CEK AUDITOR SUDAH SUBMIT (COMPLETED) TAPI AUDITEE BELUM KONFIRMASI
                                elseif (
                                    $peta->status_konfirmasi_auditor === 'Completed' &&
                                    !$peta->status_konfirmasi_auditee
                                ) {
                                    $statusPemeriksaanLabel = 'Menunggu Konfirmasi Auditee';
                                    $statusPemeriksaanBadge = 'badge-success';
                                    if ($isAuditee) {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ UNIT KERJA:</strong> Auditor telah menyelesaikan pemeriksaan. Silakan konfirmasi hasil audit.';
                                    } else {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ AUDITOR:</strong> Menunggu konfirmasi akhir dari Unit Kerja.';
                                    }
                                }
                                // 5. AUDITOR SUDAH DITUGASKAN TAPI BELUM INPUT HASIL AUDIT
                                elseif ($peta->auditor_id && !$peta->status_konfirmasi_auditor) {
                                    $statusPemeriksaanLabel = 'Menunggu Pemeriksaan Auditor';
                                    $statusPemeriksaanBadge = 'badge-info';
                                    if ($isAuditor) {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ AUDITOR:</strong> Silakan lakukan pemeriksaan dan input hasil audit untuk Unit Kerja.';
                                    } else {
                                        $statusPemeriksaanGuide =
                                            '<strong class="text-white">→ UNIT KERJA:</strong> Menunggu Auditor melakukan pemeriksaan terhadap risiko ini.';
                                    }
                                }
                                // 6. BELUM ADA AUDITOR
                                else {
                                    $statusPemeriksaanLabel = 'Belum Ditugaskan';
                                    $statusPemeriksaanBadge = 'badge-secondary';
                                    $statusPemeriksaanGuide = 'Belum ada auditor yang ditugaskan untuk risiko ini.';
                                }
                            @endphp
                            <span class="badge {{ $statusPemeriksaanBadge }} p-2">{{ $statusPemeriksaanLabel }}</span>
                            <br>
                            <small class="text-white">
                                {!! $statusPemeriksaanGuide !!}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Info Risiko --}}
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
                                                // ✅ PERBAIKAN: AMBIL NAMA USER DENGAN ROLE AUDITEE/PIC DARI UNIT KERJA
                                                $namaUserUnitKerja = '-';
                                                if ($peta->jenis) {
                                                    // 1. Cari Unit Kerja berdasarkan nama
                                                    $unitKerjaModel = \App\Models\UnitKerja::where(
                                                        'nama_unit_kerja',
                                                        $peta->jenis,
                                                    )->first();

                                                    if ($unitKerjaModel) {
                                                        // 2. Cari User yang:
                                                        //    - Terdaftar di unit kerja ini (id_unit_kerja)
                                                        //    - Memiliki Level "Auditee" atau "PIC"
                                                        $userUnitKerja = \App\Models\User::where(
                                                            'id_unit_kerja',
                                                            $unitKerjaModel->id,
                                                        )
                                                            ->whereHas('Level', function ($query) {
                                                                $query->whereIn('name', ['Auditee', 'PIC']);
                                                            })
                                                            ->first();

                                                        if ($userUnitKerja) {
                                                            $namaUserUnitKerja = $userUnitKerja->name;
                                                        } else {
                                                            // Fallback: Ambil user pertama di unit kerja (tanpa filter level)
                                                            $anyUser = \App\Models\User::where(
                                                                'id_unit_kerja',
                                                                $unitKerjaModel->id,
                                                            )->first();
                                                            if ($anyUser) {
                                                                $namaUserUnitKerja = $anyUser->name;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-user mr-1"></i>PIC: <strong
                                                    class="text-primary">{{ $namaUserUnitKerja }}</strong>
                                            </small>
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
                                                // ✅ PERBAIKAN: Ambil kode kegiatan dengan format KEG-TAHUN-XXX
                                                $kodeKegiatan = '-';
                                                if ($peta->kegiatan) {
                                                    if (!empty($peta->kegiatan->kode_regist)) {
                                                        $kodeKegiatan = $peta->kegiatan->kode_regist;
                                                    } elseif (!empty($peta->kegiatan->id_kegiatan)) {
                                                        $kodeKegiatan = $peta->kegiatan->id_kegiatan;
                                                    } elseif (!empty($peta->kegiatan->kode)) {
                                                        $kodeKegiatan = $peta->kegiatan->kode;
                                                    } else {
                                                        // Fallback: buat format KEG-TAHUN-ID
                                                        $kodeKegiatan =
                                                            'KEG-' .
                                                            date('Y') .
                                                            '-' .
                                                            str_pad($peta->kegiatan->id, 3, '0', STR_PAD_LEFT);
                                                    }
                                                } elseif ($peta->id_kegiatan) {
                                                    $kegiatan = \App\Models\Kegiatan::find($peta->id_kegiatan);
                                                    if ($kegiatan) {
                                                        $kodeKegiatan =
                                                            $kegiatan->kode_regist ??
                                                            'KEG-' .
                                                                date('Y') .
                                                                '-' .
                                                                str_pad($kegiatan->id, 3, '0', STR_PAD_LEFT);
                                                    }
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

                {{-- ========================================
                     SECTION 1: AUDITOR INPUT AUDIT REPORT (NEW WORKFLOW)
                     ✅ HANYA FORM INI YANG DIGUNAKAN UNTUK AUDITOR
                ======================================== --}}
                @if ($isAuditor && $viewMode === 'input_questions')
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                method="POST" id="formInputAudit">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="input_audit_result">

                                <div class="alert alert-info border-0 shadow-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Panduan:</strong> Silakan input hasil pemeriksaan audit untuk Unit Kerja.
                                    Pastikan semua field diisi dengan lengkap dan akurat sesuai SOP SPI.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-shield-alt text-primary mr-1"></i>
                                            Pengendalian Risiko <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="pengendalian" class="form-control" rows="5" required
                                            placeholder="Deskripsi pengendalian risiko yang sudah dilakukan oleh Unit Kerja...">{{ old('pengendalian', $peta->pengendalian) }}</textarea>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Jelaskan sistem pengendalian internal yang telah diterapkan untuk mengelola
                                            risiko ini.
                                        </small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-chart-line text-primary mr-1"></i>
                                            Strategi Mitigasi <span class="text-danger">*</span>
                                        </label>
                                        <select name="mitigasi" class="form-control" required>
                                            <option value="">-- Pilih Strategi Mitigasi --</option>
                                            <option value="Accept Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Accept Risk' ? 'selected' : '' }}>
                                                Terima Risiko
                                            </option>
                                            <option value="Share Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Share Risk' ? 'selected' : '' }}>
                                                Bagikan Risiko
                                            </option>
                                            <option value="Transfer Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Transfer Risk' ? 'selected' : '' }}>
                                                Transfer Risiko
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Pilih strategi mitigasi yang sesuai berdasarkan hasil audit.
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-2">
                                        <i class="fas fa-comment-alt text-primary mr-1"></i>
                                        Komentar Auditor <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="komentar_auditor" class="form-control" rows="5" required
                                        placeholder="Komentar, catatan, atau rekomendasi dari Auditor...">{{ old('komentar_auditor', $hasilAudit->komentar_1 ?? '') }}</textarea>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Berikan komentar atau rekomendasi untuk perbaikan berkelanjutan.
                                    </small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-2">
                                        <i class="fas fa-clipboard-check text-primary mr-1"></i>
                                        Status Konfirmasi <span class="text-danger">*</span>
                                    </label>
                                    <select name="status_konfirmasi_auditor" class="form-control" required
                                        id="selectStatusAuditor">
                                        <option value="">-- Pilih Status Konfirmasi --</option>
                                        <option value="Completed"
                                            {{ old('status_konfirmasi_auditor', $peta->status_konfirmasi_auditor) == 'Completed' ? 'selected' : '' }}>
                                            ✅ Selesai (Audit Selesai)
                                        </option>
                                        <option value="Not Completed"
                                            {{ old('status_konfirmasi_auditor', $peta->status_konfirmasi_auditor) == 'Not Completed' ? 'selected' : '' }}>
                                            ⚠️ Tindak Lanjut (Perlu Revisi)
                                        </option>
                                    </select>
                                    <small class="form-text text-muted" id="helpTextStatus">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Pilih <strong>Selesai</strong> jika audit selesai dan tidak perlu revisi.
                                        Pilih <strong>Tindak Lanjut</strong> jika Auditee perlu melakukan perbaikan.
                                    </small>
                                </div>

                                <div class="alert alert-warning border-0 shadow-sm" id="alertNotCompleted"
                                    style="display: none;">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>PERHATIAN:</strong> Jika Anda memilih <strong>Tindak Lanjut</strong>,
                                    Auditee akan mendapat akses untuk melakukan revisi atau tindak lanjut terhadap hasil
                                    audit ini.
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
                        </div>
                    </div>

                    {{-- ========================================
                     ✅ NEW SECTION: AUDITOR REVIEW FOLLOW-UP FROM AUDITEE
                     (SETELAH AUDITEE SUBMIT TINDAK LANJUT)
                ======================================== --}}
                @elseif($isAuditor && $peta->auditorCanReviewFollowUp())
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-tasks mr-2"></i>Review Hasil Tindak Lanjut dari Auditee
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            {{-- HASIL AUDIT YANG SUDAH DIINPUT AUDITOR (READ-ONLY) --}}
                            <div class="card bg-light mb-4 border-0">
                                <div class="card-header bg-secondary text-white py-3">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-history mr-2"></i>Hasil Audit Anda (Sebelumnya)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold text-dark mb-2">
                                                <i class="fas fa-shield-alt text-secondary mr-1"></i>Pengendalian Risiko
                                            </label>
                                            <div class="p-3 bg-white border rounded" style="min-height: 100px;">
                                                {{ $peta->pengendalian ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold text-dark mb-2">
                                                <i class="fas fa-chart-line text-secondary mr-1"></i>Mitigasi Risiko
                                            </label>
                                            <div class="p-3 bg-white border rounded">
                                                <span class="badge badge-info p-2"
                                                    style="font-size: 14px;">{{ $peta->mitigasi ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-comment-alt text-secondary mr-1"></i>Komentar Auditor
                                        </label>
                                        <div class="p-3 bg-white border rounded" style="min-height: 80px;">
                                            {!! nl2br(e($hasilAudit->komentar_1 ?? '-')) !!}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="font-weight-bold text-dark mb-2">Status Konfirmasi Auditor</label>
                                        <div>
                                            <span class="badge badge-warning p-2" style="font-size: 14px;">
                                                ⚠️ Not Completed (Perlu Tindak Lanjut)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- HASIL TINDAK LANJUT DARI AUDITEE --}}
                            <div class="card border-primary mb-4 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-reply mr-2"></i>Tindak Lanjut dari Auditee
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $revisionNotes = $peta->revision_notes;
                                    @endphp

                                    @if ($revisionNotes && isset($revisionNotes['catatan_tindak_lanjut']))
                                        <div class="mb-3">
                                            <label class="font-weight-bold text-dark mb-2">
                                                <i class="fas fa-edit text-primary mr-1"></i>Catatan Tindak Lanjut dari
                                                Auditee
                                            </label>
                                            <div class="p-4 bg-light border rounded shadow-sm"
                                                style="min-height: 150px; line-height: 1.8;">
                                                {!! nl2br(e($revisionNotes['catatan_tindak_lanjut'])) !!}
                                            </div>
                                        </div>

                                        {{-- ✅ TAMPILAN LINK DATA DUKUNG (READ-ONLY) --}}
                                        @if (isset($revisionNotes['link_data_dukung']) && !empty($revisionNotes['link_data_dukung']))
                                            <div class="mb-3">
                                                <label class="font-weight-bold text-dark mb-2">
                                                    <i class="fas fa-link text-primary mr-1"></i>Link Data Dukung
                                                </label>
                                                <div class="p-3 bg-light border rounded">
                                                    <a href="{{ $revisionNotes['link_data_dukung'] }}" target="_blank"
                                                        rel="noopener noreferrer" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Link Data Dukung
                                                    </a>
                                                    <div class="mt-2">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            URL: <code
                                                                class="bg-white p-1">{{ Str::limit($revisionNotes['link_data_dukung'], 60) }}</code>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-3">
                                                <label class="font-weight-bold text-muted mb-2">
                                                    <i class="fas fa-link mr-1"></i>Link Data Dukung
                                                </label>
                                                <div class="p-3 bg-light border rounded">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Auditee tidak melampirkan link data dukung
                                                    </small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="font-weight-bold text-dark mb-2">Status Konfirmasi
                                                    Auditee</label>
                                                <div class="mt-2">
                                                    @php
                                                        $statusAuditee =
                                                            $revisionNotes['status_auditee'] ??
                                                            $peta->status_konfirmasi_auditee;
                                                        $badgeAuditee =
                                                            $statusAuditee === 'Completed' ? 'success' : 'warning';
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeAuditee }} p-2"
                                                        style="font-size: 14px;">
                                                        @if ($statusAuditee === 'Completed')
                                                            ✅ Completed (Tindak Lanjut Selesai)
                                                        @else
                                                            ⏳ Not Completed (Masih Dalam Proses)
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-weight-bold text-dark mb-2">Waktu Submit</label>
                                                <div class="mt-2">
                                                    <span class="badge badge-secondary p-2" style="font-size: 14px;">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ isset($revisionNotes['submitted_at']) ? date('d M Y, H:i', strtotime($revisionNotes['submitted_at'])) : '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info border-0">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Auditee telah submit tindak lanjut. Silakan review hasilnya.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- FORM KEPUTUSAN AUDITOR --}}
                            <div class="card border-success shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 font-weight-bold text-dark">
                                        <i class="fas fa-gavel text-success mr-2"></i>Keputusan Auditor
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                        method="POST" id="formReviewFollowUp">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="approve_follow_up">

                                        <div class="form-group mb-4">
                                            <label class="font-weight-bold text-dark mb-2">
                                                <i class="fas fa-balance-scale text-success mr-1"></i>
                                                Keputusan <span class="text-danger">*</span>
                                            </label>
                                            <select name="keputusan_auditor" class="form-control" required
                                                id="selectKeputusanAuditor">
                                                <option value="">-- Pilih Keputusan --</option>
                                                <option value="approve">✅ APPROVE (Setujui & Selesai)</option>
                                                <option value="reject">❌ REJECT (Minta Perbaikan Ulang)</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Pilih keputusan berdasarkan hasil review tindak lanjut dari Auditee.
                                            </small>
                                        </div>

                                        <div class="alert alert-success border-0 shadow-sm" id="alertApprove"
                                            style="display: none;">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <strong>APPROVE:</strong> Dengan memilih APPROVE, status audit akan berubah
                                            menjadi <strong>SELESAI</strong>, dan Auditee dapat melakukan konfirmasi akhir
                                            untuk finalisasi.
                                        </div>

                                        <div class="alert alert-danger border-0 shadow-sm" id="alertReject"
                                            style="display: none;">
                                            <i class="fas fa-times-circle mr-2"></i>
                                            <strong>REJECT:</strong> Dengan memilih REJECT, Auditee akan diminta untuk
                                            melakukan perbaikan ulang terhadap tindak lanjut mereka.
                                        </div>

                                        <div class="text-right mt-4 pt-3 border-top">
                                            <a href="{{ route('manajemen-risiko.auditor.index') }}"
                                                class="btn btn-secondary btn-lg px-5 mr-2">
                                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-success btn-lg px-5"
                                                id="btnSubmitKeputusan">
                                                <i class="fas fa-check mr-2"></i> Submit Keputusan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================
                     SECTION 2: AUDITEE VIEW & CONFIRMATION (NEW WORKFLOW)
                     ✅ TAMPILAN INI SESUAI REQUIREMENT DOSEN
                ======================================== --}}
                @elseif($isAuditee)
                    @php
                        // ✅ VALIDASI KETAT: Auditor harus sudah submit hasil audit
                        $auditorHasSubmitted = false;

                        // Cek apakah Auditor sudah input semua field required
                        if (
                            $peta->pengendalian &&
                            $peta->mitigasi &&
                            $peta->status_konfirmasi_auditor &&
                            in_array($peta->status_konfirmasi_auditor, ['Completed', 'Not Completed'])
                        ) {
                            $auditorHasSubmitted = true;
                        }

                        // Alternatif: cek dari hasil_audit table
                        if (
                            !$auditorHasSubmitted &&
                            isset($hasilAudit) &&
                            $hasilAudit &&
                            $hasilAudit->pengendalian &&
                            $hasilAudit->mitigasi
                        ) {
                            $auditorHasSubmitted = true;
                        }
                    @endphp

                    @if (!$auditorHasSubmitted)
                        {{-- ❌ AUDITOR BELUM SUBMIT: HANYA TAMPILKAN INFORMASI --}}
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0 font-weight-bold">
                                    <i class="fas fa-hourglass-half mr-2"></i>Status Pemeriksaan Audit
                                </h5>
                            </div>
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-clock text-info" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="text-info font-weight-bold mb-3">
                                    Menunggu Hasil Pemeriksaan dari Auditor
                                </h4>
                                <p class="text-muted mb-4" style="font-size: 1.1rem;">
                                    Auditor <strong>{{ $peta->auditor->name ?? 'yang ditugaskan' }}</strong>
                                    sedang melakukan proses pemeriksaan terhadap risiko ini.
                                    Silakan tunggu hingga Auditor menyelesaikan input hasil audit.
                                </p>
                                <div class="alert alert-light border shadow-sm">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    <strong>Informasi:</strong> Anda akan mendapat notifikasi dan akses untuk melihat
                                    hasil audit setelah Auditor menyelesaikan proses pemeriksaan.
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ✅ AUDITOR SUDAH SUBMIT: TAMPILKAN HASIL AUDIT & FORM KONFIRMASI --}}
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="alert alert-success border-0 shadow-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <strong>Auditor telah menyelesaikan pemeriksaan!</strong>
                                    <p class="mb-0 mt-2">
                                        Berikut adalah hasil pemeriksaan audit yang telah dilakukan oleh
                                        <strong>{{ $peta->auditor->name ?? 'Auditor' }}</strong>.
                                        Silakan periksa dengan teliti dan berikan konfirmasi di bawah.
                                    </p>
                                </div>

                                {{-- HASIL AUDIT DARI AUDITOR (READ-ONLY) --}}
                                <div class="card bg-light mb-4 border-0">
                                    <div class="card-header bg-secondary text-white py-3">
                                        <h6 class="mb-0 font-weight-bold">
                                            <i class="fas fa-file-alt mr-2"></i>Ringkasan Hasil Audit
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark mb-2">
                                                    <i class="fas fa-shield-alt text-secondary mr-1"></i>Pengendalian
                                                    Risiko
                                                </label>
                                                <div class="p-3 bg-white border rounded"
                                                    style="min-height: 100px; line-height: 1.6;">
                                                    {{ $peta->pengendalian ?? ($hasilAudit->pengendalian ?? '-') }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold text-dark mb-2">
                                                    <i class="fas fa-chart-line text-secondary mr-1"></i>Mitigasi Risiko
                                                </label>
                                                <div class="p-3 bg-white border rounded">
                                                    @php
                                                        $mitigasi = $peta->mitigasi ?? ($hasilAudit->mitigasi ?? '-');
                                                        $mitigasiBadge = 'secondary';
                                                        if ($mitigasi == 'Accept Risk') {
                                                            $mitigasiBadge = 'success';
                                                        } elseif ($mitigasi == 'Share Risk') {
                                                            $mitigasiBadge = 'info';
                                                        } elseif ($mitigasi == 'Transfer Risk') {
                                                            $mitigasiBadge = 'warning';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $mitigasiBadge }} p-2"
                                                        style="font-size: 14px;">{{ $mitigasi }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="font-weight-bold text-dark mb-2">
                                                <i class="fas fa-comment-alt text-secondary mr-1"></i>Komentar Auditor
                                            </label>
                                            <div class="p-3 bg-white border rounded shadow-sm"
                                                style="min-height: 100px; line-height: 1.6;">
                                                {!! nl2br(e($hasilAudit->komentar_1 ?? '-')) !!}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-dark mb-2">Status Konfirmasi
                                                    Auditor</label>
                                                <div class="mt-2">
                                                    @php
                                                        $statusAuditor = $peta->status_konfirmasi_auditor ?? '-';
                                                        $statusAuditorBadge = 'secondary';
                                                        if ($statusAuditor == 'Completed') {
                                                            $statusAuditorBadge = 'success';
                                                        } elseif ($statusAuditor == 'Not Completed') {
                                                            $statusAuditorBadge = 'warning';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $statusAuditorBadge }} p-2"
                                                        style="font-size: 14px;">
                                                        @if ($statusAuditor == 'Completed')
                                                            ✅ Completed (Audit Selesai)
                                                        @elseif($statusAuditor == 'Not Completed')
                                                            ⚠️ Not Completed (Perlu Tindak Lanjut)
                                                        @else
                                                            {{ $statusAuditor }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-dark mb-2">Level Risiko</label>
                                                <div class="mt-2">
                                                    <span class="badge {{ $levelBadge }} p-2" style="font-size: 14px;">
                                                        {{ $hasilAudit->level_risiko ?? $levelText }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold text-dark mb-2">Skor Total</label>
                                                <div class="mt-2">
                                                    <span class="badge badge-secondary p-2" style="font-size: 14px;">
                                                        {{ $hasilAudit->skor_total ?? $skorTotal }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CONDITIONAL AUDITEE ACTION BASED ON AUDITOR STATUS --}}
                                @if ($peta->status_konfirmasi_auditor == 'Completed')
                                    {{-- ✅ AUDITOR = COMPLETED: ACC + TOLAK --}}
                                    <div class="card border-success shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h6 class="mb-0 font-weight-bold text-dark">
                                                <i class="fas fa-check-circle text-success mr-2"></i>Konfirmasi Akhir Unit
                                                Kerja
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if ($statusAudit !== 'final' && !$peta->status_konfirmasi_auditee)
                                                <div class="alert alert-success border-0 shadow-sm mb-4">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    <strong>Status:</strong> Auditor telah menyatakan bahwa audit
                                                    <strong>SELESAI</strong> dan tidak memerlukan tindak lanjut.
                                                    Silakan pilih salah satu opsi di bawah:
                                                </div>
                                            @endif

                                            @php
                                                // ✅ CEK APAKAH ADA CATATAN PENOLAKAN SEBELUMNYA
                                                $hasRejectionNote = false;
                                                $rejectionData = null;
                                                if ($peta->catatan_revisi) {
                                                    try {
                                                        $revisionNotes = json_decode($peta->catatan_revisi, true);
                                                        if (
                                                            isset($revisionNotes['status']) &&
                                                            $revisionNotes['status'] === 'rejected_by_auditee'
                                                        ) {
                                                            $hasRejectionNote = true;
                                                            $rejectionData = $revisionNotes;
                                                        }
                                                    } catch (\Exception $e) {
                                                        // Ignore error
                                                    }
                                                }
                                            @endphp

                                            {{-- ✅ TAMPILKAN CATATAN PENOLAKAN JIKA ADA --}}
                                            {{-- @if ($hasRejectionNote && $rejectionData)
                                                <div class="alert alert-danger border-0 shadow-sm mb-4">
                                                    <h6 class="font-weight-bold text-danger mb-2">
                                                        <i class="fas fa-times-circle mr-1"></i> Riwayat Penolakan Sebelumnya
                                                    </h6>
                                                    <div class="mb-2">
                                                        <strong>Ditolak oleh:</strong> {{ $rejectionData['rejected_by'] ?? '-' }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Waktu penolakan:</strong> 
                                                        {{ isset($rejectionData['rejected_at']) ? date('d M Y, H:i', strtotime($rejectionData['rejected_at'])) : '-' }}
                                                    </div>
                                                    <div>
                                                        <strong>Alasan penolakan:</strong>
                                                        <div class="p-2 bg-white rounded mt-1" style="line-height: 1.6;">
                                                            {{ $rejectionData['catatan_penolakan'] ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif --}}

                                            {{-- ✅ TOMBOL SUBMIT HANYA MUNCUL JIKA STATUS BUKAN "PEMERIKSAAN SELESAI" --}}
                                            @if ($statusAudit !== 'final')
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="card border-success h-100">
                                                            <div class="card-body text-center">
                                                                <i class="fas fa-check-double text-success mb-3"
                                                                    style="font-size: 3rem;"></i>
                                                                <h5 class="card-title text-success font-weight-bold">ACC /
                                                                    SETUJU</h5>
                                                                <p class="card-text text-muted mb-4">
                                                                    Setujui hasil pemeriksaan audit dan lanjutkan ke
                                                                    finalisasi
                                                                </p>
                                                                <form
                                                                    action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('✅ Apakah Anda yakin ingin MENYETUJUI hasil audit ini?\n\nDengan mengklik OK, Anda menyatakan bahwa hasil audit sudah sesuai dan siap untuk difinalisasi oleh Auditor.')">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="action"
                                                                        value="final_approval">
                                                                    <button type="submit"
                                                                        class="btn btn-success btn-lg btn-block shadow-sm">
                                                                        <i class="fas fa-check-double mr-2"></i> SUBMIT ACC
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="card border-danger h-100">
                                                            <div class="card-body text-center">
                                                                <i class="fas fa-times-circle text-danger mb-3"
                                                                    style="font-size: 3rem;"></i>
                                                                <h5 class="card-title text-danger font-weight-bold">TOLAK
                                                                </h5>
                                                                <p class="card-text text-muted mb-4">
                                                                    Minta Auditor untuk memperbaiki hasil audit per-item
                                                                </p>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-lg btn-block shadow-sm"
                                                                    data-toggle="modal" data-target="#modalTolakAudit">
                                                                    <i class="fas fa-times-circle mr-2"></i> TOLAK AUDIT
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="alert alert-warning border-0 mt-3">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    <strong>Catatan Penting:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li><strong>ACC:</strong> Audit selesai dan akan dilanjutkan ke
                                                            finalisasi oleh Auditor.</li>
                                                        <li><strong>TOLAK:</strong> Meminta Auditor untuk memperbaiki hasil
                                                            audit. Auditor akan mengedit ulang data audit sesuai catatan
                                                            Anda.</li>
                                                    </ul>
                                                </div>
                                            @else
                                                <div class="alert alert-success text-center border-0 shadow-sm">
                                                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                                    <strong>Pemeriksaan Selesai!</strong>
                                                    <p class="mb-0 mt-2">Anda telah menyelesaikan konfirmasi untuk audit
                                                        ini.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($peta->status_konfirmasi_auditor == 'Not Completed')
                                    {{-- ⚠️ AUDITOR = NOT COMPLETED: FORM TINDAK LANJUT --}}
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-header bg-primary text-white py-3">
                                            <h6 class="mb-0 font-weight-bold">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>Tindak Lanjut Diperlukan
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-warning border-0 shadow-sm mb-4">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Status:</strong> Auditor meminta <strong>TINDAK LANJUT</strong>
                                                untuk perbaikan.
                                                Silakan lengkapi form di bawah ini dengan detail tindak lanjut yang
                                                telah/akan dilakukan.
                                            </div>

                                            <form
                                                action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="submit_follow_up">

                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-dark mb-2">
                                                        <i class="fas fa-edit text-primary mr-1"></i>
                                                        Catatan Tindak Lanjut <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="catatan_tindak_lanjut" class="form-control" rows="6" required
                                                        placeholder="Jelaskan secara detail tindak lanjut yang telah/akan dilakukan untuk mengatasi temuan audit..."></textarea>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Jelaskan secara detail langkah perbaikan yang telah atau akan Anda
                                                        lakukan.
                                                    </small>
                                                </div>

                                                {{-- ✅ INPUT BARU: LINK DATA DUKUNG --}}
                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-dark mb-2">
                                                        <i class="fas fa-link text-primary mr-1"></i>Link Data Dukung
                                                        (Opsional)
                                                    </label>
                                                    <input type="url" name="link_data_dukung" class="form-control"
                                                        placeholder="https://drive.google.com/... atau URL lainnya">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Lampirkan link bukti pendukung (Google Drive, OneDrive, dll).
                                                        <strong>Tidak wajib</strong>, tetapi dapat membantu proses
                                                        verifikasi Auditor.
                                                    </small>
                                                </div>

                                                <div class="form-group mb-4">
                                                    <label class="font-weight-bold text-dark mb-2">
                                                        <i class="fas fa-clipboard-check text-primary mr-1"></i>
                                                        Status Konfirmasi Auditee <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="status_konfirmasi_auditee" class="form-control"
                                                        required>
                                                        <option value="">-- Pilih Status --</option>
                                                        <option value="Completed">✅ Completed (Tindak Lanjut Selesai)
                                                        </option>
                                                        <option value="Not Completed">⏳ Not Completed (Masih Dalam Proses)
                                                        </option>
                                                    </select>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Pilih "Completed" jika tindak lanjut sudah selesai, atau "Not
                                                        Completed" jika masih dalam proses.
                                                    </small>
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
                                        <strong>Status Tidak Lengkap</strong>
                                        <p class="mb-0 mt-2">
                                            Auditor belum menentukan status konfirmasi. Silakan hubungi Auditor untuk
                                            informasi lebih lanjut.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- ========================================
                     SECTION 3: READ-ONLY VIEW (ADMIN / FINAL STATE)
                ======================================== --}}
                @else
                    <div class="card mb-4">
                        <div class="card-body">
                            @if ($peta->pengendalian || $peta->mitigasi || (isset($hasilAudit) && $hasilAudit))
                                {{-- Tampilkan Hasil Audit jika ada --}}
                                <div class="card bg-light mb-3">
                                    {{-- <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Hasil Pemeriksaan Audit</h6>
                                    </div> --}}
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">Pengendalian Risiko</label>
                                                <div class="p-3 bg-white border rounded">
                                                    {{ $peta->pengendalian ?? ($hasilAudit->pengendalian ?? '-') }}
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">Mitigasi Risiko</label>
                                                <div class="p-3 bg-white border rounded">
                                                    @php
                                                        $mitigasi = $peta->mitigasi ?? ($hasilAudit->mitigasi ?? '-');
                                                    @endphp
                                                    {{ $mitigasi }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="font-weight-bold text-dark">Komentar Auditor</label>
                                            <div class="p-3 bg-white border rounded shadow-sm"
                                                style="min-height: 100px; line-height: 1.6;">
                                                {!! nl2br(e($hasilAudit->komentar_1 ?? '-')) !!}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Status Auditor</label>
                                                <div class="mt-2">
                                                    <span class="badge badge-secondary p-2">
                                                        {{ $peta->status_konfirmasi_auditor ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Status Auditee</label>
                                                <div class="mt-2">
                                                    <span class="badge badge-secondary p-2">
                                                        {{ $peta->status_konfirmasi_auditee ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada hasil pemeriksaan audit yang diinput.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif



                {{-- ========================================
                     TOMBOL CETAK (HANYA UNTUK ADMIN - AUDIT FINAL)
                     ✅ MUNCUL JIKA STATUS AUDIT = FINAL
                ======================================== --}}
                @if ($isAdmin && $statusAudit === 'final')
                    <div class="card border-success mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-print"></i> Cetak Dokumen Hasil Audit
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-white">
                                <i class="fas fa-check-circle"></i>
                                <strong>Audit Telah Final!</strong>
                                <p class="mb-0 mt-2">
                                    Pemeriksaan audit untuk risiko ini telah selesai dan difinalisasi.
                                    Klik tombol di bawah untuk mencetak dokumen hasil audit.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-danger h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="card-title">Cetak Format PDF</h5>
                                            <p class="card-text text-muted">
                                                Format resmi untuk arsip dan dokumentasi audit
                                            </p>
                                            <button onclick="cetakPDF({{ $peta->id }})"
                                                class="btn btn-danger btn-lg btn-block">
                                                <i class="fas fa-print mr-2"></i> Cetak PDF
                                            </button>
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle"></i> Template: Lembar Monitoring Manajemen
                                                Risiko
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-success h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-excel text-success mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="card-title">Cetak Format Excel</h5>
                                            <p class="card-text text-muted">
                                                Format untuk analisis dan pengolahan data lebih lanjut
                                            </p>
                                            <button onclick="cetakExcel({{ $peta->id }})"
                                                class="btn btn-success btn-lg btn-block">
                                                <i class="fas fa-print mr-2"></i> Cetak Excel
                                            </button>
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle"></i> Template: Buku template.xlsx
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif




                {{-- ========================================
                     TOMBOL FINALISASI (HANYA AUDITOR)
                ======================================== --}}
                @if ($isAuditor && $peta->canBeFinalized())
                    <div class="card border-success mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-lock"></i> Finalisasi Pemeriksaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert ">
                                <i class="fas fa-check-circle"></i>
                                <strong>Pemeriksaan siap difinalisasi!</strong>
                                <p class="mb-2 mt-2">Unit Kerja telah mengkonfirmasi hasil pemeriksaan. Anda dapat
                                    memfinalisasi
                                    pemeriksaan ini untuk mengunci semua data dan menyelesaikan proses pemeriksaan secara
                                    resmi.</p>
                                <ul class="mb-0">
                                    <li>Status saat ini: <span class="badge badge-info p-2">{{ $statusLabel }}</span>
                                    </li>
                                    <li>Unit Kerja: <strong>{{ $peta->jenis }}</strong></li>
                                    <li>Auditor: <strong>{{ $peta->auditor->name ?? '-' }}</strong></li>
                                    <li>Kode Kegiatan:
                                        @php
                                            // ✅ PERBAIKAN: Ambil kode kegiatan dengan format KEG-TAHUN-XXX
                                            $kodeKegiatan = '-';
                                            if ($peta->kegiatan) {
                                                if (!empty($peta->kegiatan->kode_regist)) {
                                                    $kodeKegiatan = $peta->kegiatan->kode_regist;
                                                } elseif (!empty($peta->kegiatan->id_kegiatan)) {
                                                    $kodeKegiatan = $peta->kegiatan->id_kegiatan;
                                                } elseif (!empty($peta->kegiatan->kode)) {
                                                    $kodeKegiatan = $peta->kegiatan->kode;
                                                } else {
                                                    // Fallback: buat format KEG-TAHUN-ID
                                                    $kodeKegiatan =
                                                        'KEG-' .
                                                        date('Y') .
                                                        '-' .
                                                        str_pad($peta->kegiatan->id, 3, '0', STR_PAD_LEFT);
                                                }
                                            } elseif ($peta->id_kegiatan) {
                                                $kegiatan = \App\Models\Kegiatan::find($peta->id_kegiatan);
                                                if ($kegiatan) {
                                                    $kodeKegiatan =
                                                        $kegiatan->kode_regist ??
                                                        'KEG-' .
                                                            date('Y') .
                                                            '-' .
                                                            str_pad($kegiatan->id, 3, '0', STR_PAD_LEFT);
                                                }
                                            }
                                        @endphp
                                        {{ $kodeKegiatan }}</li>
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


                {{-- ========================================
                     RIWAYAT AKTIVITAS PEMERIKSAAN
                     ✅ TAMPILAN TIMELINE YANG RAPI DAN TERSTRUKTUR
                ======================================== --}}
                @if ($peta->comment_prs->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history"></i> Riwayat Aktivitas Pemeriksaan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline-wrapper">
                                @foreach ($peta->comment_prs->sortByDesc('created_at') as $index => $comment)
                                    <div class="timeline-item {{ $index === 0 ? 'timeline-item-latest' : '' }}">
                                        <div class="timeline-marker">
                                            <i class="fas fa-circle"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 font-weight-bold text-primary">
                                                            {{ $comment->user->name ?? 'System' }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $comment->created_at->format('d M Y, H:i') }}
                                                            <span
                                                                class="ml-2">({{ $comment->created_at->diffForHumans() }})</span>
                                                        </small>
                                                    </div>
                                                    @php
                                                        // Tentukan badge berdasarkan jenis komentar
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
                                                        <i class="fas fa-{{ $jenisIcon }} mr-1"></i>
                                                        {{ $jenisLabel }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="timeline-body mt-3">
                                                <div class="alert alert-light mb-0 border-left-{{ $jenisBadge }}">
                                                    <p class="mb-0" style="line-height: 1.6;">
                                                        {{ $comment->comment }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($peta->comment_prs->count() === 0)
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3 mb-0">Belum ada aktivitas pemeriksaan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


                {{-- ✅ BADGE STATUS DISETUJUI UNIT KERJA (untuk Admin/Auditor) --}}
                @if (($isAdmin || $isAuditor) && $statusAudit === 'disetujui_auditee')
                    <div class="alert alert-primary mt-4 text-white">
                        <i class="fas fa-info-circle"></i>
                        @if ($isAuditor)
                            <strong>Status Pemeriksaan:</strong> Unit Kerja telah mengkonfirmasi hasil pemeriksaan.
                            Silakan <strong>finalisasi pemeriksaan</strong> untuk menyelesaikan proses secara resmi.
                        @else
                            <strong>Status Pemeriksaan:</strong> Unit Kerja telah mengkonfirmasi hasil pemeriksaan.
                            Menunggu Auditor untuk <strong>finalisasi pemeriksaan</strong>.
                        @endif
                    </div>
                @endif

                {{-- ✅ BADGE PEMERIKSAAN FINAL (untuk semua user) --}}
                @if ($statusAudit === 'final')
                    <div class="alert alert-primary mt-4 text-white">
                        <i class="fas fa-lock"></i>
                        <strong>Pemeriksaan Telah Selesai!</strong> Data pemeriksaan telah difinalisasi
                        pada
                        <strong>{{ $peta->waktu_telaah_spi ? date('d F Y, H:i', strtotime($peta->waktu_telaah_spi)) : '-' }}</strong>.
                        Semua data bersifat <span class="badge badge-dark">READ-ONLY</span>.
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- ========================================
         MODAL: KONFIRMASI FINALISASI
    ======================================== --}}
    @if (($isAdmin || $isAuditor) && $peta->canBeFinalized())
        <div class="modal fade" id="modalKonfirmasiFinalisasi" tabindex="-1" role="dialog"
            aria-labelledby="modalKonfirmasiFinalisasiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-success">
                    <div class="modal-header bg- text-white">
                        <h5 class="modal-title" id="modalKonfirmasiFinalisasiLabel">
                            <i class="fas fa-lock"></i> Konfirmasi Finalisasi Pemeriksaan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-center mb-3">Apakah Anda yakin ingin memfinalisasi pemeriksaan ini?</h5>
                        <div class="alert alert-warning">
                            <strong>Konsekuensi finalisasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Status pemeriksaan akan berubah menjadi <span class="badge badge-dark">SELESAI</span>
                                </li>
                                <li>Semua data akan <strong>LOCKED</strong> (tidak dapat diubah)</li>
                                <li>Pemeriksaan dianggap <strong>SELESAI RESMI</strong></li>
                                <li><strong>Proses TIDAK DAPAT dibatalkan</strong></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="button" class="btn btn-success"
                            onclick="document.getElementById('formFinalisasi').submit();">
                            <i class="fas fa-lock"></i> Ya, Finalisasi Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================
         ✅ MODAL: TOLAK AUDIT (AUDITEE)
         Modal untuk Auditee menolak hasil audit dan meminta perbaikan dari Auditor
    ======================================== --}}
    @if ($isAuditee && $peta->status_konfirmasi_auditor == 'Completed' && $statusAudit !== 'final')
        <div class="modal fade" id="modalTolakAudit" tabindex="-1" role="dialog"
            aria-labelledby="modalTolakAuditLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document"> {{-- Ukuran modal dikecilkan agar lebih proporsional --}}
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold" id="modalTolakAuditLabel">
                            <i class="fas fa-times-circle mr-2"></i> Konfirmasi Penolakan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{ route('manajemen-risiko.auditee.reject-audit', $peta->id) }}" method="POST"
                        id="formTolakAudit">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            {{-- Alert Sederhana --}}
                            <div class="alert alert-soft-danger border-left-danger shadow-sm mb-3">
                                <p class="mb-0">
                                    <strong>Perhatian:</strong> Dengan menolak hasil audit, status akan kembali ke
                                    <span class="badge badge-warning text-dark">Perlu Perbaikan Auditor</span>.
                                </p>
                            </div>

                            {{-- Form Input --}}
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
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
        // ✅ Script untuk toggle alert berdasarkan Status Konfirmasi Auditor
        $('#selectStatusAuditor').on('change', function() {
            const selectedStatus = $(this).val();
            if (selectedStatus === 'Not Completed') {
                $('#alertNotCompleted').slideDown();
            } else {
                $('#alertNotCompleted').slideUp();
            }
        });

        // ✅ Trigger saat halaman load (jika ada nilai selected)
        $(document).ready(function() {
            if ($('#selectStatusAuditor').val() === 'Not Completed') {
                $('#alertNotCompleted').show();
            }
        });

        // ✅ Script untuk toggle alert berdasarkan Keputusan Auditor
        $('#selectKeputusanAuditor').on('change', function() {
            const selectedKeputusan = $(this).val();
            if (selectedKeputusan === 'approve') {
                $('#alertApprove').slideDown();
                $('#alertReject').slideUp();
            } else if (selectedKeputusan === 'reject') {
                $('#alertReject').slideDown();
                $('#alertApprove').slideUp();
            } else {
                $('#alertApprove').slideUp();
                $('#alertReject').slideUp();
            }
        });

        // ✅ Script untuk cetak PDF
        function cetakPDF(id) {
            const url = `{{ route('manajemen-risiko.cetak-pdf', ':id') }}`.replace(':id', id);
            window.open(url, '_blank');
        }

        // ✅ Script untuk cetak Excel
        function cetakExcel(id) {
            const url = `{{ route('manajemen-risiko.cetak-excel', ':id') }}`.replace(':id', id);
            window.open(url, '_blank');
        }
    </script>
@endpush

@push('styles')
    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .bg-warning-light {
            background-color: #d1cdff;
        }

        /* ========================================
                                                                                                                                                       TIMELINE STYLES - RIWAYAT AKTIVITAS
                                                                                                                                                    ======================================== */
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
                box-shadow: 0 0 0 0 rgba(28, 200, 138, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(28, 200, 138, 0);
            }

            100% {}

            box-shadow: 0 0 0 0 rgba(28, 200, 138, 0);
        }
        }

        .timeline-marker i {
            display: none;
        }

        .timeline-content {
            background: #fff;
            border-radius: 8px;
            padding: 0;
        }

        .timeline-header {
            padding: 15px 20px 10px 20px;
            border-bottom: 1px solid #e3e6f0;
        }

        .timeline-header h6 {
            margin: 0;
            font-size: 16px;
        }

        .timeline-body {
            padding: 0 20px 15px 20px;
        }

        .timeline-body .alert {
            border-radius: 6px;
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
        }

        /* Border kiri untuk kategori */
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

        /* Badge styling */
        .badge {}

        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.3px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .timeline-item {
                padding-left: 35px;
            }

            .timeline-header {
                padding: 12px 15px 8px 15px;
            }

            .timeline-body {
                padding: 0 15px 12px 15px;
            }

            .timeline-header h6 {
                font-size: 14px;
            }
        }
    </style>
@endpush
