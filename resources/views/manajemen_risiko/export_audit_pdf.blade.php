<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Audit Report - {{ $peta->kode_regist }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 2px solid #000;
            padding-bottom: 3px;
        }

        .header h1 {
            font-size: 10px;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: 9px;
            margin: 0;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 5px;
        }

        table td {
            padding: 3px 5px;
            border: 1px solid #000;
            vertical-align: top;
            font-size: 7px;
            line-height: 1.3;
        }

        .label {
            font-weight: bold;
            font-size: 7px;
        }

        .value {
            font-size: 7px;
        }

        .header-row td {
            background-color: #d9d9d9;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            font-size: 7px;
        }

        .level-high {
            background-color: #FFA500;
            color: #000;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .level-extreme {
            background-color: #FF0000;
            color: #fff;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .level-moderate {
            background-color: #FFFF00;
            color: #000;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .level-low {
            background-color: #90EE90;
            color: #000;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .residual-extreme {
            background-color: #FF0000;
            color: #fff;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .residual-high {
            background-color: #FFA500;
            color: #000;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .residual-moderate {
            background-color: #FFFF00;
            color: #000;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .residual-low {
            background-color: #90EE90;
            color: #000;
            font-weight: bold;
            padding: 1px 12px;
            display: inline-block;
            font-size: 8px;
        }

        .table-content {
            font-size: 6.5px;
            line-height: 1.5;
            text-align: justify;
        }

        .table-content strong {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 8px;
            font-size: 7px;
            width: 100%;
        }

        .signature-container {
            width: 100%;
            display: table;
        }

        .signature-left {
            display: table-cell;
            width: 35%;
            vertical-align: bottom;
            padding-bottom: 0;
            text-align: left;
            padding-left: 10px;
        }

        .signature-right {
            display: table-cell;
            width: 65%;
            text-align: right;
            vertical-align: top;
            padding-right: 40px;
        }

        .signature-space {
            height: 35px;
        }

        .no-border-middle {
            border-left: 0;
            border-right: 0;
            width: 0.5%;
            padding: 0;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-underline {
            text-decoration: underline;
        }

        .red-text {
            color: #FF0000;
        }

        /* ── Komentar bernomor ── */
        .komentar-item {
            display: block;
            margin-bottom: 5px;
            line-height: 1.5;
        }

        .komentar-no {
            font-weight: bold;
            color: #1F3864;
            margin-right: 3px;
        }

        /* ── Mitigasi kepada ── */
        .mitigasi-kepada {
            display: block;
            margin-top: 5px;
            padding: 3px 5px;
            background-color: #f5f5f5;
            border-left: 2px solid #888;
            font-size: 6.5px;
            line-height: 1.4;
        }

        .mitigasi-kepada-label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LEMBAR MONITORING DAN EVALUASI MANAJEMEN RISIKO UNIT</h1>
        <p>SATUAN PENGAWAS INTERNAL</p>
        <p>POLITEKNIK NEGERI MALANG</p>
    </div>

    <table>
        <!-- Baris 1: UNIT & PEMONEV -->
        <tr>
            <td width="10%" class="label">UNIT</td>
            <td width="39.5%"><span class="value">: {{ $peta->jenis }}</span></td>
            <td class="no-border-middle"></td>
            <td width="10%" class="label">PEMONEV</td>
            <td width="39.5%"><span class="value">: {{ $hasilAudit->nama_pemonev ?? ($user->name ?? '-') }}</span></td>
        </tr>

        <!-- Baris 2: KODE RISIKO & TAHUN ANGGARAN -->
        <tr>
            <td class="label">KODE RISIKO</td>
            <td><span class="value">: {{ $peta->kode_regist ?? '-' }}</span></td>
            <td class="no-border-middle"></td>
            <td colspan="2"><span class="label">Tahun Anggaran KEGIATAN</span> <span class="value">:
                    {{ $hasilAudit->tahun_anggaran ?? date('Y') }}</span></td>
        </tr>

        <!-- Baris 3: KEGIATAN -->
        <tr>
            <td class="label">KEGIATAN</td>
            <td colspan="4"><span class="value">:
                    {{ $hasilAudit->kegiatan ?? ($peta->kegiatan->judul ?? $peta->judul) }}</span></td>
        </tr>

        <!-- Baris 4: PERNYATAAN RISIKO -->
        <tr>
            <td class="label" style="line-height: 1.1;">PERNYATAAN<br>RISIKO</td>
            <td colspan="4"><span class="value">: {{ $peta->pernyataan ?? '-' }}</span></td>
        </tr>

        <!-- Baris 5: LEVEL RISIKO & RISIKO RESIDUAL -->
        <tr>
            <td class="label">LEVEL RISIKO</td>
            <td>
                @php
                    $levelValue = $hasilAudit->level_risiko ?? $levelText;
                    $levelClass = 'level-low';
                    if ($levelValue == 'EXTREME') {
                        $levelClass = 'level-extreme';
                    } elseif ($levelValue == 'HIGH') {
                        $levelClass = 'level-high';
                    } elseif ($levelValue == 'MODERATE') {
                        $levelClass = 'level-moderate';
                    }
                @endphp
                <span class="{{ $levelClass }}">{{ $levelValue }}</span>
            </td>
            <td class="no-border-middle"></td>
            <td class="label">RISIKO RESIDUAL</td>
            <td>
                @php
                    $residualValue = $hasilAudit->risiko_residual ?? $residualText;
                    $residualClass = 'residual-low';
                    if ($residualValue == 'Extreme') {
                        $residualClass = 'residual-extreme';
                    } elseif ($residualValue == 'High') {
                        $residualClass = 'residual-high';
                    } elseif ($residualValue == 'Moderate') {
                        $residualClass = 'residual-moderate';
                    }
                @endphp
                <span class="{{ $residualClass }}">{{ $residualValue }}</span>
            </td>
        </tr>

        <!-- Baris 6: TABLE 3 KOLOM - PENGENDALIAN, MITIGASI, KOMENTAR -->
        @if ($hasilAudit)
            <tr class="header-row">
                <td colspan="2">PENGENDALIAN</td>
                <td colspan="2">MITIGASI RISIKO</td>
                <td>KOMENTAR</td>
            </tr>
            <tr>
                <td colspan="2" class="table-content" style="height: 180px; vertical-align: top;">
                    {{ $hasilAudit->pengendalian ?? '-' }}
                </td>
                <td colspan="2" class="table-content" style="height: 180px; vertical-align: top;">
                    @php
                        $mitigasiLabel = $hasilAudit->mitigasi_label ?? '-';
                        $mitigasiKepada = $hasilAudit->mitigasi_kepada ?? null;
                    @endphp
                    <strong class="red-text">{{ $mitigasiLabel }}</strong>

                    @if ($mitigasiKepada)
                        <span class="mitigasi-kepada">
                            <span class="mitigasi-kepada-label">Kepada:</span>
                            {{ $mitigasiKepada }}
                        </span>
                    @endif

                    @if ($hasilAudit->status_konfirmasi_auditee || $hasilAudit->status_konfirmasi_auditor)
                        <br><br>
                        <strong>Status Konfirmasi</strong><br>
                        @if ($hasilAudit->status_konfirmasi_auditee)
                            <strong>Auditee:</strong> {{ $hasilAudit->status_konfirmasi_auditee }}<br>
                        @endif
                        @if ($hasilAudit->status_konfirmasi_auditor)
                            <strong>Auditor:</strong> {{ $hasilAudit->status_konfirmasi_auditor }}
                        @endif
                    @endif
                </td>
                <td class="table-content" style="height: 180px; vertical-align: top;">
                    @foreach ([$hasilAudit->komentar_1, $hasilAudit->komentar_2, $hasilAudit->komentar_3] as $index => $komentar)
                        @if ($komentar)
                            <span class="komentar-item">
                                <span class="komentar-no">{{ $index + 1 }}.</span>{{ $komentar }}
                            </span>
                        @endif
                    @endforeach
                    @if (!$hasilAudit->komentar_1 && !$hasilAudit->komentar_2 && !$hasilAudit->komentar_3)
                        <span style="color:#888; font-style:italic;">— Tidak ada komentar —</span>
                    @endif
                </td>
            </tr>
        @else
            <tr>
                <td colspan="5" style="text-align: center; padding: 15px; color: #999; height: 180px;">
                    <em>Data audit belum diisi</em>
                </td>
            </tr>
        @endif
    </table>

    <div class="signature-section">
        <div class="signature-container">
            <div class="signature-left">
                <span class="text-underline">Unit.</span>
                <div class="signature-space"></div>
                <span class="text-bold text-underline">{{ $peta->jenis }}</span><br>
                <span>NIP. {{ $hasilAudit->auditor->nip ?? '197412082005011001' }}</span>
            </div>
            <div class="signature-right">
                <span>Malang, {{ date('d/m/Y') }}</span><br>
                <span class="text-bold">Pemonev</span>
                <div class="signature-space"></div>
                <span
                    class="text-bold text-underline">{{ $hasilAudit->nama_pemonev ?? ($user->name ?? 'Usman Nurhasan, S.Kom., M.T.') }}</span><br>
                <span>NIP. {{ $hasilAudit->nip_pemonev ?? ($user->nip ?? '198909162014042001') }}</span>
            </div>
        </div>
    </div>
</body>

</html>
