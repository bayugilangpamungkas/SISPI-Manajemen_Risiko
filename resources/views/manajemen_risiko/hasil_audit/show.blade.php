@extends('layout.app')
@section('title', 'Detail Hasil Audit')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header position-relative" style="padding-top: 10px; padding-bottom: 10px;">
                <a href="{{ route('manajemen-risiko.hasil-audit.index') }}" class="mr-3">
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
                {{-- BUTTON ACTIONS --}}
                <div class="mb-3 text-right">
                    <a href="{{ route('manajemen-risiko.hasil-audit.print', $hasilAudit->id) }}" target="_blank"
                        class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Cetak PDF
                    </a>
                </div>

                {{-- DETAIL TABLE --}}
                <table class="table table-bordered" style="border: 2px solid #333; width: 100%; font-size:15px;">
                    <tr>
                        <td width="49%">
                            <div>
                                <span class="font-weight-bold">UNIT</span><br>
                                <span>{{ $hasilAudit->unit_kerja }}</span>
                            </div>
                        </td>
                        <td width="2%" style="border-left:0;"></td>
                        <td width="49%">
                            <div>
                                <span class="font-weight-bold">PEMONEV</span><br>
                                <span>{{ $hasilAudit->nama_pemonev }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="font-weight-bold">KODE RISIKO</span> :
                            <span>{{ $hasilAudit->kode_risiko }}</span>
                        </td>
                        <td style="border-left:0;"></td>
                        <td>
                            <span class="font-weight-bold">Tahun Anggaran KEGIATAN</span> :
                            <span>{{ $hasilAudit->tahun_anggaran }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-right:0;">
                            <span class="font-weight-bold">KEGIATAN</span><br>
                            <span>{{ $hasilAudit->kegiatan }}</span>
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
                                style="font-size:15px;">{{ $hasilAudit->level_risiko }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <span class="font-weight-bold">RISIKO RESIDUAL</span>
                            <span class="badge {{ $residualClass }} p-2 ml-2"
                                style="font-size:15px;">{{ $hasilAudit->risiko_residual }}</span>
                            <span class="ml-2">
                                <small class="text-muted">(Skor Total: {{ $hasilAudit->skor_total }})</small>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td width="33%" valign="top">
                            <span class="font-weight-bold">PENGENDALIAN</span><br>
                            <div class="mt-2">{{ $hasilAudit->pengendalian }}</div>
                        </td>
                        <td width="33%" valign="top">
                            <span class="font-weight-bold">MITIGASI RISIKO</span><br>
                            <div class="mt-2">{{ $hasilAudit->mitigasi }}</div>
                        </td>
                        <td width="33%" valign="top">
                            <span class="font-weight-bold">KOMENTAR</span><br>
                            <div class="mt-2">
                                <strong>1.</strong> {{ $hasilAudit->komentar_1 }}<br><br>
                                <strong>2.</strong> {{ $hasilAudit->komentar_2 }}<br><br>
                                <strong>3.</strong> {{ $hasilAudit->komentar_3 }}<br><br>

                                <span class="font-weight-bold d-block mt-3">Status Konfirmasi</span>
                                <div class="mt-2">
                                    <strong>Auditee:</strong>
                                    @if ($hasilAudit->status_konfirmasi_auditee)
                                        <span
                                            class="badge {{ $hasilAudit->status_konfirmasi_auditee == 'disetujui' ? 'badge-success' : ($hasilAudit->status_konfirmasi_auditee == 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $hasilAudit->status_konfirmasi_auditee)) }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Belum dikonfirmasi</span>
                                    @endif
                                    <br>
                                    <strong>Auditor:</strong>
                                    @if ($hasilAudit->status_konfirmasi_auditor)
                                        <span
                                            class="badge {{ $hasilAudit->status_konfirmasi_auditor == 'disetujui' ? 'badge-success' : ($hasilAudit->status_konfirmasi_auditor == 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $hasilAudit->status_konfirmasi_auditor)) }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Belum dikonfirmasi</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- Bagian Tanda Tangan --}}
                <div class="row mt-5">
                    <div class="col-8">
                        <span>Unit.</span>
                        <br>
                        <span class="font-weight-bold">{{ $hasilAudit->unit_kerja }}</span>
                    </div>
                    <div class="col-4 text-right">
                        <span>
                            Malang, {{ $hasilAudit->created_at->format('d/m/Y') }}<br>
                            <b>Pemonev</b>
                        </span>
                        <br><br><br>
                        <span>
                            <b>{{ $hasilAudit->nama_pemonev }}</b><br>
                            NIP. {{ $hasilAudit->nip_pemonev ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- METADATA --}}
                <div class="alert alert-info mt-4">
                    <strong><i class="fas fa-info-circle"></i> Informasi Audit:</strong><br>
                    <small>
                        <strong>Tanggal Audit:</strong> {{ $hasilAudit->created_at->format('d F Y H:i') }}<br>
                        <strong>Terakhir Diperbarui:</strong> {{ $hasilAudit->updated_at->format('d F Y H:i') }}<br>
                        <strong>Auditor:</strong> {{ $hasilAudit->auditor->name ?? 'N/A' }}
                        ({{ $hasilAudit->auditor->Level->name ?? 'N/A' }})
                    </small>
                </div>
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

        @media print {
            .section-header a,
            .btn,
            .alert-info {
                display: none !important;
            }

            .section-body {
                margin-top: 0 !important;
            }
        }
    </style>
@endpush
