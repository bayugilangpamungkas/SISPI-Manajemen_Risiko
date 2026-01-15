<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Template Manajemen Risiko {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            color: #333;
        }

        .info {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #4472C4;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #ddd;
        }

        td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>TEMPLATE MANAJEMEN RISIKO</h2>
        <h3>TAHUN {{ $tahun }}</h3>
        @if ($unitKerja != 'all')
            <h4>{{ strtoupper($unitKerja) }}</h4>
        @endif
    </div>

    <div class="info">
        <strong>Tanggal Generate:</strong> {{ date('d F Y, H:i') }} WIB<br>
        <strong>Total Data:</strong> {{ $petas->count() }} risiko<br>
        <strong>Unit Kerja:</strong> {{ $unitKerja == 'all' ? 'Semua Unit Kerja' : $unitKerja }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Unit Kerja</th>
                <th width="8%">Kode Unit</th>
                <th width="15%">Kegiatan</th>
                <th width="10%">Kategori</th>
                <th width="20%">Judul Risiko</th>
                <th width="8%">Kode Registrasi</th>
                <th width="5%">Kemungkinan</th>
                <th width="5%">Dampak</th>
                <th width="5%">Skor</th>
                <th width="9%">Tingkat Risiko</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($petas as $peta)
                @php
                    $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

                    if ($skorTotal >= 20) {
                        $badgeClass = 'badge-danger';
                        $badgeText = 'Extreme';
                    } elseif ($skorTotal >= 15) {
                        $badgeClass = 'badge-warning';
                        $badgeText = 'High';
                    } elseif ($skorTotal >= 10) {
                        $badgeClass = 'badge-info';
                        $badgeText = 'Moderate';
                    } else {
                        $badgeClass = 'badge-success';
                        $badgeText = 'Low';
                    }

                    $unitKerjaModel = \App\Models\UnitKerja::where('nama_unit_kerja', $peta->jenis)->first();
                    $kodeUnit = $unitKerjaModel ? $unitKerjaModel->kode_unit : '-';
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td><strong>{{ $peta->jenis }}</strong></td>
                    <td style="text-align: center;">{{ $kodeUnit }}</td>
                    <td>{{ $peta->kegiatan->judul ?? '-' }}</td>
                    <td style="text-align: center;">{{ $peta->kategori }}</td>
                    <td>{{ $peta->judul }}</td>
                    <td style="text-align: center;">{{ $peta->kode_regist }}</td>
                    <td style="text-align: center;">{{ $peta->skor_kemungkinan }}</td>
                    <td style="text-align: center;">{{ $peta->skor_dampak }}</td>
                    <td style="text-align: center;"><strong>{{ $skorTotal }}</strong></td>
                    <td style="text-align: center;">
                        <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><em>Template ini digunakan untuk keperluan audit dan pelaporan</em></p>
        <p><strong>Sistem Informasi Satuan Pengawasan Internal (SISPI)</strong></p>
    </div>
</body>

</html>
