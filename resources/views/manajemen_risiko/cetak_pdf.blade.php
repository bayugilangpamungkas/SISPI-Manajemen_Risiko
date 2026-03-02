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
         |  BASE
         ============================================================ */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
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
            border-bottom: 3px solid #000;
            padding-bottom: 4px;
        }

        /*
         * ✅ PERBAIKAN LOGO:
         * Ubah vertical-align dari 'middle' → 'top'
         * dan padding-top: 0 agar logo rata atas dengan teks kop
         */
        .kop-table td {
            border: none;
            padding: 0 4px;
            /* ← padding-top 0, bukan 2px */
            vertical-align: top;
            /* ← top, bukan middle */
        }

        .kop-logo {
            width: 72px;
            text-align: center;
        }

        .kop-logo img {
            width: 68px;
            height: auto;
            margin-top: 0;
            /* ← pastikan tidak ada offset atas */
        }

        .kop-teks {
            text-align: center;
            padding: 0 6px;
        }

        .kop-institusi {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0 0 1px 0;
            line-height: 1.3;
        }

        .kop-unit {
            font-size: 13.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 2px 0;
        }

        .kop-alamat {
            font-size: 8pt;
            margin: 1px 0 0 0;
            color: #222;
        }

        /* ============================================================
         |  JUDUL DOKUMEN
         ============================================================ */
        .judul-dokumen {
            text-align: center;
            margin: 7px 0 6px 0;
        }

        .judul-dokumen h2 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 1px 0;
            text-decoration: underline;
        }

        .judul-dokumen p {
            font-size: 8.5pt;
            margin: 1px 0 0 0;
            color: #333;
        }

        /* ============================================================
         |  TABEL IDENTITAS AUDIT (2 kolom informasi)
         ============================================================ */
        .tbl-identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: 1.5px solid #000;
        }

        .tbl-identitas td {
            padding: 3.5px 7px;
            font-size: 8.5pt;
            vertical-align: top;
            border: 1px solid #555;
            line-height: 1.3;
        }

        .tbl-identitas .lbl {
            font-weight: bold;
            white-space: nowrap;
            width: 13%;
            background-color: #f0f0f0;
        }

        .tbl-identitas .sep {
            width: 1%;
            text-align: center;
            padding: 3.5px 2px;
            border-left: none;
            border-right: none;
            background-color: #f0f0f0;
        }

        .tbl-identitas .val {
            width: 35%;
        }

        /* pemisah tengah antara kiri–kanan */
        .tbl-identitas .mid-gap {
            width: 3%;
            border: 1px solid #555;
            /* Tambahkan border agar garis tidak terputus */
            background-color: #fff;
            padding: 0;
        }

        /* ============================================================
         |  BADGE LEVEL RISIKO
         ============================================================ */
        .badge-risiko {
            display: inline-block;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 2px 14px;
            border-radius: 2px;
            letter-spacing: 0.5px;
        }

        .lvl-extreme {
            background-color: #C00000;
            color: #fff;
        }

        .lvl-high {
            background-color: #FF6600;
            color: #fff;
        }

        .lvl-moderate {
            background-color: #FFD700;
            color: #000;
        }

        .lvl-low {
            background-color: #70AD47;
            color: #fff;
        }

        /* ============================================================
         |  TABEL ISI AUDIT (3 kolom utama)
         ============================================================ */
        .tbl-audit {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 7px;
        }

        .tbl-audit th {
            background-color: #1F3864;
            color: #fff;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: center;
            padding: 5px 7px;
            border: 1px solid #3A5A9B;
            letter-spacing: 0.3px;
        }

        .tbl-audit td {
            padding: 6px 8px;
            font-size: 8.5pt;
            vertical-align: top;
            border: 1px solid #555;
            line-height: 1.45;
        }

        /* zebra ringan pada sel konten */
        .tbl-audit td.col-pengendalian {
            background-color: #FAFBFF;
        }

        .tbl-audit td.col-mitigasi {
            background-color: #FFFEFA;
        }

        .tbl-audit td.col-komentar {
            background-color: #FFFBF0;
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
            font-weight: bold;
            color: #C00000;
            display: block;
            margin-bottom: 3px;
        }

        .isi-kosong {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 30px 0;
        }

        /* ── Komentar bernomor ── */
        .komentar-item {
            display: block;
            margin-bottom: 6px;
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
            margin-top: 6px;
            padding: 4px 6px;
            background-color: #f5f5f5;
            border-left: 3px solid #888;
            font-size: 8pt;
            line-height: 1.4;
        }

        .mitigasi-kepada-label {
            font-weight: bold;
            color: #555;
        }

        /* ============================================================
         |  BAGIAN TANDA TANGAN
         ============================================================ */
        .ttd-section {
            width: 100%;
            margin-top: 6px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-table td {
            border: none;
            padding: 0 10px;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .ttd-kota {
            font-size: 8.5pt;
            margin-bottom: 2px;
        }

        .ttd-jabatan {
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 0;
        }

        .ttd-ruang {
            height: 48px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 8.5pt;
        }

        .ttd-nip {
            font-size: 8pt;
        }

        /* ============================================================
         |  FOOTER HALAMAN
         ============================================================ */
        .footer-doc {
            border-top: 1px solid #999;
            margin-top: 8px;
            padding-top: 3px;
            font-size: 7.5pt;
            color: #555;
            text-align: center;
        }

        /* ── Kolom Mitigasi ── */
        .mitigasi-strategi {
            display: block;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
            margin-bottom: 8px;
        }

        .mitigasi-divider {
            border: none;
            border-top: 1px solid #ccc;
            margin: 6px 0;
        }

        .status-konfirmasi-label {
            display: block;
            font-weight: bold;
            font-size: 8.5pt;
            text-align: center;
            margin-bottom: 4px;
        }

        .status-konfirmasi-row {
            display: block;
            text-align: center;
            font-size: 8.5pt;
            line-height: 1.8;
        }

        /* ── Komentar: nomor bold + judul bold + teks biasa justify ── */
        .komentar-item {
            display: block;
            margin-bottom: 5px;
            line-height: 1.55;
            text-align: justify;
        }

        .komentar-no {
            font-weight: bold;
        }

        .komentar-judul {
            font-weight: bold;
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
                    <p class="kop-institusi">Politeknik Negeri Malang</p>
                    <p class="kop-unit">Satuan Pengawas Internal</p>
                    <p class="kop-alamat">
                        Jl. Soekarno-Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141
                        &nbsp;|&nbsp; Telp. (0341) 404424 &nbsp;|&nbsp; spi.polinema.ac.id
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================================================================
     JUDUL DOKUMEN
     ================================================================ --}}
    <div class="judul-dokumen">
        <h2>Lembar Monitoring dan Evaluasi Manajemen Risiko Unit</h2>
        <p>Laporan Hasil Audit – Tahun Anggaran {{ $hasilAudit->tahun_anggaran ?? date('Y') }}</p>
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
                            <span style="color:#888; font-style:italic;">— Tidak ada komentar —</span>
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
                <td>
                    <p class="ttd-jabatan">Unit Kerja</p>
                    <p class="ttd-kota">{{ $peta->jenis }}</p>
                    <div class="ttd-ruang"></div>
                    <p class="ttd-nama">{{ $namaUserUnitKerja ?? ($user->name ?? 'DATA KOSONG') }}</p>
                    <p class="ttd-nip">NIP.{{ $hasilAudit->auditor->nip ?? '-' }}</p>
                </td>

                {{-- Kanan: Auditor / Pemonev --}}
                <td>
                    <p class="ttd-kota">Malang, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="ttd-jabatan">Pemonev</p>
                    <div class="ttd-ruang"></div>
                    <p class="ttd-nama">
                        {{ $hasilAudit->nama_pemonev ?? ($user->name ?? 'Usman Nurhasan, S.Kom., M.T.') }}
                    </p>
                    <p class="ttd-nip">
                        NIP. {{ $hasilAudit->nip_pemonev ?? ($user->nip ?? '–') }}
                    </p>
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
