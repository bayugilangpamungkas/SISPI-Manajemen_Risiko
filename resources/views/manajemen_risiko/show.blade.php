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
                            <strong class="text-white">Status Pemeriksaan:</strong> <span
                                class="badge {{ $statusBadge }} p-2">{{ $statusLabel }}</span>
                            <br>
                            <small class="text-white">
                                @if ($statusAudit === 'belum_ditugaskan')
                                    Belum ada auditor yang ditugaskan untuk risiko ini.
                                @elseif($statusAudit === 'menunggu_wawancara')
                                    <strong class="text-white">→ AUDITOR:</strong> Silakan input daftar pertanyaan
                                    pemeriksaan untuk Unit
                                    Kerja.
                                @elseif($statusAudit === 'menunggu_jawaban')
                                    <strong class="text-white">→ UNIT KERJA:</strong> Silakan jawab pertanyaan pemeriksaan
                                    dari Auditor.
                                @elseif($statusAudit === 'menunggu_review')
                                    <strong class="text-white">→ AUDITOR:</strong> Silakan verifikasi jawaban dari Unit
                                    Kerja dan berikan
                                    penilaian.
                                @elseif($statusAudit === 'perlu_revisi')
                                    <strong class="text-white">→ UNIT KERJA:</strong> Auditor meminta Anda melakukan
                                    perbaikan terhadap
                                    jawaban.
                                @elseif($statusAudit === 'menunggu_konfirmasi_auditor')
                                    <strong class="text-white">→ AUDITOR:</strong> Silakan konfirmasi hasil perbaikan dari
                                    Unit Kerja.
                                @elseif($statusAudit === 'menunggu_konfirmasi_auditee')
                                    <strong class="text-white">→ UNIT KERJA:</strong> Silakan konfirmasi hasil verifikasi
                                    dari Auditor.
                                @elseif($statusAudit === 'disetujui_auditee')
                                    <strong class="text-white">→ AUDITOR:</strong> Unit Kerja sudah konfirmasi. Silakan
                                    finalisasi pemeriksaan
                                    untuk menyelesaikan proses.
                                @elseif($statusAudit === 'final')
                                    ✅ Pemeriksaan telah selesai dan data telah difinalisasi secara resmi.
                                @endif
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
                                        <td>: {{ $peta->jenis }}</td>
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
                ======================================== --}}
                @if ($isAuditor && $viewMode === 'input_questions')
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Input Hasil Pemeriksaan Audit</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                method="POST" id="formInputAudit">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="input_audit_result">

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Silakan input hasil pemeriksaan audit untuk Unit
                                    Kerja.
                                    Pastikan semua field diisi dengan lengkap dan akurat sesuai SOP SPI.
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold">Pengendalian Risiko (Risk Control) <span
                                                class="text-danger">*</span></label>
                                        <textarea name="pengendalian" class="form-control" rows="4" required
                                            placeholder="Deskripsi pengendalian risiko yang sudah dilakukan oleh Unit Kerja...">{{ old('pengendalian', $peta->pengendalian) }}</textarea>
                                        <small class="form-text text-muted">
                                            Jelaskan sistem pengendalian internal yang telah diterapkan untuk mengelola
                                            risiko ini.
                                        </small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold">Mitigasi Risiko (Risk Mitigation) <span
                                                class="text-danger">*</span></label>
                                        <select name="mitigasi" class="form-control" required>
                                            <option value="">-- Pilih Strategi Mitigasi --</option>
                                            <option value="Accept Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Accept Risk' ? 'selected' : '' }}>
                                                Accept Risk (Terima Risiko)
                                            </option>
                                            <option value="Share Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Share Risk' ? 'selected' : '' }}>
                                                Share Risk (Bagikan Risiko)
                                            </option>
                                            <option value="Transfer Risk"
                                                {{ old('mitigasi', $peta->mitigasi) == 'Transfer Risk' ? 'selected' : '' }}>
                                                Transfer Risk (Transfer Risiko)
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">
                                            Pilih strategi mitigasi yang sesuai berdasarkan hasil audit.
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Komentar Auditor (Auditor Comment) <span
                                            class="text-danger">*</span></label>
                                    <textarea name="komentar_auditor" class="form-control" rows="5" required
                                        placeholder="Komentar, catatan, atau rekomendasi dari Auditor...">{{ old('komentar_auditor', $hasilAudit->komentar_1 ?? '') }}</textarea>
                                    <small class="form-text text-muted">
                                        Berikan komentar atau rekomendasi untuk perbaikan berkelanjutan.
                                    </small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Status Konfirmasi Auditor (Auditor Confirmation Status)
                                        <span class="text-danger">*</span></label>
                                    <select name="status_konfirmasi_auditor" class="form-control" required
                                        id="selectStatusAuditor">
                                        <option value="">-- Pilih Status Konfirmasi --</option>
                                        <option value="Completed"
                                            {{ old('status_konfirmasi_auditor', $peta->status_konfirmasi_auditor) == 'Completed' ? 'selected' : '' }}>
                                            ✅ Completed (Audit Selesai)
                                        </option>
                                        <option value="Not Completed"
                                            {{ old('status_konfirmasi_auditor', $peta->status_konfirmasi_auditor) == 'Not Completed' ? 'selected' : '' }}>
                                            ⚠️ Not Completed (Perlu Tindak Lanjut dari Auditee)
                                        </option>
                                    </select>
                                    <small class="form-text text-muted" id="helpTextStatus">
                                        <i class="fas fa-info-circle"></i> Pilih <strong>Completed</strong> jika audit
                                        selesai dan tidak perlu revisi.
                                        Pilih <strong>Not Completed</strong> jika Auditee perlu melakukan tindak lanjut.
                                    </small>
                                </div>

                                <div class="alert alert-warning" id="alertNotCompleted" style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>PERHATIAN:</strong> Jika Anda memilih <strong>Not Completed</strong>,
                                    Auditee akan mendapat akses untuk melakukan revisi atau tindak lanjut terhadap hasil
                                    audit ini.
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save"></i> Simpan Hasil Audit
                                    </button>
                                    <a href="{{ route('manajemen-risiko.auditor.index') }}"
                                        class="btn btn-secondary btn-lg px-5">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif($isAuditee && $viewMode === 'answer_questions')
                    {{-- ========================================
                     SECTION 2: UNIT KERJA JAWAB PERTANYAAN
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-edit"></i> Jawab Pertanyaan Pemeriksaan</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="answer_questions">

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Jawab semua pertanyaan dengan lengkap dan
                                    akurat.
                                    Sertakan <strong>link dokumen pendukung</strong> jika diperlukan untuk verifikasi.
                                </div>

                                @if (!empty($questions))
                                    @foreach ($questions as $index => $q)
                                        <div class="question-answer-item mb-4 p-4 border rounded bg-light">
                                            <h6 class="text-primary font-weight-bold">Pertanyaan {{ $index + 1 }}</h6>
                                            <div class="p-3 bg-white mb-3 rounded border">
                                                {{ $q['question'] ?? '-' }}
                                            </div>

                                            <label class="font-weight-bold">Jawaban <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="answers[{{ $index }}][answer]" class="form-control mb-3" rows="4" required
                                                placeholder="Tulis jawaban Anda..."></textarea>

                                            <label class="font-weight-bold">Link Data Dukung (URL)</label>
                                            <div class="links-container-{{ $index }}">
                                                <div class="input-group mb-2">
                                                    <input type="url" name="answers[{{ $index }}][links][]"
                                                        class="form-control" placeholder="https://example.com/dokumen1">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger remove-link">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-info add-link"
                                                data-index="{{ $index }}">
                                                <i class="fas fa-plus"></i> Tambah Link
                                            </button>

                                            <label class="font-weight-bold mt-3">Catatan Tambahan (opsional)</label>
                                            <textarea name="answers[{{ $index }}][notes]" class="form-control" rows="2"
                                                placeholder="Catatan tambahan jika diperlukan..."></textarea>
                                        </div>
                                    @endforeach

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-paper-plane"></i> Submit Jawaban ke Auditor
                                        </button>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        Belum ada pertanyaan dari Auditor.
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                @elseif($isAuditee && $viewMode === 'do_revision')
                    {{-- ========================================
                     SECTION 2B: UNIT KERJA MELAKUKAN PERBAIKAN
                    ======================================== --}}
                    @php
                        $revisiData = $peta->catatan_revisi ? json_decode($peta->catatan_revisi, true) : null;
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-redo"></i> Perbaikan Jawaban Pemeriksaan</h5>
                        </div>
                        <div class="card-body">
                            @if ($revisiData)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Auditor meminta Anda untuk melakukan perbaikan!</strong>
                                    <p class="mb-2 mt-2"><strong>Catatan Umum dari Auditor:</strong></p>
                                    <div class="p-2 bg-white rounded">{{ $revisiData['catatan_umum'] ?? '-' }}</div>
                                    <small class="d-block mt-2 text-muted">
                                        <i class="fas fa-clock"></i> Dikirim oleh
                                        {{ $revisiData['sent_by'] ?? 'Auditor' }} pada
                                        {{ date('d M Y H:i', strtotime($revisiData['sent_at'] ?? now())) }}
                                    </small>
                                </div>

                                <form action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="submit_revision">

                                    @if (!empty($questions))
                                        @foreach ($questions as $index => $q)
                                            @php
                                                $response = $responses[$index] ?? null;
                                                $needRevisi = false;
                                                $catatanRevisi = '';

                                                // Cek apakah pertanyaan ini perlu direvisi
                                                if (isset($revisiData['items'])) {
                                                    foreach ($revisiData['items'] as $item) {
                                                        if (($item['pertanyaan_no'] ?? -1) == $index) {
                                                            $needRevisi = true;
                                                            $catatanRevisi = $item['catatan'] ?? '';
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <div
                                                class="question-answer-item mb-4 p-4 border rounded {{ $needRevisi ? 'bg-warning-light border-warning' : 'bg-light' }}">
                                                @if ($needRevisi)
                                                    <div class="badge badge-warning mb-2">
                                                        <i class="fas fa-exclamation-circle"></i> PERLU PERBAIKAN
                                                    </div>
                                                @endif

                                                <h6 class="text-primary font-weight-bold">Pertanyaan {{ $index + 1 }}
                                                </h6>
                                                <div class="p-3 bg-white mb-3 rounded border">
                                                    {{ $q['question'] ?? '-' }}
                                                </div>

                                                @if ($needRevisi && $catatanRevisi)
                                                    <div class="alert alert-danger">
                                                        <strong><i class="fas fa-info-circle"></i> Catatan Perbaikan dari
                                                            Auditor:</strong>
                                                        <p class="mb-0 mt-1">{{ $catatanRevisi }}</p>
                                                    </div>
                                                @endif

                                                <div class="mb-3">
                                                    <strong>Jawaban Sebelumnya:</strong>
                                                    <div class="p-2 bg-light rounded border text-muted">
                                                        {{ $response['answer'] ?? 'Belum ada jawaban' }}
                                                    </div>
                                                </div>

                                                <label class="font-weight-bold">Jawaban Perbaikan <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="answers[{{ $index }}][answer]" class="form-control mb-3" rows="4" required
                                                    placeholder="Tulis jawaban perbaikan Anda...">{{ $response['answer'] ?? '' }}</textarea>

                                                <label class="font-weight-bold">Link Data Dukung (URL)</label>
                                                <div class="links-container-{{ $index }}">
                                                    @if (!empty($response['links']))
                                                        @foreach ($response['links'] as $linkIndex => $link)
                                                            @if ($link)
                                                                <div class="input-group mb-2">
                                                                    <input type="url"
                                                                        name="answers[{{ $index }}][links][]"
                                                                        class="form-control" value="{{ $link }}"
                                                                        placeholder="https://example.com/dokumen">
                                                                    <div class="input-group-append">
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-link">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="input-group mb-2">
                                                            <input type="url"
                                                                name="answers[{{ $index }}][links][]"
                                                                class="form-control"
                                                                placeholder="https://example.com/dokumen">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-danger remove-link">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-info add-link"
                                                    data-index="{{ $index }}">
                                                    <i class="fas fa-plus"></i> Tambah Link
                                                </button>

                                                <label class="font-weight-bold mt-3">Catatan Tambahan (opsional)</label>
                                                <textarea name="answers[{{ $index }}][notes]" class="form-control" rows="2"
                                                    placeholder="Catatan tambahan...">{{ $response['notes'] ?? '' }}</textarea>
                                            </div>
                                        @endforeach

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-success btn-lg px-5">
                                                <i class="fas fa-paper-plane"></i> Submit Perbaikan ke Auditor
                                            </button>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Belum ada pertanyaan dari Auditor.
                                        </div>
                                    @endif
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Data catatan perbaikan tidak ditemukan. Silakan hubungi Auditor.
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($isAuditor && $viewMode === 'review_answers')
                    {{-- ========================================
                     SECTION 3: AUDITOR VERIFIKASI JAWABAN
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Verifikasi Jawaban Unit Kerja</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="review_answers">

                                @if (!empty($questions) && !empty($responses))
                                    @foreach ($questions as $index => $q)
                                        @php
                                            $response = $responses[$index] ?? null;
                                        @endphp
                                        <div class="review-item mb-4 p-4 border rounded">
                                            <h6 class="text-primary font-weight-bold">{{ $index + 1 }}.
                                                {{ $q['question'] }}</h6>

                                            <div class="mt-3">
                                                <strong>Jawaban Unit Kerja:</strong>
                                                <div class="p-3 bg-light rounded">
                                                    {{ $response['answer'] ?? 'Belum dijawab' }}
                                                </div>
                                            </div>

                                            @if (!empty($response['links']))
                                                <div class="mt-2">
                                                    <strong>Data Dukung:</strong>
                                                    <ul class="list-unstyled ml-3">
                                                        @foreach ($response['links'] as $link)
                                                            @if ($link)
                                                                <li><a href="{{ $link }}" target="_blank"
                                                                        class="text-primary"><i class="fas fa-link"></i>
                                                                        {{ $link }}</a></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if (!empty($response['notes']))
                                                <div class="mt-2">
                                                    <strong>Catatan:</strong>
                                                    <div class="p-2 bg-white rounded border">{{ $response['notes'] }}
                                                    </div>
                                                </div>
                                            @endif

                                            <hr class="my-3">

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-weight-bold">Status Penilaian <span
                                                            class="text-danger">*</span></label>
                                                    <select name="penilaian[{{ $index }}][status]"
                                                        class="form-control" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="memadai">✅ Memadai</option>
                                                        <option value="kurang_memadai">⚠️ Kurang Memadai</option>
                                                        <option value="tidak_memadai">❌ Tidak Memadai</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-weight-bold">Skor (0-100)</label>
                                                    <input type="number" name="penilaian[{{ $index }}][skor]"
                                                        class="form-control" min="0" max="100"
                                                        placeholder="Opsional">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="font-weight-bold">Komentar Auditor</label>
                                                    <textarea name="penilaian[{{ $index }}][komentar]" class="form-control" rows="2"
                                                        placeholder="Komentar untuk Unit Kerja..."></textarea>
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                <label class="font-weight-bold">Rekomendasi</label>
                                                <textarea name="penilaian[{{ $index }}][rekomendasi]" class="form-control" rows="2"
                                                    placeholder="Rekomendasi untuk perbaikan..."></textarea>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="card bg-light mt-4">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold">Kesimpulan Pemeriksaan</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Pengendalian</label>
                                                    <textarea name="pengendalian" class="form-control" rows="3" placeholder="Deskripsi pengendalian risiko..."></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Mitigasi Risiko</label>
                                                    <textarea name="mitigasi" class="form-control" rows="3" placeholder="Rencana mitigasi risiko..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg px-5 mr-2">
                                            <i class="fas fa-check-circle"></i> Submit Verifikasi
                                        </button>
                                        <button type="button" class="btn btn-warning btn-lg px-5" data-toggle="modal"
                                            data-target="#modalKirimRevisi">
                                            <i class="fas fa-redo"></i> Minta Perbaikan
                                        </button>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        Belum ada jawaban dari Unit Kerja untuk diverifikasi.
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                @elseif($isAuditor && $peta->auditorCanConfirmRevision())
                    {{-- ========================================
                     SECTION 3B: AUDITOR KONFIRMASI PERBAIKAN UNIT KERJA
                    ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-check-circle"></i> Konfirmasi Hasil Perbaikan Unit Kerja
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Unit Kerja telah mengirim perbaikan!</strong>
                                <p class="mb-0 mt-2">Silakan periksa jawaban yang telah diperbaiki, kemudian konfirmasi
                                    jika sudah memadai.</p>
                            </div>

                            @if (!empty($questions))
                                @foreach ($questions as $index => $q)
                                    @php
                                        $response = $responses[$index] ?? null;
                                    @endphp
                                    <div class="mb-4 p-4 border rounded bg-light">
                                        <h6 class="text-primary font-weight-bold">{{ $index + 1 }}.
                                            {{ $q['question'] }}</h6>

                                        @if ($response)
                                            <div class="mt-3">
                                                <strong>Jawaban Perbaikan dari Unit Kerja:</strong>
                                                <div class="p-3 bg-white rounded border">
                                                    {{ $response['answer'] }}
                                                </div>
                                            </div>

                                            @if (!empty($response['links']))
                                                <div class="mt-2">
                                                    <strong>Data Dukung:</strong>
                                                    <ul class="list-unstyled ml-3">
                                                        @foreach ($response['links'] as $link)
                                                            @if ($link)
                                                                <li><a href="{{ $link }}" target="_blank"
                                                                        class="text-primary">
                                                                        <i class="fas fa-link"></i> {{ $link }}
                                                                    </a></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if (!empty($response['notes']))
                                                <div class="mt-2">
                                                    <strong>Catatan:</strong>
                                                    <div class="p-2 bg-light rounded border">{{ $response['notes'] }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($response['revised_at']))
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> Direvisi pada:
                                                        {{ date('d M Y H:i', strtotime($response['revised_at'])) }}
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-muted mt-2">Belum ada jawaban perbaikan.</p>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Form Konfirmasi Perbaikan --}}
                                <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                    method="POST" class="mt-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="confirm_revision">

                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Catatan Konfirmasi (Opsional)</label>
                                                <textarea name="catatan_konfirmasi" class="form-control" rows="3"
                                                    placeholder="Catatan atau tanggapan Anda terhadap perbaikan..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-check-circle"></i> Konfirmasi Perbaikan Unit Kerja
                                        </button>
                                        <a href="{{ route('manajemen-risiko.auditor.index') }}"
                                            class="btn btn-secondary btn-lg px-5">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    Belum ada pertanyaan untuk diverifikasi.
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($isAuditee)
                    {{-- ========================================
                     SECTION NEW: AUDITEE VIEW AUDIT RESULT & CONFIRMATION (SESUAI REQUIREMENT DOSEN)
                     ✅ STRICT GUARD: HANYA MUNCUL SETELAH AUDITOR SUBMIT HASIL AUDIT
                    ======================================== --}}

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
                            $hasilAudit &&
                            $hasilAudit->pengendalian &&
                            $hasilAudit->mitigasi
                        ) {
                            $auditorHasSubmitted = true;
                        }
                    @endphp

                    {{-- ========================================
                         CONDITIONAL RENDERING BASED ON AUDITOR SUBMISSION STATUS
                    ======================================== --}}

                    @if (!$auditorHasSubmitted)
                        {{-- ❌ AUDITOR BELUM SUBMIT: HANYA TAMPILKAN INFORMASI --}}
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-hourglass-half"></i> Status Pemeriksaan Audit
                                </h5>
                            </div>
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-clock text-info" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="text-info font-weight-bold mb-3">
                                    Menunggu Hasil Pemeriksaan dari Auditor
                                </h4>
                                <p class="text-muted mb-4">
                                    Auditor <strong>{{ $peta->auditor->name ?? 'yang ditugaskan' }}</strong>
                                    sedang melakukan proses pemeriksaan terhadap risiko ini.
                                    Silakan tunggu hingga Auditor menyelesaikan input hasil audit.
                                </p>
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    <strong>Informasi:</strong> Anda akan mendapat notifikasi dan akses untuk melihat
                                    hasil audit setelah Auditor menyelesaikan proses pemeriksaan.
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ✅ AUDITOR SUDAH SUBMIT: TAMPILKAN HASIL AUDIT & FORM KONFIRMASI --}}
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard-list"></i> Hasil Pemeriksaan Audit dari Auditor
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Auditor telah menyelesaikan pemeriksaan!</strong>
                                    <p class="mb-0 mt-2">
                                        Berikut adalah hasil pemeriksaan audit yang telah dilakukan oleh
                                        <strong>{{ $peta->auditor->name ?? 'Auditor' }}</strong>.
                                        Silakan periksa dengan teliti dan berikan konfirmasi di bawah.
                                    </p>
                                </div>

                                {{-- SECTION 1: HASIL AUDIT DARI AUDITOR (READ-ONLY) --}}
                                <div class="card bg-light mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-clipboard-check"></i> Hasil Audit (Read-Only)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">
                                                    <i class="fas fa-shield-alt"></i> Pengendalian Risiko (Risk Control)
                                                </label>
                                                <div class="p-3 bg-white border rounded" style="min-height: 100px;">
                                                    {{ $peta->pengendalian ?? ($hasilAudit->pengendalian ?? '-') }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="font-weight-bold">
                                                    <i class="fas fa-chart-line"></i> Mitigasi Risiko (Risk Mitigation)
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
                                                    <span
                                                        class="badge badge-{{ $mitigasiBadge }} p-2">{{ $mitigasi }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">
                                                <i class="fas fa-comment-dots"></i> Komentar Auditor (Auditor Comment)
                                            </label>
                                            <div class="p-3 bg-white border rounded"
                                                style="min-height: 100px; white-space: pre-wrap;">
                                                {{ $hasilAudit->komentar_1 ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="font-weight-bold">Status Konfirmasi Auditor</label>
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
                                                <label class="font-weight-bold">Level Risiko</label>
                                                <div class="mt-2">
                                                    <span class="badge badge-warning p-2">
                                                        {{ $hasilAudit->level_risiko ?? $levelText }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="font-weight-bold">Skor Total</label>
                                                <div class="mt-2">
                                                    <span class="badge badge-secondary p-2">
                                                        {{ $hasilAudit->skor_total ?? $skorTotal }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECTION 2: CONDITIONAL AUDITEE ACTION BASED ON AUDITOR STATUS --}}
                                @if ($peta->status_konfirmasi_auditor == 'Completed')
                                    {{-- ✅ JIKA AUDITOR STATUS = COMPLETED: HANYA APPROVAL --}}
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-check-circle"></i> Konfirmasi Akhir (Approval Only)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle"></i>
                                                <strong>Status Audit: SELESAI</strong>
                                                <p class="mb-0 mt-2">
                                                    Auditor telah menyatakan bahwa proses audit untuk risiko ini telah
                                                    <strong>SELESAI</strong>.
                                                    Tidak ada revisi yang diperlukan. Silakan lakukan <strong>konfirmasi
                                                        akhir</strong>
                                                    (approve) untuk menyelesaikan proses dari sisi Unit Kerja.
                                                </p>
                                            </div>

                                            <form
                                                action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="final_approval">

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Catatan Konfirmasi (Opsional)</label>
                                                    <textarea name="catatan_auditee" class="form-control" rows="3"
                                                        placeholder="Tanggapan atau catatan Anda terhadap hasil audit..."></textarea>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle"></i> Catatan ini akan tersimpan
                                                        sebagai riwayat aktivitas.
                                                    </small>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                                        <i class="fas fa-check-double"></i> Approve & Konfirmasi Hasil
                                                        Audit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($peta->status_konfirmasi_auditor == 'Not Completed')
                                    {{-- ⚠️ JIKA AUDITOR STATUS = NOT COMPLETED: FORM REVISI --}}
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0">
                                                <i class="fas fa-exclamation-triangle"></i> Tindak Lanjut & Revisi
                                                Diperlukan
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Status Audit: BELUM SELESAI</strong>
                                                <p class="mb-0 mt-2">
                                                    Auditor menyatakan bahwa audit <strong>BELUM SELESAI</strong> dan
                                                    memerlukan
                                                    <strong>tindak lanjut</strong> dari Unit Kerja. Silakan lakukan
                                                    perbaikan atau
                                                    revisi sesuai catatan/komentar Auditor di atas.
                                                </p>
                                            </div>

                                            <form
                                                action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="submit_follow_up">

                                                <div class="form-group">
                                                    <label class="font-weight-bold">
                                                        Catatan Tindak Lanjut / Revisi <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="catatan_tindak_lanjut" class="form-control" rows="5" required
                                                        placeholder="Jelaskan tindak lanjut atau perbaikan yang telah/akan dilakukan berdasarkan komentar Auditor..."></textarea>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle"></i> Jelaskan secara detail
                                                        langkah-langkah perbaikan yang Anda lakukan.
                                                    </small>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">
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
                                                        Pilih "Completed" jika tindak lanjut sudah selesai.
                                                        Pilih "Not Completed" jika masih dalam proses.
                                                    </small>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                                        <i class="fas fa-paper-plane"></i> Submit Tindak Lanjut ke Auditor
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    {{-- ℹ️ FALLBACK: AUDITOR BELUM SET STATUS (SEHARUSNYA TIDAK TERJADI) --}}
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Status Tidak Lengkap</strong>
                                        <p class="mb-0 mt-2">
                                            Auditor telah menginput hasil audit, tetapi belum menentukan status konfirmasi.
                                            Silakan hubungi Auditor untuk melengkapi status pemeriksaan.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    {{-- ========================================
                     SECTION 5: READ-ONLY (FINAL / DEFAULT)
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Pemeriksaan Manajemen Risiko (Hanya Baca)
                            </h5>
                        </div>
                        <div class="card-body">
                            @if (!empty($questions))
                                @foreach ($questions as $index => $q)
                                    @php
                                        $response = $responses[$index] ?? null;
                                        $penilaian = $penilaianAuditor[$index] ?? null;
                                    @endphp
                                    <div class="mb-4 p-4 border rounded">
                                        <h6 class="text-primary font-weight-bold">{{ $index + 1 }}.
                                            {{ $q['question'] }}</h6>

                                        @if ($response)
                                            <div class="mt-3">
                                                <strong>Jawaban:</strong>
                                                <div class="p-3 bg-light rounded">{{ $response['answer'] }}</div>
                                            </div>

                                            @if (!empty($response['links']))
                                                <div class="mt-2">
                                                    <strong>Data Dukung:</strong>
                                                    <ul class="list-unstyled ml-3">
                                                        @foreach ($response['links'] as $link)
                                                            @if ($link)
                                                                <li><a href="{{ $link }}" target="_blank"
                                                                        class="text-primary"><i class="fas fa-link"></i>
                                                                        {{ $link }}</a></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if ($penilaian)
                                                <hr class="my-3">
                                                <div class="bg-light p-3 rounded">
                                                    <strong>Penilaian Auditor:</strong>
                                                    @php
                                                        $statusBadgeReview = 'secondary';
                                                        if (($penilaian['status'] ?? '') === 'memadai') {
                                                            $statusBadgeReview = 'success';
                                                        } elseif (($penilaian['status'] ?? '') === 'kurang_memadai') {
                                                            $statusBadgeReview = 'warning';
                                                        } elseif (($penilaian['status'] ?? '') === 'tidak_memadai') {
                                                            $statusBadgeReview = 'danger';
                                                        }
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $statusBadgeReview }} p-2">{{ ucfirst(str_replace('_', ' ', $penilaian['status'] ?? '-')) }}</span>
                                                    @if (!empty($penilaian['komentar']))
                                                        <div class="mt-2"><strong>Komentar:</strong>
                                                            {{ $penilaian['komentar'] }}</div>
                                                    @endif
                                                    @if (!empty($penilaian['rekomendasi']))
                                                        <div class="mt-2"><strong>Rekomendasi:</strong>
                                                            {{ $penilaian['rekomendasi'] }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <p class="text-muted mt-2">Belum ada jawaban.</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    Belum ada pertanyaan pemeriksaan yang diinput.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Riwayat Aktivitas --}}
                @if ($peta->comment_prs->count() > 0)
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Aktivitas Pemeriksaan</h5>
                        </div>
                        <div class="card-body">
                            <ul class="timeline">
                                @foreach ($peta->comment_prs->sortByDesc('created_at') as $comment)
                                    <li class="mb-3">
                                        <div class="d-flex">
                                            <div class="mr-3">
                                                <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong>{{ $comment->user->name ?? 'System' }}</strong>
                                                <span
                                                    class="text-muted ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                                <p class="mb-0 mt-1">{{ $comment->comment }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
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

                                <div class="form-group">
                                    <label class="font-weight-bold">Catatan Finalisasi (Opsional)</label>
                                    <textarea name="catatan_finalisasi" class="form-control" rows="3"
                                        placeholder="Catatan atau keterangan tambahan untuk finalisasi pemeriksaan ini..."></textarea>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Catatan ini akan tersimpan sebagai dokumen
                                        resmi.
                                    </small>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>PERHATIAN:</strong> Setelah pemeriksaan difinalisasi:
                                    <ul class="mb-0 mt-2">
                                        <li>Status akan berubah menjadi <span class="badge badge-dark">FINAL</span></li>
                                        <li>Semua data akan <strong>READ-ONLY</strong> (tidak dapat diubah)</li>
                                        <li>Pemeriksaan dianggap <strong>SELESAI RESMI</strong></li>
                                        <li>Proses tidak dapat dibatalkan</li>
                                    </ul>
                                </div>

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
                                <li>Status pemeriksaan akan berubah menjadi <span class="badge badge-dark">FINAL</span>
                                </li>
                                <li>Semua data akan <strong>LOCKED</strong> (tidak dapat diubah)</li>
                                <li>Pemeriksaan dianggap <strong>SELESAI RESMI</strong></li>
                                <li><strong>Proses TIDAK DAPAT dibatalkan</strong></li>
                            </ul>
                        </div>
                        <div class="alert alert-info">
                            <strong>Detail Pemeriksaan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Unit Kerja: <strong>{{ $peta->jenis }}</strong></li>
                                <li>Kode Risiko: <strong>{{ $peta->kode_regist }}</strong></li>
                                <li>Auditor: <strong>{{ $peta->auditor->name ?? '-' }}</strong></li>
                                <li>Kegiatan: <strong>{{ $peta->kegiatan->judul ?? $peta->judul }}</strong></li>
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
         MODAL: MINTA PERBAIKAN (AUDITOR)
    ======================================== --}}
    @if ($isAuditor && $viewMode === 'review_answers')
        <div class="modal fade" id="modalKirimRevisi" tabindex="-1" role="dialog"
            aria-labelledby="modalKirimRevisiLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="modalKirimRevisiLabel">
                            <i class="fas fa-redo"></i> Minta Perbaikan kepada Unit Kerja
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('manajemen-risiko.auditor.send-revision', $peta->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-primary">
                                <i class="fas fa-info-circle"></i> Tandai pertanyaan yang perlu diperbaiki oleh Unit Kerja.
                                Sistem akan mengirim notifikasi dan Unit Kerja dapat melakukan perbaikan jawaban.
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Catatan Perbaikan Umum <span
                                        class="text-danger">*</span></label>
                                <textarea name="catatan_revisi" class="form-control" rows="3" required
                                    placeholder="Jelaskan secara umum aspek yang perlu diperbaiki oleh Unit Kerja..."></textarea>
                            </div>

                            <hr>

                            <h6 class="font-weight-bold mb-3">Tandai Item yang Memerlukan Perbaikan:</h6>

                            <div id="revisiItemsContainer">
                                @foreach ($questions as $index => $q)
                                    <div class="form-check mb-3 p-3 border rounded">
                                        <div class="d-flex align-items-start">
                                            <input class="form-check-input mt-1" type="checkbox"
                                                name="revisi_check[{{ $index }}]"
                                                id="revisi{{ $index }}" value="1">
                                            <label class="form-check-label ml-2 flex-grow-1"
                                                for="revisi{{ $index }}">
                                                <strong>Pertanyaan {{ $index + 1 }}:</strong> {{ $q['question'] }}
                                            </label>
                                        </div>
                                        <div class="mt-2 ml-4" id="catatanRevisi{{ $index }}"
                                            style="display:none;">
                                            <input type="hidden"
                                                name="items_revisi[{{ $index }}][pertanyaan_no]"
                                                value="{{ $index }}">
                                            <label class="font-weight-bold text-danger">Catatan Perbaikan <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="items_revisi[{{ $index }}][catatan]" class="form-control catatan-revisi-input"
                                                rows="2" placeholder="Jelaskan apa yang perlu diperbaiki untuk pertanyaan ini..."></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane"></i> Kirim Permintaan Perbaikan
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

        // ✅ Script untuk Auditor - Tambah/Hapus Pertanyaan dengan auto-update nomor
        function updateQuestionNumbers() {
            $('.question-item').each(function(index) {
                $(this).find('label.font-weight-bold').first().text('Pertanyaan ' + (index + 1));
                // Update name attribute untuk textarea
                $(this).find('textarea').attr('name', 'questions[' + index + '][question]');
            });
        }

        $('#addQuestion').on('click', function() {
            const currentCount = $('.question-item').length;
            const newQuestion = `
            <div class="question-item mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="font-weight-bold">Pertanyaan ${currentCount + 1}</label>
                    <button type="button" class="btn btn-sm btn-danger remove-question">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
                <textarea name="questions[${currentCount}][question]" class="form-control" rows="3" required placeholder="Tulis pertanyaan pemeriksaan..."></textarea>
            </div>
        `;
            $('#questionsContainer').append(newQuestion);
        });

        $(document).on('click', '.remove-question', function() {
            if ($('.question-item').length > 1) {
                $(this).closest('.question-item').remove();
                // ✅ Update penomoran setelah hapus
                updateQuestionNumbers();
            } else {
                alert('Minimal harus ada 1 pertanyaan!');
            }
        });

        // Script untuk Unit Kerja - Tambah/Hapus Link
        $(document).on('click', '.add-link', function() {
            const index = $(this).data('index');
            const newLink = `
            <div class="input-group mb-2">
                <input type="url" name="answers[${index}][links][]" class="form-control" placeholder="https://example.com/dokumen">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-link">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
            $(`.links-container-${index}`).append(newLink);
        });

        $(document).on('click', '.remove-link', function() {
            $(this).closest('.input-group').remove();
        });

        // ✅ Script untuk Modal Perbaikan - Toggle checkbox
        $(document).on('change', 'input[name^="revisi_check"]', function() {
            const index = $(this).attr('id').replace('revisi', '');
            const catatanDiv = $('#catatanRevisi' + index);
            const catatanInput = catatanDiv.find('.catatan-revisi-input');

            if ($(this).is(':checked')) {
                catatanDiv.slideDown();
                catatanInput.prop('required', true);
            } else {
                catatanDiv.slideUp();
                catatanInput.prop('required', false);
                catatanInput.val('');
            }
        });

        // ✅ Validasi form perbaikan sebelum submit
        $('form[action*="send-revision"]').on('submit', function(e) {
            const checkedItems = $('input[name^="revisi_check"]:checked').length;

            if (checkedItems === 0) {
                e.preventDefault();
                alert('Silakan tandai minimal 1 pertanyaan yang perlu diperbaiki!');
                return false;
            }

            // Validasi setiap checkbox yang dicentang harus punya catatan
            let isValid = true;
            $('input[name^="revisi_check"]:checked').each(function() {
                const index = $(this).attr('id').replace('revisi', '');
                const catatan = $(`textarea[name="items_revisi[${index}][catatan]"]`).val().trim();

                if (catatan === '') {
                    isValid = false;
                    $(`textarea[name="items_revisi[${index}][catatan]"]`).addClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Silakan isi catatan perbaikan untuk setiap item yang ditandai!');
                return false;
            }
        });
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

        .timeline {
            list-style: none;
            padding-left: 0;
        }

        .badge {
            font-size: 14px;
        }
    </style>
@endpush
