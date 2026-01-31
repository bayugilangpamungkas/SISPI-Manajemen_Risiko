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
                                <textarea name="pengendalian" class="form-control mt-1" rows="5" required placeholder="…">{{ old('pengendalian', $hasilAudit->pengendalian ?? '') }}</textarea>
                            </td>
                            <td width="33%" valign="top">
                                <span class="font-weight-bold">MITIGASI RISIKO</span>
                                <input type="text" name="mitigasi" class="form-control mt-1"
                                    value="{{ old('mitigasi', $hasilAudit->mitigasi ?? '') }}" required placeholder="…">
                            </td>
                            <td width="33%" valign="top">
                                <span class="font-weight-bold">KOMENTAR</span>
                                <textarea name="komentar_1" class="form-control mb-2 mt-1" rows="2" required placeholder="1. …">{{ old('komentar_1', $hasilAudit->komentar_1 ?? '') }}</textarea>
                                <textarea name="komentar_2" class="form-control mb-2" rows="2" required placeholder="2. …">{{ old('komentar_2', $hasilAudit->komentar_2 ?? '') }}</textarea>
                                <textarea name="komentar_3" class="form-control mb-2" rows="2" required placeholder="3. …">{{ old('komentar_3', $hasilAudit->komentar_3 ?? '') }}</textarea>
                                <span class="font-weight-bold d-block mt-2">Status Konfirmasi</span>
                                <select name="status_konfirmasi_auditee" class="form-control mb-1" style="width:90%">
                                    <option value="">- Auditee -</option>
                                    <option value="disetujui" {{ old('status_konfirmasi_auditee', $hasilAudit->status_konfirmasi_auditee ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ old('status_konfirmasi_auditee', $hasilAudit->status_konfirmasi_auditee ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="perlu_revisi" {{ old('status_konfirmasi_auditee', $hasilAudit->status_konfirmasi_auditee ?? '') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                                </select>
                                <select name="status_konfirmasi_auditor" class="form-control mb-1" style="width:90%">
                                    <option value="">- Auditor -</option>
                                    <option value="disetujui" {{ old('status_konfirmasi_auditor', $hasilAudit->status_konfirmasi_auditor ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ old('status_konfirmasi_auditor', $hasilAudit->status_konfirmasi_auditor ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="perlu_revisi" {{ old('status_konfirmasi_auditor', $hasilAudit->status_konfirmasi_auditor ?? '') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
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
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save"></i> Simpan Data Audit
                        </button>
                        @if(isset($hasilAudit) && $hasilAudit)
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle"></i> Data audit terakhir disimpan pada: <strong>{{ $hasilAudit->updated_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        @endif
                    </div>
                </form>
                {{-- === END FORM === --}}
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
