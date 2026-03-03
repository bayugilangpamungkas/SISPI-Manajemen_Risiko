<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Audit – {{ $peta->kode_regist }}</title>
    <style>
        /* ============================================================
         |  PAGE SETUP — A4 LANDSCAPE
         ============================================================ */
        @page {
            size: A4 landscape;
            margin-top: 15mm;
            margin-bottom: 18mm;
            margin-left: 20mm;
            margin-right: 15mm;
        }

        /* ============================================================
         |  BASE — Times New Roman untuk seluruh dokumen
         ============================================================ */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* ============================================================
         |  KOP SURAT
         ============================================================ */
        .kop-wrapper {
            width: 100%;
            margin-bottom: 0;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            /* Garis pembatas kop surat */
            border-bottom: 3px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .kop-table td {
            border: none;
            padding: 0 4px;
            vertical-align: top;
        }

        .kop-logo {
            width: 100px;
            text-align: center;
        }

        .kop-logo img {
            width: 120px;
            height: auto;
            margin-top: 0;
        }

        .kop-teks {
            text-align: center;
            padding: 0 6px;
        }

        /* ── Baris 1: Kementerian + Politeknik — 12pt, normal, UPPERCASE ── */
        .kop-institusi {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            font-weight: normal;
            font-style: normal;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.3;
        }

        /* ── Baris 2: Satuan Pengawas Internal — 14pt, BOLD, UPPERCASE ── */
        .kop-unit {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 3px 0 3px 0;
            line-height: 1.3;
        }

        /* ── Baris 3: Alamat & Kontak — 9pt, normal ── */
        .kop-alamat {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            font-weight: normal;
            margin: 1px 0 0 0;
            color: #000;
            line-height: 1.45;
        }

        /* ============================================================
         |  JUDUL DOKUMEN
         ============================================================ */
        .judul-dokumen {
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
            margin: 8px 0 6px 0;
        }

        .judul-dokumen h2 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
            /* text-decoration: underline; */
        }

        .judul-dokumen p {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 0;
            color: #000;
        }

        /* ============================================================
         |  TABEL IDENTITAS AUDIT
         ============================================================ */
        .tbl-identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: 1.5px solid #000;
        }

        .tbl-identitas td {
            font-family: 'Times New Roman', Times, serif;
            padding: 3px 7px;
            font-size: 10pt;
            vertical-align: top;
            border: 1px solid #000;
            line-height: 1.35;
        }

        .tbl-identitas .lbl {
            font-weight: bold;
            white-space: nowrap;
            width: 13%;
        }

        .tbl-identitas .sep {
            width: 1%;
            text-align: center;
            padding: 3px 2px;
            border-left: none;
            border-right: none;
        }

        .tbl-identitas .val {
            width: 35%;
        }

        .tbl-identitas .mid-gap {
            width: 3%;
            border: 1px solid #000;
            padding: 0;
        }

        /* ============================================================
         |  BADGE LEVEL RISIKO
         ============================================================ */
        .badge-risiko {
            display: inline-block;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            font-size: 10pt;
            padding: 1px 12px;
            border-radius: 2px;
            background-color: #fff;
            color: #000;
        }

        .lvl-extreme,
        .lvl-high,
        .lvl-moderate,
        .lvl-low {
            background-color: #fff;
            color: #000;
        }

        /* ============================================================
         |  TABEL ISI AUDIT (3 kolom)
         ============================================================ */
        .tbl-audit {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 6px;
        }

        .tbl-audit th {
            font-family: 'Times New Roman', Times, serif;
            background-color: #fff;
            color: #000;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            padding: 5px 7px;
            border: 1px solid #000;
        }

        .tbl-audit td {
            font-family: 'Times New Roman', Times, serif;
            padding: 6px 8px;
            font-size: 10pt;
            vertical-align: top;
            border: 1px solid #000;
            line-height: 1.45;
            background-color: #fff;
            color: #000;
        }

        .tbl-audit td.col-pengendalian,
        .tbl-audit td.col-mitigasi,
        .tbl-audit td.col-komentar {
            background-color: #fff;
        }

        .col-width-35 {
            width: 35%;
        }

        .col-width-33 {
            width: 33%;
        }

        .col-width-32 {
            width: 32%;
        }

        .isi-label {
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            color: #000;
            display: block;
            margin-bottom: 3px;
        }

        .isi-kosong {
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
            color: #000;
            font-style: italic;
            padding: 30px 0;
        }

        /* ── Komentar ── */
        .komentar-item {
            font-family: 'Times New Roman', Times, serif;
            display: block;
            margin-bottom: 5px;
            line-height: 1.55;
            text-align: justify;
        }

        .komentar-no {
            font-weight: bold;
            color: #000;
            margin-right: 2px;
        }

        .komentar-judul {
            font-weight: bold;
        }

        /* ── Mitigasi kepada ── */
        .mitigasi-kepada {
            display: block;
            margin-top: 6px;
            padding: 4px 6px;
            background-color: #fff;
            border-left: 3px solid #000;
            font-size: 9pt;
            line-height: 1.4;
        }

        .mitigasi-kepada-label {
            font-weight: bold;
            color: #000;
        }

        /* ── Strategi Mitigasi ── */
        .mitigasi-strategi {
            font-family: 'Times New Roman', Times, serif;
            display: block;
            font-size: 10pt;
            font-weight: normal;
            text-align: center;
            margin-bottom: 8px;
        }

        .mitigasi-divider {
            border: none;
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        .status-konfirmasi-label {
            font-family: 'Times New Roman', Times, serif;
            display: block;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            margin-bottom: 4px;
        }

        .status-konfirmasi-row {
            font-family: 'Times New Roman', Times, serif;
            display: block;
            text-align: center;
            font-size: 10pt;
            line-height: 1.8;
        }

        /* ============================================================
         |  TANDA TANGAN
         ============================================================ */
        .ttd-section {
            width: 100%;
            margin-top: 8px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-table td {
            font-family: 'Times New Roman', Times, serif;
            border: none;
            padding: 0 20px;
            width: 50%;
            vertical-align: top;
            font-size: 10pt;
        }

        .ttd-table td.ttd-kiri {
            text-align: center;
        }

        .ttd-table td.ttd-kanan {
            text-align: center;
        }

        .ttd-kota {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 0 0 2px 0;
        }

        .ttd-jabatan {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
        }

        .ttd-ruang {
            height: 50px;
        }

        .ttd-nama {
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
            margin: 0;
        }

        .ttd-nip {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            margin: 1px 0 0 0;
        }

        /* ============================================================
         |  FOOTER
         ============================================================ */
        .footer-doc {
            font-family: 'Times New Roman', Times, serif;
            border-top: 1px solid #000;
            margin-top: 8px;
            padding-top: 3px;
            font-size: 8pt;
            color: #000;
            text-align: center;
        }
    </style>
</head>

<body>

    @php
        $logoPath = public_path('img/logo kop polinema.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $levelValue = $hasilAudit->level_risiko ?? $levelText;
        $residualValue = $hasilAudit->risiko_residual ?? $residualText;

        $lvlClass = match (strtoupper($levelValue)) {
            'EXTREME' => 'lvl-extreme',
            'HIGH' => 'lvl-high',
            'MODERATE' => 'lvl-moderate',
            default => 'lvl-low',
        };

        $rvlClass = match (strtoupper($residualValue)) {
            'EXTREME' => 'lvl-extreme',
            'HIGH' => 'lvl-high',
            'MODERATE' => 'lvl-moderate',
            default => 'lvl-low',
        };
    @endphp

    {{-- ================================================================
     KOP SURAT
     ================================================================ --}}
    <div class="kop-wrapper">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo Polinema">
                    @endif
                </td>
                <td class="kop-teks">
                    <p class="kop-institusi">Kementerian Pendidikan Tinggi, Sains,</p>
                    <p class="kop-institusi">dan Teknologi</p>
                    <p class="kop-institusi">Politeknik Negeri Malang</p>
                    <p class="kop-unit">Satuan Pengawas Internal</p>
                    <p class="kop-alamat">Jalan Soekarno Hatta Nomor 9 Jatimulyo, Lowokwaru, Malang 65141</p>
                    <p class="kop-alamat">Telepon (0341) 404424, 404425, Faksimile (0341) 404420</p>
                    <p class="kop-alamat">Laman www.polinema.ac.id</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================================================================
     JUDUL DOKUMEN
     ================================================================ --}}
    <div class="judul-dokumen">
        <h2>Lembar Monitoring dan Evaluasi Manajemen Risiko Unit</h2>
        {{-- <p>Laporan Hasil Audit – Tahun Anggaran {{ $hasilAudit->tahun_anggaran ?? date('Y') }}</p> --}}
    </div>

    {{-- ================================================================
     IDENTITAS AUDIT — 2 panel berdampingan (7 kolom tabel)
     Struktur tiap baris: lbl | sep | val | mid-gap | lbl | sep | val
     Baris full-width   : lbl | sep | val[colspan=5 menutupi mid-gap+lbl+sep+val)
     ================================================================ --}}
    <table class="tbl-identitas">
        {{-- Baris 1: Unit Kerja | Pemonev --}}
        <tr>
            <td class="lbl">Unit Kerja</td>
            <td class="sep">:</td>
            <td class="val">{{ $peta->jenis }}</td>
            <td class="mid-gap"></td>
            <td class="lbl">Pemonev</td>
            <td class="sep">:</td>
            <td class="val">{{ $hasilAudit->nama_pemonev ?? ($user->name ?? '-') }}</td>
        </tr>
        {{-- Baris 2: Kode Risiko | Tahun Anggaran --}}
        <tr>
            <td class="lbl">Kode Risiko</td>
            <td class="sep">:</td>
            <td class="val">{{ $peta->kode_regist ?? '-' }}</td>
            <td class="mid-gap"></td>
            <td class="lbl">Tahun Anggaran</td>
            <td class="sep">:</td>
            <td class="val">{{ $hasilAudit->tahun_anggaran ?? date('Y') }}</td>
        </tr>
        {{-- Baris 3: Pernyataan Risiko — full width (colspan=5 menutupi mid-gap+lbl+sep+val) --}}
        <tr>
            <td class="lbl">Kegiatan</td>
            <td class="sep">:</td>
            <td class="val" colspan="5">
                {{ $hasilAudit->kegiatan ?? ($peta->kegiatan->judul ?? ($peta->judul ?? '-')) }}
            </td>
        </tr>
        {{-- Baris 4: Kegiatan — full width (colspan=5) --}}
        <tr>
            <td class="lbl">Pernyataan Risiko</td>
            <td class="sep">:</td>
            <td class="val" colspan="5">{{ $peta->pernyataan ?? '-' }}</td>
        </tr>
        {{-- Baris 5: Level Risiko | Risiko Residual --}}
        <tr>
            <td class="lbl">Level Risiko</td>
            <td class="sep">:</td>
            <td class="val">
                <span class="badge-risiko {{ $lvlClass }}">{{ $levelValue }}</span>
            </td>
            <td class="mid-gap"></td>
            <td class="lbl">Risiko Residual</td>
            <td class="sep">:</td>
            <td class="val">
                <span class="badge-risiko {{ $rvlClass }}">{{ $residualValue }}</span>
            </td>
        </tr>
    </table>

    {{-- ================================================================
     ISI AUDIT — 3 KOLOM: Pengendalian | Mitigasi | Komentar
     ================================================================ --}}
    <table class="tbl-audit">
        <thead>
            <tr>
                <th class="col-width-35">Pengendalian Risiko</th>
                <th class="col-width-33">Strategi Mitigasi</th>
                <th class="col-width-32">Komentar Auditor</th>
            </tr>
        </thead>
        <tbody>
            @if ($hasilAudit)
                <tr>
                    {{-- Kolom 1: Pengendalian --}}
                    <td class="col-pengendalian" style="min-height:130px;">
                        {{ $hasilAudit->pengendalian ?? '-' }}
                    </td>

                    {{-- Kolom 2: Mitigasi --}}
                    <td class="col-mitigasi" style="min-height:130px; text-align:center; vertical-align:middle;">
                        @php
                            $mitigasiRaw = $hasilAudit->mitigasi_label ?? ($hasilAudit->mitigasi ?? '-');
                            $mitigasiLabel = match ($mitigasiRaw) {
                                'Accept Risk' => 'Menerima Risiko',
                                'Share Risk' => 'Membagi Risiko',
                                'Transfer Risk' => 'Melimpahkan Risiko',
                                default => $mitigasiRaw,
                            };

                            $stAuditor = $peta->status_konfirmasi_auditor ?? null;
                            $stAuditee = $peta->status_konfirmasi_auditee ?? null;

                            $stAuditorLabel = match ($stAuditor) {
                                'Completed' => 'Sudah',
                                'Not Completed' => 'Belum',
                                default => $stAuditor,
                            };
                            $stAuditeeLabel = match ($stAuditee) {
                                'Completed' => 'Sudah',
                                'Not Completed' => 'Belum',
                                default => $stAuditee,
                            };
                        @endphp
                        <span class="mitigasi-strategi">{{ $mitigasiLabel }}</span>

                        @if ($stAuditee || $stAuditor)
                            <hr class="mitigasi-divider">
                            <span class="status-konfirmasi-label">Status Konfirmasi</span>
                            @if ($stAuditor)
                                <span class="status-konfirmasi-row">Auditor: {{ $stAuditorLabel }}</span>
                            @endif
                            @if ($stAuditee)
                                <span class="status-konfirmasi-row">Auditee: {{ $stAuditeeLabel }}</span>
                            @endif
                        @endif
                    </td>

                    {{-- Kolom 3: Komentar --}}
                    <td class="col-komentar" style="min-height:130px;">
                        @php
                            $rawKomentar = $hasilAudit->komentar_1 ?? '';
                            $baris = array_filter(
                                array_map('trim', preg_split('/\r\n|\r|\n/', $rawKomentar)),
                                fn($b) => $b !== '' && $b !== '-',
                            );
                            $baris = array_values($baris);
                        @endphp
                        @if (count($baris) > 0)
                            @foreach ($baris as $b)
                                @php
                                    $noStr = '';
                                    $judulStr = '';
                                    $sisaStr = $b;
                                    if (preg_match('/^(\d+\.\s*)(.+)$/', $b, $m)) {
                                        $noStr = rtrim($m[1]);
                                        $konten = $m[2];
                                        if (preg_match('/^([^:]{3,60}):\s*(.+)$/', $konten, $m2)) {
                                            $judulStr = $m2[1];
                                            $sisaStr = $m2[2];
                                        } else {
                                            $sisaStr = $konten;
                                        }
                                    }
                                @endphp
                                <span class="komentar-item">
                                    @if ($noStr)
                                        <span class="komentar-no">{{ $noStr }}</span>
                                    @endif
                                    @if ($judulStr)
                                        <span class="komentar-judul">{{ $judulStr }}:</span>
                                    @endif
                                    {{ $sisaStr }}
                                </span>
                            @endforeach
                        @else
                            <span style="color:#000; font-style:italic;">— Tidak ada komentar —</span>
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="3" class="isi-kosong">
                        Data hasil audit belum diisi oleh Auditor.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ================================================================
     TANDA TANGAN — 3 posisi sejajar
     ================================================================ --}}
    <div class="ttd-section">
        <table class="ttd-table">
            <tr>
                {{-- Kiri: Unit Kerja / Auditee --}}
                <td class="ttd-kiri">
                    <p class="ttd-jabatan">Unit Kerja</p>
                    <p class="ttd-kota">{{ $peta->jenis }}</p>
                    <div class="ttd-ruang"></div>
                    <p class="ttd-nama">{{ $namaUserUnitKerja ?? ($user->name ?? 'DATA KOSONG') }}</p>
                    <p class="ttd-nip">NIP. {{ $hasilAudit->auditor->nip ?? '-' }}</p>
                </td>

                {{-- Kanan: Auditor / Pemonev --}}
                <td class="ttd-kanan">
                    <p class="ttd-kota">Malang, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="ttd-jabatan">Pemonev</p>
                    <div class="ttd-ruang"></div>
                    <p class="ttd-nama">
                        {{ $hasilAudit->nama_pemonev ?? ($user->name ?? 'Usman Nurhasan, S.Kom., M.T.') }}</p>
                    <p class="ttd-nip">NIP. {{ $hasilAudit->nip_pemonev ?? ($user->nip ?? '–') }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================================================================
     FOOTER
     ================================================================ --}}
    <div class="footer-doc">
        Dicetak oleh sistem SISPI &nbsp;|&nbsp;
        SPI Politeknik Negeri Malang &nbsp;|&nbsp;
        {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
        Kode: {{ $peta->kode_regist ?? '-' }}
    </div>

</body>

</html>
