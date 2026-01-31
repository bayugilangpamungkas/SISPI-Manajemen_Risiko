<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Audit Report - {{ $peta->kode_regist }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }

        .header p {
            font-size: 13px;
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        table td {
            padding: 8px;
            border: 2px solid #000;
            vertical-align: top;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }

        .bg-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .bg-success {
            background-color: #28a745;
            color: #fff;
        }

        .bg-info {
            background-color: #17a2b8;
            color: #fff;
        }

        .signature-section {
            margin-top: 30px;
            font-size: 11px;
        }

        .signature-left {
            float: left;
            width: 60%;
        }

        .signature-right {
            float: right;
            width: 38%;
            text-align: right;
        }

        .clear {
            clear: both;
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
        <tr>
            <td width="49%">
                <span class="font-weight-bold">UNIT</span><br>
                <span>{{ $peta->jenis }}</span>
            </td>
            <td width="2%" style="border-left:0; border-right:0; border-top:0; border-bottom:0;"></td>
            <td width="49%">
                <span class="font-weight-bold">PEMONEV</span><br>
                <span>{{ $user->name }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="font-weight-bold">KODE RISIKO</span> :
                <span>{{ $peta->kode_regist ?? '-' }}</span>
            </td>
            <td style="border-left:0; border-right:0; border-top:0; border-bottom:0;"></td>
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
            <td rowspan="2" style="border-left:0;">
                <span class="font-weight-bold">PERNYATAAN RISIKO</span><br>
                <span>{{ $peta->pernyataan ?? '-' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-right:0;">
                <span class="font-weight-bold">LEVEL RISIKO </span>
                <span class="badge {{ $badgeClass }}">{{ $hasilAudit->level_risiko ?? $levelText }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="font-weight-bold">RISIKO RESIDUAL</span>
                <span class="badge {{ $residualClass }}">{{ $hasilAudit->risiko_residual ?? $residualText }}</span>
                @if($hasilAudit && $hasilAudit->skor_total)
                    <span style="font-size: 10px; color: #666;"> (Skor Total: {{ $hasilAudit->skor_total }})</span>
                @endif
            </td>
        </tr>

        @if($hasilAudit)
        <tr>
            <td width="33%">
                <span class="font-weight-bold">PENGENDALIAN</span><br>
                {{ $hasilAudit->pengendalian }}
            </td>
            <td width="33%">
                <span class="font-weight-bold">MITIGASI RISIKO</span><br>
                {{ $hasilAudit->mitigasi }}
            </td>
            <td width="33%">
                <span class="font-weight-bold">KOMENTAR</span><br>
                1. {{ $hasilAudit->komentar_1 }}<br><br>
                2. {{ $hasilAudit->komentar_2 }}<br><br>
                3. {{ $hasilAudit->komentar_3 }}<br><br>
                
                <span class="font-weight-bold">Status Konfirmasi</span><br>
                Auditee: {{ $hasilAudit->status_konfirmasi_auditee ?? '-' }}<br>
                Auditor: {{ $hasilAudit->status_konfirmasi_auditor ?? '-' }}
            </td>
        </tr>
        @else
        <tr>
            <td colspan="3" style="text-align: center; padding: 20px; color: #999;">
                <em>Data audit belum diisi</em>
            </td>
        </tr>
        @endif
    </table>

    <div class="signature-section">
        <div class="signature-left">
            <span>Unit.</span><br>
            <span class="font-weight-bold">{{ $peta->jenis }}</span>
        </div>
        <div class="signature-right">
            <span>Malang, {{ date('d/m/Y') }}</span><br>
            <span class="font-weight-bold">Pemonev</span><br><br><br>
            <span class="font-weight-bold">{{ $user->name }}</span><br>
            <span>NIP. {{ $user->nip ?? '-' }}</span>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
