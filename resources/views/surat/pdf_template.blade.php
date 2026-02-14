<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        /* Mengatur margin halaman agar kop naik ke atas */
        @page {
            margin: 1cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Standar Polinema dengan Logo */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 80px;
            /* Lebar area logo */
            vertical-align: middle;
            text-align: left;
        }

        .logo-cell img {
            width: 80px;
            /* Sesuaikan ukuran logo */
            height: auto;
        }

        .text-cell {
            text-align: center;
            vertical-align: middle;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-surat h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .kop-surat p {
            margin: 0;
            font-size: 9pt;
        }

        /* Nomor & Judul Surat */
        .judul-container {
            text-align: center;
            margin: 20px 0;
        }

        .judul-container strong {
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .judul-container span {
            display: block;
            margin-top: 5px;
        }

        /* Meta Data Surat */
        .meta-surat {
            margin-bottom: 25px;
        }

        .meta-surat table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-surat td {
            padding: 3px 0;
            vertical-align: top;
        }

        .meta-surat td:first-child {
            width: 100px;
        }

        /* Isi Surat */
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
            white-space: pre-wrap;
            min-height: 150px;
        }

        /* Tanda Tangan */
        .ttd {
            margin-top: 40px;
            float: right;
            width: 300px;
            text-align: center;
        }

        .ttd p {
            margin: 2px 0;
        }

        .ttd-space {
            height: 75px;
        }

        /* Footer */
        .footer {
            clear: both;
            margin-top: 50px;
            font-size: 9pt;
            color: #444;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-style: italic;
        }
    </style>
</head>

<body>
    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    {{-- Menggunakan public_path untuk memastikan logo terbaca saat generate PDF --}}
                    <img src="{{ public_path('img/logo polinema.png') }}" alt="Logo">
                </td>
                <td class="text-cell">
                    <h2>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h2>
                    <h1>POLITEKNIK NEGERI MALANG</h1>
                    <h2>SATUAN PENGAWAS INTERNAL</h2>
                    <p>Jl. Soekarno Hatta No. 9 Malang 65141</p>
                    <p>Telp. (0341) 404424 | Email: spi@polinema.ac.id | Laman: spi.polinema.ac.id</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- JUDUL & NOMOR --}}
    <div class="judul-container">
        <strong>{{ strtoupper($surat->jenis_surat) }}</strong>
        <span>Nomor: {{ $surat->nomor_surat }}</span>
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

    <div style="clear: both;"></div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dokumen ini dibuat melalui Sistem Informasi SPI Politeknik Negeri Malang</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>

</html>
