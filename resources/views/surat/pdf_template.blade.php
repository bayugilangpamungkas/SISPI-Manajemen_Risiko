<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* Kop Surat */
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            margin: 0 0 15px 0;
            padding: 0 0 8px 0;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 100px;
            /* sebelumnya 85px */
            vertical-align: middle;
            text-align: left;
            padding-right: 12px;
        }

        .logo-cell img {
            width: 100px;
            /* sebelumnya 85px */
            height: auto;
            display: block;
        }


        .text-cell {
            text-align: center;
            vertical-align: middle;
            padding-left: 5px;
        }

        .kop-surat h2 {
            margin: 2px 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.3;
        }

        .kop-surat h1 {
            margin: 3px 0;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        .kop-surat p {
            margin: 1px 0;
            font-size: 9pt;
            line-height: 1.4;
        }


        /* Judul Surat */
        .judul-container {
            text-align: center;
            margin: 25px 0 20px 0;
        }

        .judul-container strong {
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .judul-container span {
            display: block;
            margin-top: 8px;
            font-size: 12pt;
        }

        /* Meta Surat */
        .meta-surat {
            margin: 0 0 30px 0;
        }

        .meta-surat table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-surat td {
            padding: 4px 0;
            vertical-align: top;
            line-height: 1.5;
        }

        .meta-surat td:first-child {
            width: 110px;
            font-weight: normal;
        }

        .meta-surat td:nth-child(2) {
            padding-left: 5px;
        }

        /* Isi Surat */
        .isi-surat {
            text-align: justify;
            text-justify: inter-word;
            margin: 25px 0 30px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            min-height: 180px;
            line-height: 1.7;
        }

        /* Tanda Tangan */
        .ttd {
            margin-top: 50px;
            float: right;
            width: 320px;
            text-align: center;
        }

        .ttd p {
            margin: 3px 0;
            line-height: 1.4;
        }

        .ttd-space {
            height: 80px;
            margin: 10px 0;
        }

        .ttd strong {
            font-weight: bold;
        }

        .ttd-line {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px solid #000;
            margin: 5px 0;
        }

        /* Footer */
        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 12px;
            border-top: 1px solid #999;
            font-size: 8.5pt;
            color: #555;
            font-style: italic;
            line-height: 1.4;
        }

        .footer p {
            margin: 2px 0;
        }

        /* Print Optimization */
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
                    <td>:
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

    {{-- ISI SURAT --}}
    <div class="isi-surat">{{ $surat->isi_surat }}</div>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <p>Malang, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</p>
        <p><strong>Kepala Satuan Pengawas Internal</strong></p>
        <p><strong>Politeknik Negeri Malang</strong></p>
        <div class="ttd-space"></div>
        <p><strong><span class="ttd-line"></span></strong></p>
        <p>NIP. __________________________</p>
    </div>

    <div style="clear: both;"></div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dokumen ini dibuat melalui Sistem Informasi SPI Politeknik Negeri Malang</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>

</html>
