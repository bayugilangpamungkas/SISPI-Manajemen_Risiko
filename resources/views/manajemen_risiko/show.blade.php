@extends('layout.app')
@section('title', 'Detail Audit Wawancara')

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
                        AUDIT WAWANCARA MANAJEMEN RISIKO
                    </h1>
                    <div style="font-size:15px; line-height: 1.2;">
                        <span class="d-block">SATUAN PENGAWAS INTERNAL</span>
                        <span>POLITEKNIK NEGERI MALANG</span>
                    </div>
                </div>
            </div>

            <div class="section-body mt-4">
                {{-- Alert Status --}}
                <div class="alert alert-{{ str_replace('badge-', '', $statusBadge) }} border-left-primary mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x mr-3"></i>
                        <div>
                            <strong class="text-white">Status Audit:</strong> <span
                                class="badge {{ $statusBadge }} p-2">{{ $statusLabel }}</span>
                            <br>
                            <small class="text-muted">
                                @if ($statusAudit === 'menunggu_wawancara')
                                    Menunggu Auditor menginput daftar pertanyaan audit.
                                @elseif($statusAudit === 'menunggu_jawaban')
                                    Menunggu Auditee menjawab pertanyaan audit.
                                @elseif($statusAudit === 'menunggu_review')
                                    Menunggu Auditor melakukan review terhadap jawaban.
                                @elseif($statusAudit === 'selesai_review')
                                    Menunggu konfirmasi dari Auditee terhadap hasil review.
                                @elseif($statusAudit === 'final')
                                    Audit telah selesai dan data telah difinalisasi.
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
                                        <th>Kode Risiko</th>
                                        <td>: {{ $peta->kode_regist ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kegiatan</th>
                                        <td>: {{ $peta->kegiatan->judul ?? $peta->judul }}</td>
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
                     SECTION 1: AUDITOR INPUT PERTANYAAN
                ======================================== --}}
                @if ($isAuditor && $viewMode === 'input_questions')
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-question-circle"></i> Input Daftar Pertanyaan Audit</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                method="POST" id="formInputPertanyaan">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="input_questions">

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Silakan input daftar pertanyaan audit yang akan
                                    dijawab oleh Auditee. Pertanyaan bersifat <strong>READ-ONLY</strong> untuk Auditee.
                                </div>

                                <div id="questionsContainer">
                                    @if (!empty($questions))
                                        @foreach ($questions as $index => $q)
                                            <div class="question-item mb-3 p-3 border rounded">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="font-weight-bold">Pertanyaan {{ $index + 1 }}</label>
                                                    <button type="button" class="btn btn-sm btn-danger remove-question">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </div>
                                                <textarea name="questions[{{ $index }}][question]" class="form-control" rows="3" required
                                                    placeholder="Tulis pertanyaan audit...">{{ $q['question'] ?? '' }}</textarea>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="question-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="font-weight-bold">Pertanyaan 1</label>
                                                <button type="button" class="btn btn-sm btn-danger remove-question">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                            <textarea name="questions[0][question]" class="form-control" rows="3" required
                                                placeholder="Tulis pertanyaan audit..."></textarea>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-success mb-3" id="addQuestion">
                                    <i class="fas fa-plus"></i> Tambah Pertanyaan
                                </button>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save"></i> Submit Pertanyaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif($isAuditee && $viewMode === 'answer_questions')
                    {{-- ========================================
                     SECTION 2: AUDITEE JAWAB PERTANYAAN
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-edit"></i> Jawab Pertanyaan Audit</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="answer_questions">

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Jawab semua pertanyaan dengan lengkap.
                                    Sertakan <strong>link data dukung</strong> jika diperlukan.
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
                                            <i class="fas fa-paper-plane"></i> Submit Jawaban
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
                     SECTION 2B: AUDITEE MELAKUKAN REVISI
                    ======================================== --}}
                    @php
                        $revisiData = $peta->catatan_revisi ? json_decode($peta->catatan_revisi, true) : null;
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-redo"></i> Revisi Jawaban Audit</h5>
                        </div>
                        <div class="card-body">
                            @if ($revisiData)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Auditor meminta Anda untuk melakukan revisi!</strong>
                                    <p class="mb-2 mt-2"><strong>Catatan Umum:</strong></p>
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
                                                        <i class="fas fa-exclamation-circle"></i> PERLU REVISI
                                                    </div>
                                                @endif

                                                <h6 class="text-primary font-weight-bold">Pertanyaan {{ $index + 1 }}
                                                </h6>
                                                <div class="p-3 bg-white mb-3 rounded border">
                                                    {{ $q['question'] ?? '-' }}
                                                </div>

                                                @if ($needRevisi && $catatanRevisi)
                                                    <div class="alert alert-danger">
                                                        <strong><i class="fas fa-info-circle"></i> Catatan Revisi dari
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

                                                <label class="font-weight-bold">Jawaban Revisi <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="answers[{{ $index }}][answer]" class="form-control mb-3" rows="4" required
                                                    placeholder="Tulis jawaban revisi Anda...">{{ $response['answer'] ?? '' }}</textarea>

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
                                                <i class="fas fa-paper-plane"></i> Submit Revisi
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
                                    Data revisi tidak ditemukan. Silakan hubungi Auditor.
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($isAuditor && $viewMode === 'review_answers')
                    {{-- ========================================
                     SECTION 3: AUDITOR REVIEW JAWABAN
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Review Jawaban Auditee</h5>
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
                                                <strong>Jawaban Auditee:</strong>
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
                                                        placeholder="Komentar untuk auditee..."></textarea>
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
                                            <h6 class="font-weight-bold">Kesimpulan Audit</h6>
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
                                            <i class="fas fa-check-circle"></i> Submit
                                        </button>
                                        <button type="button" class="btn btn-warning btn-lg px-5" data-toggle="modal"
                                            data-target="#modalKirimRevisi">
                                            <i class="fas fa-redo"></i> Kirim Revisi
                                        </button>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        Belum ada jawaban dari Auditee untuk direview.
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                @elseif($isAuditor && $peta->auditorCanConfirmRevision())
                    {{-- ========================================
                     SECTION 3B: AUDITOR KONFIRMASI REVISI AUDITEE
                    ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-check-circle"></i> Konfirmasi Hasil Revisi Auditee</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Auditee telah mengirim revisi!</strong>
                                <p class="mb-0 mt-2">Silakan periksa jawaban yang telah diperbaiki, kemudian konfirmasi
                                    jika sudah sesuai.</p>
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
                                                <strong>Jawaban Revisi dari Auditee:</strong>
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
                                            <p class="text-muted mt-2">Belum ada jawaban revisi.</p>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Form Konfirmasi Revisi --}}
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
                                                    placeholder="Catatan atau tanggapan Anda terhadap revisi..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-check-circle"></i> Konfirmasi Revisi Auditee
                                        </button>
                                        <a href="{{ route('manajemen-risiko.auditor.index') }}"
                                            class="btn btn-secondary btn-lg px-5">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    Belum ada pertanyaan untuk direview.
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($isAuditee && $viewMode === 'confirm_review')
                    {{-- ========================================
                     SECTION 4: AUDITEE KONFIRMASI REVIEW
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-check-double"></i> Konfirmasi Hasil Review</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Auditor telah menyelesaikan review!</strong>
                                <p class="mb-0 mt-2">Silakan periksa hasil review dari auditor, kemudian konfirmasi jika
                                    Anda setuju dengan hasilnya.</p>
                            </div>

                            @if (!empty($questions))
                                {{-- Tampilkan hasil review --}}
                                @foreach ($questions as $index => $q)
                                    @php
                                        $penilaian = $penilaianAuditor[$index] ?? null;
                                        $response = $responses[$index] ?? null;
                                    @endphp
                                    <div class="mb-4 p-4 border rounded bg-light">
                                        <h6 class="text-primary font-weight-bold">{{ $index + 1 }}.
                                            {{ $q['question'] }}</h6>

                                        <div class="mt-2">
                                            <strong>Jawaban Anda:</strong>
                                            <div class="p-2 bg-white rounded border">{{ $response['answer'] ?? '-' }}
                                            </div>
                                        </div>

                                        @if ($penilaian)
                                            <hr>

                                            <div class="mt-2">
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
                                                    class="badge badge-{{ $statusBadgeReview }} p-2 ml-2">{{ ucfirst(str_replace('_', ' ', $penilaian['status'] ?? 'Belum dinilai')) }}</span>
                                                @if (!empty($penilaian['skor']))
                                                    <span class="ml-2">Skor:
                                                        <strong>{{ $penilaian['skor'] }}</strong></span>
                                                @endif
                                            </div>

                                            @if (!empty($penilaian['komentar']))
                                                <div class="mt-2">
                                                    <strong>Komentar:</strong>
                                                    <div class="p-2 bg-light rounded border">{{ $penilaian['komentar'] }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($penilaian['rekomendasi']))
                                                <div class="mt-2">
                                                    <strong>Rekomendasi:</strong>
                                                    <div class="p-2 bg-warning-light rounded border">
                                                        {{ $penilaian['rekomendasi'] }}
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <hr>
                                            <div class="alert alert-info mt-2">
                                                <i class="fas fa-info-circle"></i> Auditor belum memberikan penilaian
                                                detail untuk pertanyaan ini.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Informasi Hasil Audit Umum --}}
                                @if ($hasilAudit)
                                    <div class="card bg-light mt-4">
                                        <div class="card-header">
                                            <h6 class="mb-0 font-weight-bold"><i class="fas fa-clipboard-list"></i>
                                                Kesimpulan Audit</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <strong>Pengendalian Risiko:</strong>
                                                    <div class="p-2 bg-white rounded border mt-1">
                                                        {{ $hasilAudit->pengendalian ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong>Mitigasi Risiko:</strong>
                                                    <div class="p-2 bg-white rounded border mt-1">
                                                        {{ $hasilAudit->mitigasi ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Level Risiko:</strong>
                                                    <span
                                                        class="badge badge-warning p-2 ml-2">{{ $hasilAudit->level_risiko ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Risiko Residual:</strong>
                                                    <span
                                                        class="badge badge-info p-2 ml-2">{{ $hasilAudit->risiko_residual ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Skor Total:</strong>
                                                    <span
                                                        class="badge badge-secondary p-2 ml-2">{{ $hasilAudit->skor_total ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <form action="{{ route('manajemen-risiko.auditee.submit-response', $peta->id) }}"
                                    method="POST" class="mt-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="confirm_review">

                                    <div class="form-group">
                                        <label class="font-weight-bold">Catatan Konfirmasi (Opsional)</label>
                                        <textarea name="catatan_auditee" class="form-control" rows="3"
                                            placeholder="Tanggapan atau catatan Anda terhadap hasil review..."></textarea>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Catatan ini akan tersimpan sebagai riwayat
                                            aktivitas.
                                        </small>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-check-circle"></i> Konfirmasi Hasil Review
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Belum ada pertanyaan dari Auditor.
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- ========================================
                     SECTION 5: READ-ONLY (FINAL / DEFAULT)
                ======================================== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Audit Wawancara (Read-Only)</h5>
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
                                    Belum ada pertanyaan audit yang diinput.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Riwayat Aktivitas --}}
                @if ($peta->comment_prs->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Riwayat Aktivitas</h5>
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
                     TOMBOL FINALISASI AUDIT (ADMIN/AUDITOR)
                ======================================== --}}
                @if (($isAdmin || $isAuditor) && $peta->canBeFinalized())
                    <div class="card border-success mt-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-lock"></i> Finalisasi Audit
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Audit siap difinalisasi!</strong>
                                <p class="mb-2 mt-2">Auditee telah mengkonfirmasi hasil audit. Anda dapat memfinalisasi
                                    audit ini untuk mengunci semua data dan menyelesaikan proses audit secara resmi.</p>
                                <ul class="mb-0">
                                    <li>Status saat ini: <span class="badge badge-info p-2">{{ $statusLabel }}</span>
                                    </li>
                                    <li>Auditee: <strong>{{ $peta->jenis }}</strong></li>
                                    <li>Auditor: <strong>{{ $peta->auditor->name ?? '-' }}</strong></li>
                                    <li>Kode Risiko: <strong>{{ $peta->kode_regist }}</strong></li>
                                </ul>
                            </div>

                            <form action="{{ route('manajemen-risiko.finalisasi', $peta->id) }}" method="POST"
                                id="formFinalisasi">
                                @csrf

                                <div class="form-group">
                                    <label class="font-weight-bold">Catatan Finalisasi (Opsional)</label>
                                    <textarea name="catatan_finalisasi" class="form-control" rows="3"
                                        placeholder="Catatan atau keterangan tambahan untuk finalisasi audit ini..."></textarea>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Catatan ini akan tersimpan sebagai riwayat
                                        aktivitas.
                                    </small>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>PERHATIAN:</strong> Setelah audit difinalisasi:
                                    <ul class="mb-0 mt-2">
                                        <li>Status akan berubah menjadi <span class="badge badge-dark">FINAL</span></li>
                                        <li>Semua data akan <strong>READ-ONLY</strong> (tidak dapat diubah)</li>
                                        <li>Audit dianggap <strong>SELESAI RESMI</strong></li>
                                        <li>Proses tidak dapat dibatalkan</li>
                                    </ul>
                                </div>

                                <div class="text-center">
                                    <button type="button" class="btn btn-success btn-lg px-5" data-toggle="modal"
                                        data-target="#modalKonfirmasiFinalisasi">
                                        <i class="fas fa-lock"></i> Finalisasi Audit Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- ✅ BADGE STATUS DISETUJUI AUDITEE (untuk Admin/Auditor) --}}
                @if (($isAdmin || $isAuditor) && $statusAudit === 'disetujui_auditee')
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Status Audit:</strong> Auditee telah mengkonfirmasi hasil audit.
                        Silakan <strong>finalisasi audit</strong> untuk menyelesaikan proses secara resmi.
                    </div>
                @endif

                {{-- ✅ BADGE AUDIT FINAL (untuk semua user) --}}
                @if ($statusAudit === 'final')
                    <div class="alert alert-dark mt-4">
                        <i class="fas fa-lock"></i>
                        <strong>Audit Telah Selesai!</strong> Data audit telah difinalisasi pada
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
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalKonfirmasiFinalisasiLabel">
                            <i class="fas fa-lock"></i> Konfirmasi Finalisasi Audit
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-center mb-3">Apakah Anda yakin ingin memfinalisasi audit ini?</h5>
                        <div class="alert alert-warning">
                            <strong>Konsekuensi finalisasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Status audit akan berubah menjadi <span class="badge badge-dark">FINAL</span></li>
                                <li>Semua data akan <strong>LOCKED</strong> (tidak dapat diubah)</li>
                                <li>Audit dianggap <strong>SELESAI RESMI</strong></li>
                                <li><strong>Proses TIDAK DAPAT dibatalkan</strong></li>
                            </ul>
                        </div>
                        <div class="alert alert-info">
                            <strong>Detail Audit:</strong>
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
         MODAL: KIRIM REVISI (AUDITOR)
    ======================================== --}}
    @if ($isAuditor && $viewMode === 'review_answers')
        <div class="modal fade" id="modalKirimRevisi" tabindex="-1" role="dialog"
            aria-labelledby="modalKirimRevisiLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="modalKirimRevisiLabel">
                            <i class="fas fa-redo"></i> Revisi
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('manajemen-risiko.auditor.send-revision', $peta->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Tandai pertanyaan yang perlu direvisi oleh Auditee.
                                Sistem akan mengirim notifikasi dan Auditee dapat melakukan perbaikan jawaban.
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Catatan Revisi <span class="text-danger">*</span></label>
                                <textarea name="catatan_revisi" class="form-control" rows="3" required
                                    placeholder="Jelaskan secara umum apa yang perlu diperbaiki oleh Auditee..."></textarea>
                            </div>

                            <hr>

                            <h6 class="font-weight-bold mb-3">Tandai Item yang Perlu Direvisi:</h6>

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
                                            <label class="font-weight-bold text-danger">Catatan Revisi <span
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
                                <i class="fas fa-paper-plane"></i> Kirim Revisi ke Auditee
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
                <textarea name="questions[${currentCount}][question]" class="form-control" rows="3" required placeholder="Tulis pertanyaan audit..."></textarea>
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

        // Script untuk Auditee - Tambah/Hapus Link
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

        // ✅ Script untuk Modal Revisi - Toggle checkbox
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

        // ✅ Validasi form revisi sebelum submit
        $('form[action*="send-revision"]').on('submit', function(e) {
            const checkedItems = $('input[name^="revisi_check"]:checked').length;

            if (checkedItems === 0) {
                e.preventDefault();
                alert('Silakan tandai minimal 1 pertanyaan yang perlu direvisi!');
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
                alert('Silakan isi catatan revisi untuk setiap item yang ditandai!');
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
            background-color: #fff3cd;
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
