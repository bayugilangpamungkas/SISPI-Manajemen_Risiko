<!DOCTYPE html>
<html>

<head>
    <title>Lembar Monitoring Manajemen Risiko</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            vertical-align: top;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            color: black;
            font-weight: bold;
        }

        .footer-table {
            border: none !important;
            margin-top: 30px;
        }

        .footer-table td {
            border: none !important;
        }
    </style>
</head>

<body>

    <div class="text-center">
        <h3 style="margin-bottom: 5px;">LEMBAR MONITORING DAN EVALUASI MANAJEMEN RISIKO UNIT</h3>
        <div style="font-size: 14px;">SATUAN PENGAWAS INTERNAL</div>
        <div style="font-size: 14px;">POLITEKNIK NEGERI MALANG</div>
    </div>

    <table>
        <tr>
            <td width="50%">
                <span class="font-weight-bold">UNIT:</span><br>
                {{ $peta->jenis }}
            </td>
            <td>
                <span class="font-weight-bold">PEMONEV:</span><br>
                {{ $user->name }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="font-weight-bold">KODE RISIKO:</span> {{ $peta->kode_regist ?? '-' }}
            </td>
            <td>
                <span class="font-weight-bold">TAHUN ANGGARAN:</span> {{ date('Y') }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="font-weight-bold">KEGIATAN:</span><br>
                {{ $peta->kegiatan->judul ?? $peta->judul }}
            </td>
            <td>
                <span class="font-weight-bold">PERNYATAAN RISIKO:</span><br>
                {{ $peta->pernyataan ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="font-weight-bold">LEVEL RISIKO:</span> {{ $peta->skor_kemungkinan * $peta->skor_dampak }}
            </td>
            <td>
                <span class="font-weight-bold">STATUS KONFIRMASI:</span><br>
                Auditor: {{ ucfirst($peta->status_konfirmasi_auditor ?? '-') }}<br>
                Auditee: {{ ucfirst($peta->status_konfirmasi_auditee ?? '-') }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="font-weight-bold">PENGENDALIAN:</span><br>
                {{ $peta->pengendalian ?? '-' }}
            </td>
            <td>
                <span class="font-weight-bold">MITIGASI RISIKO:</span><br>
                {{ $peta->mitigasi ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="font-weight-bold">KOMENTAR / CATATAN AUDITOR:</span><br>
                @php
                    // Filter berdasarkan kolom 'jenis' yang isinya 'analisis'
                    $lastComment = $peta->comment_prs->where('jenis', 'analisis')->last();
                @endphp

                {!! $lastComment ? nl2br(e($lastComment->comment)) : '-' !!}
            </td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td width="70%"></td>
            <td class="text-center">
                Malang, {{ $tanggal }}<br>
                <span class="font-weight-bold">Pemonev</span>
                <br><br><br><br>
                <span class="font-weight-bold">{{ $user->name }}</span><br>
                NIP. {{ $user->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>

</html>
