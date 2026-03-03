<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }} - {{ $surat->jenis_surat }} </title>
    <style>
        /* ============================================================
         |  PAGE SETUP — A4 PORTRAIT
         ============================================================ */
        @page {
            size: A4 portrait;
            margin: 1.5cm 2.5cm 2cm 2.5cm;
        }

        /* ============================================================
         |  BASE — Times New Roman untuk seluruh dokumen
         ============================================================ */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* ============================================================
         |  KOP SURAT
         ============================================================ */
        .kop-surat {
            width: 100%;
            /* Garis pembatas kop — double sesuai standar surat resmi */
            border-bottom: 3px double #000;
            margin: 0 0 12px 0;
            padding: 0 0 8px 0;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 100px;
            vertical-align: middle;
            text-align: left;
            padding-right: 12px;
        }

        .logo-cell img {
            width: 100px;
            height: auto;
            display: block;
        }

        .text-cell {
            text-align: center;
            vertical-align: middle;
            padding-left: 5px;
        }

        /* ── Baris 1: Kementerian & Politeknik — 12pt, normal, UPPERCASE ── */
        .kop-surat .kop-institusi {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
            line-height: 1.3;
        }

        /* ── Baris 2: Satuan Pengawas Internal — 14pt, BOLD, UPPERCASE ── */
        .kop-surat .kop-unit {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 3px 0;
            line-height: 1.3;
        }

        /* ── Baris 3: Alamat & Kontak — 9pt, normal ── */
        .kop-surat .kop-alamat {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            font-weight: normal;
            margin: 1px 0 0 0;
            color: #000;
            line-height: 1.45;
        }

        /* ============================================================
         |  JUDUL SURAT
         ============================================================ */
        .judul-container {
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
            margin: 20px 0 16px 0;
        }

        .judul-container strong {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .judul-container span {
            font-family: 'Times New Roman', Times, serif;
            display: block;
            margin-top: 6px;
            font-size: 12pt;
            font-weight: normal;
        }

        /* ============================================================
         |  META SURAT (Kepada, Perihal, Tanggal, Referensi)
         ============================================================ */
        .meta-surat {
            margin: 0 0 20px 0;
        }

        .meta-surat table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-surat td {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            padding: 3px 0;
            vertical-align: top;
            line-height: 1.5;
            color: #000;
        }

        .meta-surat td:first-child {
            width: 120px;
            font-weight: normal;
        }

        .meta-surat td:nth-child(2) {
            width: 10px;
            padding: 3px 4px;
        }

        .meta-surat td:last-child {
            font-weight: normal;
        }

        /* ── Referensi ── */
        .referensi-item {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
        }

        .referensi-label {
            font-weight: normal;
        }

        .referensi-value {
            font-weight: normal;
        }

        /* ============================================================
         |  ISI SURAT — render HTML dari Summernote (DomPDF-safe)
         ============================================================ */
        .isi-surat {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            text-align: justify;
            text-justify: inter-word;
            margin: 40px 0 25px 0;
            min-height: 180px;
            color: #000;
        }

        /* Setiap <p> dari Summernote */
        .isi-surat p {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.8;
            margin: 0 0 10px 0;
            padding: 0;
            text-align: justify;
            color: #000;
        }

        .isi-surat p:last-child {
            margin-bottom: 0;
        }

        /* List dari Summernote */
        .isi-surat ul,
        .isi-surat ol {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.8;
            margin: 0 0 10px 1.5em;
            padding: 0;
        }

        .isi-surat li {
            margin-bottom: 4px;
        }

        /* Bold, italic, underline dari Summernote */
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
         |  TANDA TANGAN
         ============================================================ */
        .ttd {
            font-family: 'Times New Roman', Times, serif;
            margin-top: 40px;
            float: right;
            width: 280px;
            text-align: center;
        }

        .ttd p {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 2px 0;
            line-height: 1.5;
            color: #000;
        }

        .ttd-space {
            height: 70px;
            margin: 8px 0;
        }

        .ttd strong {
            font-family: 'Times New Roman', Times, serif;
            /* font-weight: bold; */
        }

        .ttd-line {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px solid #000;
            margin: 4px 0;
        }

        /* ============================================================
         |  FOOTER
         ============================================================ */
        .footer {
            font-family: 'Times New Roman', Times, serif;
            clear: both;
            margin-top: 50px;
            padding-top: 8px;
            border-top: 1px solid #000;
            font-size: 8pt;
            font-style: italic;
            color: #000;
            line-height: 1.4;
        }

        .footer p {
            margin: 2px 0;
        }

        /* ============================================================
         |  PRINT OPTIMIZATION
         ============================================================ */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .kop-surat {
                page-break-after: avoid;
            }

            .ttd {
                page-break-inside: avoid;
            }
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
                    <img src="{{ public_path('img/logo kop polinema.png') }}" alt="Logo Polinema">
                </td>
                <td class="text-cell">
                    <p class="kop-institusi">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</p>
                    <p class="kop-institusi">POLITEKNIK NEGERI MALANG</p>
                    <p class="kop-unit">SATUAN PENGAWAS INTERNAL</p>
                    <p class="kop-alamat">Jl. Soekarno Hatta No. 9 Malang 65141</p>
                    <p class="kop-alamat">Telp. (0341) 404424 | Email: spi@polinema.ac.id | Laman: spi.polinema.ac.id
                    </p>
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
                <td>: </td>
                <td>{{ $surat->tujuan_surat }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: </td>
                <td>{{ $surat->perihal }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: </td>
                <td>{{ $surat->tanggal_surat->translatedFormat('d F Y') }}</td>
            </tr>
            @if ($surat->tipe_referensi != 'Tanpa Referensi')
                <tr>
                    <td>Referensi</td>
                    <td>: </td>
                    <td>
                        @php
                            $kodeKegiatan = '-';
                            // Mengambil data melalui relasi petaRisiko yang ada di model
                            $peta = $surat->petaRisiko;

                            if ($peta && $peta->kegiatan) {
                                $keg = $peta->kegiatan;
                                if (!empty($keg->kode_regist)) {
                                    $kodeKegiatan = $keg->kode_regist;
                                } elseif (!empty($keg->id_kegiatan)) {
                                    $kodeKegiatan = $keg->id_kegiatan;
                                } elseif (!empty($keg->kode)) {
                                    $kodeKegiatan = $keg->kode;
                                } else {
                                    // Fallback: buat format KEG-TAHUN-ID
                                    $kodeKegiatan = 'KEG-' . date('Y') . '-' . str_pad($keg->id, 3, '0', STR_PAD_LEFT);
                                }
                            } elseif ($peta && $peta->id_kegiatan) {
                                // Fallback jika relasi kegiatan tidak terload tapi ID ada
                                $kegiatanManual = \App\Models\Kegiatan::find($peta->id_kegiatan);
                                if ($kegiatanManual) {
                                    $kodeKegiatan =
                                        $kegiatanManual->kode_regist ??
                                        'KEG-' . date('Y') . '-' . str_pad($kegiatanManual->id, 3, '0', STR_PAD_LEFT);
                                }
                            }
                        @endphp

                        <span class="referensi-item">
                            <span class="referensi-label">{{ $surat->tipe_referensi }}</span>:
                            <span class="referensi-value">{{ $kodeKegiatan }}</span>
                        </span>
                    </td>
                </tr>
            @endif
        </table>
    </div>

    {{-- ISI SURAT — render HTML langsung dari Summernote --}}
    <div class="isi-surat">{!! $surat->isi_surat !!}</div>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        {{-- <p>Malang, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</p> --}}
        <p>Ketua SPI</p>
        <div class="ttd-space"></div>
        <p>{{ $ketuaSPI->name ?? '-' }}</p>
        <p>NIP. {{ $ketuaSPI->nip ?? '-' }}</p>
    </div>

    <div style="clear: both;"></div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dokumen ini dibuat melalui Sistem Informasi SPI Politeknik Negeri Malang</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>

</html>
