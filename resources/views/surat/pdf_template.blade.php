<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }} - {{ $surat->jenis_surat }}</title>
    <style>
        /* ============================================================
         |  PAGE SETUP — A4 PORTRAIT
         ============================================================ */
        @page {
            size: A4 portrait;
            margin: 1.5cm 2.5cm 2cm 2.5cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* ============================================================
         |  KOP SURAT
         ============================================================ */
        .kop-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .kop-logo-cell {
            width: 90px;
            vertical-align: middle;
            text-align: center;
            padding-right: 10px;
        }

        .kop-logo-cell img {
            width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .kop-text-cell {
            vertical-align: middle;
            text-align: center;
            padding: 0;
        }

        .kop-line1 {
            font-size: 11pt;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin: 0;
            line-height: 1.35;
        }

        .kop-line2 {
            font-size: 12pt;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin: 0;
            line-height: 1.35;
        }

        .kop-line3 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
            line-height: 1.3;
        }

        .kop-alamat {
            font-size: 8.5pt;
            font-weight: normal;
            margin: 2px 0 0 0;
            line-height: 1.45;
        }

        /* Garis pembatas kop — double border sesuai standar */
        .kop-border-bottom {
            border-bottom: 4px double #000;
            margin-bottom: 14px;
            padding-bottom: 4px;
        }

        /* ============================================================
         |  BLOK META SURAT
         |  Layout: tabel 2 kolom besar
         |  Kiri  → Nomor / Lampiran / Hal
         |  Kanan → Tanggal (rata kanan, sejajar baris Nomor)
         ============================================================ */
        .blok-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        /* Kolom kiri: berisi sub-tabel Nomor-Lampiran-Hal */
        .blok-meta>tbody>tr>td.meta-kiri {
            vertical-align: top;
            width: 65%;
            padding: 0;
        }

        /* Kolom kanan: tanggal rata kanan, sejajar baris Nomor */
        .blok-meta>tbody>tr>td.meta-kanan {
            vertical-align: top;
            width: 35%;
            text-align: right;
            font-size: 12pt;
            padding-top: 1px;
            white-space: nowrap;
        }

        /* Sub-tabel kiri: Nomor / Lampiran / Hal */
        .sub-meta {
            border-collapse: collapse;
            width: auto;
        }

        .sub-meta td {
            font-size: 12pt;
            padding: 1px 0;
            vertical-align: top;
            line-height: 1.65;
        }

        /* Kolom label — underline sesuai standar surat resmi */
        .sub-meta td.lbl {
            width: 85px;
            text-decoration: underline;
            white-space: nowrap;
        }

        /* Kolom titik dua */
        .sub-meta td.sep {
            width: 16px;
            padding: 1px 5px;
            text-decoration: none;
        }

        /* Kolom nilai */
        .sub-meta td.val {
            font-weight: normal;
        }

        /* ============================================================
         |  ISI SURAT
         ============================================================ */
        .isi-surat {
            font-size: 12pt;
            text-align: justify;
            text-justify: inter-word;
            margin: 16px 0 0 0;
            color: #000;
        }

        .isi-surat p {
            font-size: 12pt;
            line-height: 1.8;
            margin: 0 0 12px 0;
            padding: 0;
            text-align: justify;
        }

        .isi-surat p:last-child {
            margin-bottom: 0;
        }

        .isi-surat ul,
        .isi-surat ol {
            font-size: 12pt;
            line-height: 1.8;
            margin: 0 0 10px 1.8em;
            padding: 0;
        }

        .isi-surat li {
            margin-bottom: 4px;
        }

        .isi-surat strong,
        .isi-surat b {
            font-weight: bold;
        }

        .isi-surat em,
        .isi-surat i {
            font-style: italic;
        }

        .isi-surat u {
            text-decoration: underline;
        }

        /* ============================================================
         |  TANDA TANGAN + FOOTER — fixed di bawah halaman
         |  Dibungkus satu blok agar keduanya selalu di bawah
         ============================================================ */
        .blok-bawah {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
        }

        /* Tabel TTD — 2 kolom, TTD di kanan */
        .ttd-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .ttd-kiri {
            width: 55%;
            vertical-align: bottom;
        }

        .ttd-kanan {
            width: 45%;
            vertical-align: top;
            text-align: center;
        }

        .ttd-kanan p {
            font-size: 12pt;
            margin: 1px 0;
            line-height: 1.55;
            text-align: center;
        }

        .ttd-space {
            height: 80px;
        }

        .ttd-nama {
            font-size: 12pt;
            text-decoration: underline;
            margin: 0;
            line-height: 1.55;
            text-align: center;
        }

        .ttd-nip {
            font-size: 12pt;
            margin: 0;
            line-height: 1.55;
            text-align: center;
        }

        /* Footer */
        .footer {
            padding-top: 6px;
            border-top: 1px solid #000;
            font-size: 8pt;
            font-style: italic;
            color: #555;
            line-height: 1.4;
        }

        .footer p {
            margin: 2px 0;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .blok-bawah {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    {{-- ══════════════════════════════════════════
         KOP SURAT
    ══════════════════════════════════════════ --}}
    <div class="kop-border-bottom">
        <table class="kop-outer">
            <tr>
                <td class="kop-logo-cell">
                    <img src="{{ public_path('img/logo kop polinema.png') }}" alt="Logo Polinema">
                </td>
                <td class="kop-text-cell">
                    <p class="kop-line1">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</p>
                    <p class="kop-line2">POLITEKNIK NEGERI MALANG</p>
                    <p class="kop-line3">SATUAN PENGAWAS INTERNAL</p>
                    <p class="kop-alamat">Jalan Soekarno Hatta Nomor 9 Jatimulyo, Lowokwaru, Malang 65141</p>
                    <p class="kop-alamat">Telepon (0341) 404424, 404425, Faksimile (0341) 404420 &nbsp;|&nbsp; Laman
                        www.polinema.ac.id</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════════════════════════════════════════
         BLOK META: Nomor / Lampiran / Hal  +  Tanggal
         Layout dua kolom: kiri = meta, kanan = tanggal
    ══════════════════════════════════════════ --}}
    <table class="blok-meta">
        <tbody>
            <tr>
                {{-- ── KIRI: Nomor, Lampiran, Hal ── --}}
                <td class="meta-kiri">
                    <table class="sub-meta">
                        <tbody>
                            <tr>
                                <td class="lbl">Nomor</td>
                                <td class="sep">:</td>
                                <td class="val">{{ $surat->nomor_surat }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Lampiran</td>
                                <td class="sep">:</td>
                                <td class="val">{{ $surat->lampiran ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="lbl">Hal</td>
                                <td class="sep">:</td>
                                <td class="val">{{ $surat->perihal }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                {{-- ── KANAN: Tanggal (rata kanan, sejajar baris Nomor) ── --}}
                <td class="meta-kanan">
                    {{ $surat->tanggal_surat->translatedFormat('d F Y') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════════════════════════════════════
         ISI SURAT
    ══════════════════════════════════════════ --}}
    <div class="isi-surat">{!! $surat->isi_surat !!}</div>

    {{-- ══════════════════════════════════════════
         BLOK BAWAH: TANDA TANGAN + FOOTER
    ══════════════════════════════════════════ --}}
    <div class="blok-bawah">
        <table class="ttd-wrapper">
            <tr>
                <td class="ttd-kiri"></td>
                <td class="ttd-kanan">
                    <p>Ketua SPI,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $ketuaSPI->name ?? '-' }}</p>
                    <p class="ttd-nip">NIP. {{ $ketuaSPI->nip ?? '-' }}</p>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Dokumen ini dibuat melalui Sistem Informasi SPI Politeknik Negeri Malang</p>
            <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
    </div>

</body>

</html>
