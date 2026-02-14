<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 2cm 3cm;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .kop-surat h2 {
            margin: 5px 0;
            font-size: 16pt;
            font-weight: bold;
        }

        .kop-surat p {
            margin: 3px 0;
            font-size: 11pt;
        }

        .nomor-surat {
            margin: 30px 0 20px 0;
            text-align: center;
        }

        .nomor-surat strong {
            font-size: 13pt;
            text-decoration: underline;
        }

        .meta-surat {
            margin-bottom: 30px;
        }

        .meta-surat table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-surat td {
            padding: 5px 0;
            vertical-align: top;
        }

        .meta-surat td:first-child {
            width: 120px;
        }

        .isi-surat {
            text-align: justify;
            margin: 30px 0;
            white-space: pre-wrap;
        }

        .ttd {
            margin-top: 50px;
            text-align: right;
        }

        .ttd p {
            margin: 5px 0;
        }

        .ttd-space {
            height: 80px;
        }

        .footer {
            margin-top: 50px;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <h2>SATUAN PENGAWAS INTERNAL</h2>
        <h2>POLITEKNIK NEGERI MALANG</h2>
        <p>Jl. Soekarno Hatta No. 9 Malang 65141</p>
        <p>Telp. (0341) 404424 | Email: spi@polinema.ac.id</p>
    </div>

    {{-- NOMOR SURAT --}}
    <div class="nomor-surat">
        <p><strong>{{ strtoupper($surat->jenis_surat) }}</strong></p>
        <p><strong>Nomor: {{ $surat->nomor_surat }}</strong></p>
    </div>

    {{-- META SURAT --}}
    <div class="meta-surat">
        <table>
            <tr>
                <td>Kepada</td>
                <td>: {{ $surat->tujuan_surat }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: {{ $surat->perihal }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</td>
            </tr>
            @if ($surat->tipe_referensi != 'Tanpa Referensi')
                <tr>
                    <td>Referensi</td>
                    <td>: {{ $surat->tipe_referensi }} - {{ $surat->referensi_nama }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- ISI SURAT --}}
    <div class="isi-surat">
        {{ $surat->isi_surat }}
    </div>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <p>Malang, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</p>
        <p><strong>Kepala Satuan Pengawas Internal</strong></p>
        <p><strong>Politeknik Negeri Malang</strong></p>
        <div class="ttd-space"></div>
        <p><strong><u>____________________</u></strong></p>
        <p>NIP. ____________________</p>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p><em>Dokumen ini dibuat melalui Sistem Informasi SPI Politeknik Negeri Malang</em></p>
        <p><em>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</em></p>
    </div>
</body>

</html>
