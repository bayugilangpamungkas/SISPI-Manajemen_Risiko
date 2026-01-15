@extends('layout.app')
@section('title', 'Detail & Template Risiko - Auditor')

@section('main')
    @php
        $user = Auth::user();
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);

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
    @endphp

    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('manajemen-risiko.auditor.index') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Detail & Template Risiko</h1>
                        <small class="text-muted">Auditor: {{ $user->name }} ({{ $user->Level->name }})</small>
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

                {{-- INFO PANEL: Status & Actions --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm bg-light">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-2">
                                            <i class="fas fa-info-circle text-primary"></i>
                                            <strong>Status Pengerjaan</strong>
                                        </h5>
                                        <div class="d-flex align-items-center">
                                            @if ($peta->status_telaah)
                                                <span class="badge badge-success badge-lg mr-2" style="font-size: 14px;">
                                                    <i class="fas fa-check-circle"></i> Sudah ACC
                                                </span>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i>
                                                    {{ $peta->waktu_telaah_spi ? \Carbon\Carbon::parse($peta->waktu_telaah_spi)->format('d M Y, H:i') : '-' }}
                                                </small>
                                            @elseif($peta->koreksiPr == 'submitted')
                                                <span class="badge badge-info badge-lg" style="font-size: 14px;">
                                                    <i class="fas fa-paper-plane"></i> Menunggu Review Anda
                                                </span>
                                            @else
                                                <span class="badge badge-warning badge-lg" style="font-size: 14px;">
                                                    <i class="fas fa-clock"></i> Belum Disubmit Auditee
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @if ($peta->status_telaah)
                                            {{-- Tombol Cetak Laporan (Aktif setelah ACC) --}}
                                            <a href="{{ route('manajemen-risiko.auditor.generate-report') }}?unit_kerja={{ $peta->jenis }}&tahun={{ date('Y', strtotime($peta->created_at)) }}"
                                                class="btn btn-danger btn-lg" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Cetak Laporan PDF
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-lg" disabled>
                                                <i class="fas fa-lock"></i> Cetak Laporan (Belum ACC)
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- COLUMN 1: Informasi Risiko & Template Input --}}
                    <div class="col-lg-8">
                        {{-- CARD 1: Informasi Risiko --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="text-white mb-0">
                                    <i class="fas fa-clipboard-list"></i> Informasi Risiko dari Auditee
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
                                        <td>
                                            <span class="badge badge-secondary">{{ $kodeUnit ?? '-' }}</span>
                                        </td>
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
                                    <tr>
                                        <th class="bg-light">Uraian Dampak</th>
                                        <td>{{ $peta->uraian ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Metode Pengendalian</th>
                                        <td>{{ $peta->metode ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- CARD 2: Form Tabel Input Risiko (Menyesuaikan Kode Unit) --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h4 class="text-white mb-0 text-center font-weight-bold">
                                    LEMBAR MONITORING DAN EVALUASI MANAJEMEN RISIKO UNIT<br>
                                    SATUAN PENGAWAS INTERNAL<br>
                                    POLITEKNIK NEGERI MALANG
                                </h4>
                            </div>
                            <div class="card-body" style="background-color: #f8f9fa; border: 3px solid #000;">
                                <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}"
                                    method="POST" id="formInputRisiko">
                                    @csrf
                                    @method('PUT')

                                    {{-- Info Header Tabel - Grid Layout --}}
                                    <div
                                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px;">
                                        <div>
                                            <div style="display: flex; margin-bottom: 12px; font-size: 13px;">
                                                <strong style="min-width: 120px;">UNIT</strong>
                                                <span style="margin: 0 10px;">:</span>
                                                <span>{{ $peta->jenis }}</span>
                                            </div>
                                            <div style="display: flex; font-size: 13px;">
                                                <strong style="min-width: 120px;">KODE RISIKO</strong>
                                                <span style="margin: 0 10px;">:</span>
                                                <span>{{ $peta->kode_regist }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div style="display: flex; margin-bottom: 12px; font-size: 13px;">
                                                <strong style="min-width: 100px;">PEMONEV</strong>
                                                <span style="margin: 0 10px;">:</span>
                                                <span>{{ $unitKerjaModel ? $unitKerjaModel->pimpinan ?? '-' : '-' }}</span>
                                            </div>
                                            <div style="display: flex; font-size: 13px;">
                                                <strong style="min-width: 100px;">Tahun</strong>
                                                <span style="margin: 0 10px;">:</span>
                                                <span>{{ date('Y', strtotime($peta->created_at)) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kegiatan, Pernyataan Risiko, Level Risiko --}}
                                    <div style="margin-bottom: 25px;">
                                        <div style="display: flex; margin-bottom: 12px; font-size: 13px;">
                                            <strong style="min-width: 150px;">KEGIATAN</strong>
                                            <span style="margin: 0 10px;">:</span>
                                            <span>{{ $peta->kegiatan ? $peta->kegiatan->judul : $peta->judul }}</span>
                                        </div>
                                        <div style="display: flex; margin-bottom: 12px; font-size: 13px;">
                                            <strong style="min-width: 150px;">PERNYATAAN RISIKO</strong>
                                            <span style="margin: 0 10px;">:</span>
                                            <span>{{ $peta->pernyataan ?: '-' }}</span>
                                        </div>
                                        <div style="display: flex; align-items: center; font-size: 13px;">
                                            <strong style="min-width: 150px;">LEVEL RISIKO</strong>
                                            <span style="margin: 0 10px;">:</span>
                                            <span class="badge {{ $badgeClass }}"
                                                style="font-size: 12px; padding: 5px 15px; margin-right: 15px;">
                                                {{ strtoupper($badgeText) }}
                                            </span>
                                            <span
                                                style="font-weight: bold; color: {{ $skorTotal >= 20 ? '#dc3545' : ($skorTotal >= 15 ? '#ffc107' : '#28a745') }};">
                                                RISIKO {{ $skorTotal >= 15 ? 'RESIDUAL' : 'INHERENT' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Tabel Input Monitoring --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered"
                                            style="border: 2px solid #000; margin-bottom: 0;">
                                            <thead>
                                                <tr style="background-color: #e9ecef;">
                                                    <th rowspan="2" class="text-center align-middle"
                                                        style="width: 50px; border: 2px solid #000; font-weight: bold; font-size: 13px;">
                                                        No
                                                    </th>
                                                    <th colspan="2" class="text-center"
                                                        style="border: 2px solid #000; background-color: #fff3cd; font-weight: bold; font-size: 13px;">
                                                        PENGENDALIAN
                                                    </th>
                                                    <th rowspan="2" class="text-center align-middle"
                                                        style="width: 25%; border: 2px solid #000; background-color: #fff3cd; font-weight: bold; font-size: 13px;">
                                                        MITIGASI RISIKO
                                                    </th>
                                                    <th rowspan="2" class="text-center align-middle"
                                                        style="width: 20%; border: 2px solid #000; background-color: #fff3cd; font-weight: bold; font-size: 13px;">
                                                        KOMENTAR
                                                    </th>
                                                </tr>
                                                <tr style="background-color: #e9ecef;">
                                                    <th class="text-center"
                                                        style="width: 25%; border: 2px solid #000; font-weight: bold; font-size: 12px; padding: 10px 5px;">
                                                        Untuk mengatasi resiko yang terjadi, maka cara mengendalikan
                                                        dampaknya adalah
                                                    </th>
                                                    <th class="text-center"
                                                        style="width: 15%; border: 2px solid #000; font-weight: bold; font-size: 13px;">
                                                        Status Konfirmasi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBodyUmum">
                                                @php
                                                    $dataRisiko = is_array($peta->template_data)
                                                        ? $peta->template_data
                                                        : [];
                                                    $jumlahBaris = max(1, count($dataRisiko['menerima_risiko'] ?? []));
                                                @endphp
                                                @for ($i = 0; $i < $jumlahBaris; $i++)
                                                    <tr>
                                                        <td class="text-center align-middle"
                                                            style="border: 2px solid #000; font-weight: bold; width: 50px;">
                                                            {{ $i + 1 }}
                                                        </td>
                                                        <td style="border: 2px solid #000; padding: 8px; width: 25%;">
                                                            <textarea name="menerima_risiko[]" class="form-control" rows="8"
                                                                style="border: 1px solid #ced4da; font-size: 12px; resize: vertical;"
                                                                placeholder="Jelaskan cara mengendalikan dampak risiko...">{{ $dataRisiko['menerima_risiko'][$i] ?? '' }}</textarea>
                                                        </td>
                                                        <td style="border: 2px solid #000; padding: 8px; width: 15%;">
                                                            <textarea name="status_konfirmasi[]" class="form-control" rows="8"
                                                                style="border: 1px solid #ced4da; font-size: 12px; resize: vertical;"
                                                                placeholder="Status konfirmasi dari unit terkait...">{{ $dataRisiko['status_konfirmasi'][$i] ?? '' }}</textarea>
                                                        </td>
                                                        <td style="border: 2px solid #000; padding: 8px; width: 25%;">
                                                            <textarea name="mitigasi_risiko[]" class="form-control" rows="8"
                                                                style="border: 1px solid #ced4da; font-size: 12px; resize: vertical;"
                                                                placeholder="1. Mitigasi pertama&#10;2. Mitigasi kedua&#10;3. Mitigasi ketiga...">{{ $dataRisiko['mitigasi_risiko'][$i] ?? '' }}</textarea>
                                                        </td>
                                                        <td style="border: 2px solid #000; padding: 8px; width: 20%;">
                                                            <textarea name="komentar[]" class="form-control" rows="8"
                                                                style="border: 1px solid #ced4da; font-size: 12px; resize: vertical;" placeholder="Komentar dan evaluasi...">{{ $dataRisiko['komentar'][$i] ?? '' }}</textarea>
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Tombol Tambah Baris --}}
                                    <div class="text-center mb-4" style="margin-top: 20px;">
                                        <button type="button" class="btn btn-primary" onclick="tambahBaris()"
                                            style="padding: 10px 30px; font-weight: bold;">
                                            <i class="fas fa-plus-circle"></i> Tambah Baris
                                        </button>
                                    </div>

                                    <hr class="my-4">

                                    {{-- Kesimpulan & Catatan Auditor --}}
                                    <div
                                        style="background-color: white; padding: 20px; border-radius: 8px; border: 1px solid #dee2e6;">
                                        <h5 class="mb-3 font-weight-bold" style="color: #495057;">
                                            <i class="fas fa-clipboard-check"></i> Kesimpulan Auditor
                                        </h5>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Kesimpulan Umum</label>
                                            <textarea name="kesimpulan" class="form-control" rows="3" style="border: 1px solid #ced4da; font-size: 13px;"
                                                placeholder="Kesimpulan umum dari hasil penilaian risiko...">{{ old('kesimpulan', is_array($peta->template_data) ? $peta->template_data['kesimpulan'] ?? '' : '') }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Rekomendasi Tindak Lanjut</label>
                                            <textarea name="rekomendasi_tindak_lanjut" class="form-control" rows="3"
                                                style="border: 1px solid #ced4da; font-size: 13px;"
                                                placeholder="Rekomendasi untuk tindak lanjut pengelolaan risiko...">{{ old('rekomendasi_tindak_lanjut', is_array($peta->template_data) ? $peta->template_data['rekomendasi_tindak_lanjut'] ?? '' : '') }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Catatan Tambahan untuk Auditee</label>
                                            <textarea name="catatan_tambahan" class="form-control" rows="2"
                                                style="border: 1px solid #ced4da; font-size: 13px;"
                                                placeholder="Catatan tambahan yang perlu diketahui oleh Auditee...">{{ old('catatan_tambahan', is_array($peta->template_data) ? $peta->template_data['catatan_tambahan'] ?? '' : '') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning mt-4"
                                        style="border-left: 4px solid #ffc107; font-size: 13px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Penting:</strong> Setelah menyimpan, gunakan tombol <strong>"Kirim Template
                                            ke Auditee"</strong>
                                        agar Auditee dapat melihat dan melengkapi data.
                                    </div>

                                    <div class="d-flex justify-content-between mt-4" style="gap: 10px;">
                                        <button type="submit" class="btn btn-success btn-lg"
                                            style="padding: 12px 40px; font-weight: bold; flex: 1;">
                                            <i class="fas fa-save"></i> Simpan Template
                                        </button>

                                        {{-- Tombol Kirim Template - Tampil jika belum dikirim --}}
                                        @if (!$peta->template_sent_at)
                                            @if (!empty($peta->template_data))
                                                {{-- Tombol Aktif (Biru) - Jika sudah simpan minimal 1 kali --}}
                                                <button type="button" class="btn btn-info btn-lg"
                                                    onclick="sendTemplateToAuditee()"
                                                    style="padding: 12px 40px; font-weight: bold; flex: 1;">
                                                    <i class="fas fa-paper-plane"></i> Kirim Template ke Auditee
                                                </button>
                                            @else
                                                {{-- Tombol Disabled (Abu-abu) - Jika belum pernah simpan --}}
                                                <button type="button" class="btn btn-secondary btn-lg" disabled
                                                    style="padding: 12px 40px; font-weight: bold; flex: 1;"
                                                    title="Simpan template terlebih dahulu">
                                                    <i class="fas fa-lock"></i> Simpan Dulu untuk Mengirim
                                                </button>
                                            @endif
                                        @else
                                            {{-- Jika sudah dikirim, tampilkan status --}}
                                            <div class="alert alert-success mb-0" style="flex: 1; margin: 0;">
                                                <i class="fas fa-check-circle"></i>
                                                <strong>Template Sudah Dikirim ke Auditee</strong><br>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($peta->template_sent_at)->format('d M Y, H:i') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- CARD 3: Riwayat Komentar --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h4 class="text-white mb-0">
                                    <i class="fas fa-comments"></i> Riwayat Review & Komunikasi
                                </h4>
                            </div>
                            <div class="card-body">
                                @if ($peta->comment_prs->count() > 0)
                                    <div class="timeline">
                                        @foreach ($peta->comment_prs as $comment)
                                            <div class="alert alert-light border mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div>
                                                        <strong class="text-primary">
                                                            <i class="fas fa-user-circle"></i> {{ $comment->user->name }}
                                                        </strong>
                                                        <span
                                                            class="badge badge-info ml-2">{{ ucfirst($comment->jenis) }}</span>
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
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-info-circle"></i> Belum ada komunikasi untuk risiko ini.
                                    </div>
                                @endif

                                <hr>
                                <h6 class="font-weight-bold mb-3">Tambah Komentar/Catatan</h6>
                                <form action="{{ route('manajemen-risiko.comment', $peta->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Jenis Komentar</label>
                                        <select name="jenis" class="form-control" required>
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="keuangan">Aspek Keuangan</option>
                                            <option value="analisis">Analisis Risiko</option>
                                            <option value="mitigasi">Strategi Mitigasi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <textarea name="comment" class="form-control" rows="3" required
                                            placeholder="Tulis komentar atau catatan review Anda..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Kirim Komentar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMN 2: Sidebar Info & Actions --}}
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

                        {{-- CARD: Actions --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-warning">
                                <h4 class="mb-0"><i class="fas fa-tasks"></i> Aksi Review</h4>
                            </div>
                            <div class="card-body">
                                @if (!$peta->status_telaah)
                                    <button class="btn btn-success btn-block btn-lg mb-2" data-toggle="modal"
                                        data-target="#approveModal">
                                        <i class="fas fa-check-circle"></i> ACC / Setujui
                                    </button>
                                    <button class="btn btn-danger btn-block btn-lg" data-toggle="modal"
                                        data-target="#rejectModal">
                                        <i class="fas fa-times-circle"></i> Tolak & Kembalikan
                                    </button>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle"></i> Tombol Cetak Laporan akan aktif setelah ACC
                                    </small>
                                @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> <strong>Sudah di-ACC</strong><br>
                                        <small>{{ $peta->waktu_telaah_spi ? \Carbon\Carbon::parse($peta->waktu_telaah_spi)->format('d M Y, H:i') : '-' }}</small>
                                    </div>
                                    <a href="{{ route('manajemen-risiko.auditor.generate-report') }}?unit_kerja={{ $peta->jenis }}&tahun={{ date('Y', strtotime($peta->created_at)) }}"
                                        class="btn btn-danger btn-block btn-lg" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Cetak Laporan PDF
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- CARD: Info Auditee --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h4 class="text-white mb-0"><i class="fas fa-building"></i> Info Unit Kerja</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Unit:</strong></td>
                                        <td>{{ $peta->jenis }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kode:</strong></td>
                                        <td><span class="badge badge-secondary">{{ $kodeUnit ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kode Reg:</strong></td>
                                        <td>{{ $peta->kode_regist }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tahun:</strong></td>
                                        <td>{{ date('Y', strtotime($peta->created_at)) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- CARD: Progress Status --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-dark text-white">
                                <h4 class="text-white mb-0"><i class="fas fa-clipboard-check"></i> Status Progress</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Template Terisi
                                        @if (!empty($peta->template_data))
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Dikirim ke Auditee
                                        @if ($peta->template_sent_at)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Auditee Selesai
                                        @if ($peta->koreksiPr == 'submitted')
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i></span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        ACC / Approved
                                        @if ($peta->status_telaah)
                                            <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- MODAL: Approve / ACC --}}
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Konfirmasi ACC / Setujui</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('manajemen-risiko.auditor.approve', $peta->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p><strong>Apakah Anda yakin ingin menyetujui risiko ini?</strong></p>
                        <hr>
                        <div class="alert alert-info">
                            <strong>Unit:</strong> {{ $peta->jenis }}<br>
                            <strong>Risiko:</strong> {{ $peta->judul }}<br>
                            <strong>Skor:</strong> {{ $skorTotal }} ({{ $badgeText }})
                        </div>
                        <div class="form-group">
                            <label><strong>Catatan Persetujuan (Opsional)</strong></label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Tambahkan catatan hasil review..."></textarea>
                        </div>
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> Data akan diteruskan ke Admin dan tombol <strong>Cetak
                                Laporan</strong> akan aktif.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Ya, ACC / Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Reject --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle"></i> Tolak & Kembalikan ke Auditee</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('manajemen-risiko.auditor.reject', $peta->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p><strong>Data akan dikembalikan ke Auditee untuk perbaikan</strong></p>
                        <hr>
                        <div class="alert alert-warning">
                            <strong>Unit:</strong> {{ $peta->jenis }}<br>
                            <strong>Risiko:</strong> {{ $peta->judul }}
                        </div>
                        <div class="form-group">
                            <label><strong>Alasan Penolakan & Perbaikan yang Diperlukan <span
                                        class="text-danger">*</span></strong></label>
                            <textarea name="comment" class="form-control" rows="5" required
                                placeholder="Jelaskan:&#10;1. Alasan penolakan&#10;2. Perbaikan yang harus dilakukan&#10;3. Aspek yang perlu ditinjau ulang"></textarea>
                        </div>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Auditee akan menerima notifikasi dan wajib
                            melakukan revisi.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Ya, Tolak & Kembalikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-success-light {
            background-color: #d4edda !important;
        }

        .table-bordered th,
        .table-bordered td {
            border: 2px solid #000 !important;
        }

        .table thead th {
            vertical-align: middle;
            font-weight: 600;
            font-size: 13px;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
        }

        /* Styling untuk textarea di tabel */
        .table td textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            font-size: 12px;
            font-family: Arial, sans-serif;
            resize: vertical;
            min-height: 100px;
        }

        /* Grid layout untuk info header */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
        }

        .info-row-flex {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .info-row-flex strong {
            min-width: 150px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function sendTemplateToAuditee() {
            Swal.fire({
                title: 'Kirim Template ke Auditee?',
                text: 'Template tugas akan dikirimkan ke Auditee untuk dikerjakan',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form kirim template
                    fetch('{{ route('manajemen-risiko.auditor.send-template', $peta->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Template berhasil dikirim ke Auditee',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mengirim template',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // Fungsi Tambah Baris pada Tabel
        function tambahBaris() {
            const tbody = document.querySelector('#tableBodyUmum');
            const lastRow = tbody.querySelector('tr:last-child');
            const newRow = lastRow.cloneNode(true);

            // Reset semua input di baris baru
            newRow.querySelectorAll('textarea, input, select').forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = '';
                }
            });

            // Update nomor urut
            const rowNumber = tbody.querySelectorAll('tr').length + 1;
            newRow.querySelector('td:first-child').textContent = rowNumber;

            tbody.appendChild(newRow);

            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Baris Ditambahkan!',
                text: 'Baris baru berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Fungsi Hapus Baris pada Tabel
        function hapusBaris(button) {
            const row = button.closest('tr');
            const tbody = row.parentNode;
            tbody.removeChild(row);

            // Update nomor urut
            tbody.querySelectorAll('tr').forEach((tr, index) => {
                tr.querySelector('td:first-child').textContent = index + 1;
            });

            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Baris Dihapus!',
                text: 'Baris berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Auto-hide alerts
        setTimeout(() => {
            $('.alert-success, .alert-danger').fadeOut('slow');
        }, 5000);
    </script>
@endpush
