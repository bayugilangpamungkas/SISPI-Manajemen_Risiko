@extends('layout.app')
@section('title', 'Detail Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditee = !$isAdmin && !$isAuditor;
        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // LEVEL
        if ($skorTotal >= 20) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 15) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
            $badgeClass = 'bg-warning text-dark';
        } else {
            $levelText = 'LOW';
            $badgeClass = 'bg-success text-white';
        }
        // RESIDUAL
        if ($skorTotal >= 20) {
            $residualText = 'Extreme';
            $residualClass = 'bg-danger text-white';
        } elseif ($skorTotal >= 15) {
            $residualText = 'High';
            $residualClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $residualText = 'Moderate';
            $residualClass = 'bg-info text-dark';
        } else {
            $residualText = 'Low';
            $residualClass = 'bg-success text-white';
        }
    @endphp

    <div class="main-content">
        <section class="section">
            <div class="section-header position-relative" style="padding-top: 10px; padding-bottom: 10px;">
                <a href="{{ url('/manajemen-risiko') }}" class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <div class="w-100 text-center px-5">
                    <h1 class="font-weight-bold mb-0" style="font-size:19px">
                        LEMBAR MONITORING DAN EVALUASI MANAJEMEN RISIKO UNIT
                    </h1>
                    <div style="font-size:15px; line-height: 1.2;">
                        <span class="d-block">SATUAN PENGAWAS INTERNAL</span>
                        <span>POLITEKNIK NEGERI MALANG</span>
                    </div>
                </div>
            </div>

            <div class="section-body mt-4">
                {{-- === TAHAP 1: INPUT & SIMPAN DATA === --}}
                <form action="{{ route('manajemen-risiko.auditor.update-template', $peta->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- TAMPILAN TABEL FORM INPUT (Sama seperti sebelumnya) --}}
                    <table class="table table-bordered" style="border: 2px solid #333; width: 100%; font-size:15px;">
                        <tr>
                            <td width="49%">
                                <div>
                                    <span class="font-weight-bold">UNIT</span><br>
                                    <span>{{ $peta->jenis }}</span>
                                </div>
                            </td>
                            <td width="2%" style="border-left:0;"></td>
                            <td width="49%">
                                <div>
                                    <span class="font-weight-bold">PEMONEV</span><br>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="font-weight-bold">KODE RISIKO</span> :
                                <span>{{ $peta->kode_regist ?? '-' }}</span>
                            </td>
                            <td style="border-left:0;"></td>
                            <td>
                                <span class="font-weight-bold">Tahun Anggaran KEGIATAN</span> :
                                <span>{{ date('Y') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-right:0;">
                                <span class="font-weight-bold">KEGIATAN</span><br>
                                <span>{{ $peta->kegiatan->judul ?? $peta->judul }}</span>
                            </td>
                            <td rowspan="2" valign="top" style="border-left:0;">
                                <span class="font-weight-bold">PERNYATAAN RISIKO</span><br>
                                <span>{{ $peta->pernyataan ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-right:0;">
                                <span class="font-weight-bold">LEVEL RISIKO </span>
                                <span class="badge {{ $badgeClass }} p-2 ml-2"
                                    style="font-size:15px;">{{ $levelText }}</span>
                            </td>
                            <td style="border-left:0;">
                                <span class="font-weight-bold">RISIKO RESIDUAL</span>
                                <span class="badge {{ $residualClass }} p-2 ml-2"
                                    style="font-size:15px;">{{ $residualText }}</span>
                            </td>
                        </tr>

                        <tr>
                            <td width="33%" valign="top">
                                <span class="font-weight-bold">PENGENDALIAN</span>
                                <textarea name="pengendalian" class="form-control mt-1" rows="5" required placeholder="…">{{ old('pengendalian') }}</textarea>
                            </td>
                            <td width="33%" valign="top">
                                <span class="font-weight-bold">MITIGASI RISIKO</span>
                                <input type="text" name="mitigasi" class="form-control mt-1"
                                    value="{{ old('mitigasi') }}" required placeholder="…">
                            </td>
                            <td width="33%" valign="top">
                                <span class="font-weight-bold">KOMENTAR</span>
                                <textarea name="komentar_1" class="form-control mb-2 mt-1" rows="2" required placeholder="1. …">{{ old('komentar_1') }}</textarea>
                                <textarea name="komentar_2" class="form-control mb-2" rows="2" required placeholder="2. …">{{ old('komentar_2') }}</textarea>
                                <textarea name="komentar_3" class="form-control mb-2" rows="2" required placeholder="3. …">{{ old('komentar_3') }}</textarea>
                                <span class="font-weight-bold d-block mt-2">Status Konfirmasi</span>
                                <select name="status_konfirmasi_auditee" class="form-control mb-1" style="width:90%">
                                    <option value="">- Auditee -</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                    <option value="perlu_revisi">Perlu Revisi</option>
                                </select>
                                <select name="status_konfirmasi_auditor" class="form-control mb-1" style="width:90%">
                                    <option value="">- Auditor -</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                    <option value="perlu_revisi">Perlu Revisi</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    {{-- Bagian Tanda Tangan --}}
                    <div class="row mt-5">
                        <div class="col-8">
                            <span>Unit.</span>
                            <br>
                            <span class="font-weight-bold">{{ $peta->jenis }}</span>
                        </div>
                        <div class="col-4 text-right">
                            <span>
                                Malang, {{ date('d/m/Y') }}<br>
                                <b>Pemonev</b>
                            </span>
                            <br><br>
                            <span>
                                <b>{{ $user->name }}</b><br>
                                NIP. {{ $user->nip ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- TOMBOL SIMPAN DATABASE --}}
                    <div class="alert alert-light border mt-4">
                        <h6 class="font-weight-bold text-primary"><i class="fas fa-save"></i> Langkah 1: Simpan Data</h6>
                        <p class="mb-2 text-muted small">Simpan perubahan data (komentar, mitigasi, status) ke sistem
                            sebelum mengunduh.</p>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save"></i> Simpan Perubahan Data
                        </button>
                    </div>
                </form>
                {{-- === END TAHAP 1 === --}}



                {{-- === TAHAP 2: DOWNLOAD FILE (Untuk Direview Auditor) === --}}
                {{-- Note: Anda harus membuat route 'export-pdf' di controller yang menggunakan DomPDF atau library lain --}}
                <div class="alert alert-light border mt-3">
                    <h6 class="font-weight-bold text-warning"><i class="fas fa-file-download"></i> Langkah 2: Review
                        Template</h6>
                    <p class="mb-2 text-muted small">Unduh file hasil inputan di atas untuk diperiksa kembali sebelum
                        dikirim ke Auditee.</p>

                    {{-- Ganti href ini dengan route export PDF Anda --}}
                    <a href="{{ route('manajemen-risiko.auditor.export-pdf', $peta->id) }}" target="_blank"
                        class="btn btn-warning px-4 text-dark">
                        <i class="fas fa-file-pdf"></i> Download PDF Hasil Inputan
                    </a>
                </div>
                {{-- === END TAHAP 2 === --}}



                {{-- === TAHAP 3: UPLOAD FILE FINAL (Kirim ke Auditee) === --}}
                <div class="alert alert-light border mt-3" style="background-color: #f8f9fa;">
                    <h6 class="font-weight-bold text-success"><i class="fas fa-paper-plane"></i> Langkah 3: Kirim ke
                        Auditee</h6>
                    <p class="mb-2 text-muted small">Jika file hasil download sudah oke (atau sudah Anda beri catatan
                        tambahan), upload di sini untuk dikirim ke Auditee.</p>

                    <form action="{{ route('manajemen-risiko.auditor.upload-lampiran', $peta->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="form-group row align-items-center">
                            <div class="col-md-8">
                                <input type="file" name="file_pendukung" id="file_pendukung"
                                    class="form-control h-auto py-2" accept=".pdf, .xls, .xlsx" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-upload"></i> Kirim ke Auditee
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- === END TAHAP 3 === --}}
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        table th,
        table td {
            vertical-align: top !important;
            border: 2px solid #333 !important;
            padding: 8px 8px !important;
        }

        .badge {
            font-weight: 700;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }
    </style>
@endpush
