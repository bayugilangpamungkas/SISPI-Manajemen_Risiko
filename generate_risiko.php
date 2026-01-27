<?php

/**
 * Script untuk menambahkan risiko otomatis ke kegiatan yang belum punya risiko
 * 
 * Cara menjalankan:
 * 1. Via browser: http://localhost/sispi/generate_risiko.php
 * 2. Via terminal: php generate_risiko.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kegiatan;
use App\Models\Peta;
use Carbon\Carbon;

echo "🚀 Memulai proses penambahan risiko untuk kegiatan...\n\n";

$tahun = date('Y');
echo "📅 Tahun target: {$tahun}\n";

// Template risiko dengan variasi
$risikoTemplates = [
    'UPA TIK' => [
        [
            'judul' => 'Upgrade Infrastruktur Jaringan Terhambat',
            'kategori' => 'Operasional',
            'uraian' => 'Risiko keterlambatan atau hambatan dalam proses upgrade infrastruktur jaringan yang dapat mempengaruhi kinerja sistem TI',
            'kemungkinan' => 3,
            'dampak' => 4,
        ],
        [
            'judul' => 'Gangguan Sistem Server',
            'kategori' => 'Teknologi',
            'uraian' => 'Risiko gangguan atau downtime pada sistem server yang dapat mengganggu layanan',
            'kemungkinan' => 2,
            'dampak' => 4,
        ],
        [
            'judul' => 'Keamanan Data dan Informasi',
            'kategori' => 'Keamanan',
            'uraian' => 'Risiko kebocoran atau kerusakan data penting akibat serangan cyber atau human error',
            'kemungkinan' => 2,
            'dampak' => 5,
        ],
        [
            'judul' => 'Kegagalan Sistem Backup Data',
            'kategori' => 'Teknologi',
            'uraian' => 'Risiko kehilangan data karena sistem backup tidak berjalan optimal',
            'kemungkinan' => 2,
            'dampak' => 4,
        ],
    ],
    'default' => [
        [
            'judul' => 'Keterlambatan Pelaksanaan Kegiatan',
            'kategori' => 'Operasional',
            'uraian' => 'Risiko tidak tercapainya target waktu pelaksanaan kegiatan yang telah direncanakan',
            'kemungkinan' => 3,
            'dampak' => 3,
        ],
        [
            'judul' => 'Anggaran Tidak Mencukupi',
            'kategori' => 'Keuangan',
            'uraian' => 'Risiko kekurangan dana untuk menyelesaikan kegiatan sesuai rencana',
            'kemungkinan' => 2,
            'dampak' => 4,
        ],
        [
            'judul' => 'Kurangnya SDM Kompeten',
            'kategori' => 'Sumber Daya Manusia',
            'uraian' => 'Risiko keterbatasan sumber daya manusia yang memiliki kompetensi sesuai kebutuhan kegiatan',
            'kemungkinan' => 2,
            'dampak' => 3,
        ],
        [
            'judul' => 'Koordinasi Antar Unit Tidak Optimal',
            'kategori' => 'Operasional',
            'uraian' => 'Risiko hambatan komunikasi dan koordinasi antar unit kerja terkait',
            'kemungkinan' => 3,
            'dampak' => 2,
        ],
        [
            'judul' => 'Perubahan Regulasi atau Kebijakan',
            'kategori' => 'Legal & Compliance',
            'uraian' => 'Risiko perubahan aturan atau kebijakan yang mempengaruhi pelaksanaan kegiatan',
            'kemungkinan' => 2,
            'dampak' => 3,
        ],
        [
            'judul' => 'Kualitas Output Tidak Sesuai Standar',
            'kategori' => 'Kualitas',
            'uraian' => 'Risiko hasil kegiatan tidak memenuhi standar mutu yang ditetapkan',
            'kemungkinan' => 2,
            'dampak' => 4,
        ],
    ],
];

function getTingkatRisiko($kemungkinan, $dampak)
{
    $skor = $kemungkinan * $dampak;

    if ($skor >= 15) {
        return 'Extreme';
    } elseif ($skor >= 10) {
        return 'High';
    } elseif ($skor >= 5) {
        return 'Moderate';
    } else {
        return 'Low';
    }
}

// Get semua kegiatan
$allKegiatans = Kegiatan::with('unitKerja')->get();
echo "📊 Total kegiatan ditemukan: " . $allKegiatans->count() . "\n\n";

$totalAdded = 0;
$kegiatanTanpaRisiko = 0;
$kegiatanDiproses = [];

foreach ($allKegiatans as $kegiatan) {
    // Cek apakah kegiatan ini punya risiko di tahun ini
    $hasRisiko = Peta::where('id_kegiatan', $kegiatan->id)
        ->whereYear('created_at', $tahun)
        ->exists();

    if (!$hasRisiko) {
        $kegiatanTanpaRisiko++;

        // Dapatkan jenis unit kerja
        $jenisUnit = $kegiatan->unitKerja ? $kegiatan->unitKerja->nama_unit_kerja : 'default';

        // Pilih template risiko yang sesuai
        $templates = $risikoTemplates[$jenisUnit] ?? $risikoTemplates['default'];

        // Generate 2-4 risiko untuk kegiatan ini
        $jumlahRisiko = rand(2, 4);

        // Shuffle untuk variasi
        shuffle($templates);
        $selectedTemplates = array_slice($templates, 0, $jumlahRisiko);

        $risikoAdded = [];

        foreach ($selectedTemplates as $template) {
            $tingkatRisiko = getTingkatRisiko($template['kemungkinan'], $template['dampak']);

            $peta = Peta::create([
                'id_kegiatan' => $kegiatan->id,
                'judul' => $template['judul'],
                'jenis' => $jenisUnit,
                'anggaran' => $kegiatan->anggaran ?? '0', // Ambil dari kegiatan atau default 0
                'kode_regist' => 'AUTO-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'kategori' => $template['kategori'],
                'uraian' => $template['uraian'],
                'skor_kemungkinan' => $template['kemungkinan'],
                'skor_dampak' => $template['dampak'],
                'tingkat_risiko' => $tingkatRisiko,
                'tampil_manajemen_risiko' => 0,
                'status_telaah' => 0,
                'metode' => 'Risk Assessment',
                'pernyataan' => 'Risiko ini telah diidentifikasi dan perlu dilakukan mitigasi',
                'pengendalian' => 'Monitoring berkala dan evaluasi risiko',
                'mitigasi' => 'Penyusunan rencana mitigasi risiko dan tindakan preventif',
                'waktu' => Carbon::create($tahun, rand(1, 12), rand(1, 28))->format('Y-m-d'),
                'created_at' => Carbon::create($tahun, rand(1, 12), rand(1, 28)),
                'updated_at' => Carbon::now(),
            ]);

            $totalAdded++;
            $risikoAdded[] = $template['judul'];
        }

        $kegiatanDiproses[] = [
            'id' => $kegiatan->id_kegiatan,
            'judul' => $kegiatan->judul,
            'unit' => $jenisUnit,
            'jumlah_risiko' => $jumlahRisiko,
            'risiko' => $risikoAdded,
        ];

        echo "✅ [{$kegiatanTanpaRisiko}/{$allKegiatans->count()}] Menambahkan {$jumlahRisiko} risiko untuk:\n";
        echo "   ID: {$kegiatan->id_kegiatan}\n";
        echo "   Kegiatan: " . substr($kegiatan->judul, 0, 60) . "...\n";
        echo "   Unit: {$jenisUnit}\n\n";
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "📈 RINGKASAN HASIL\n";
echo str_repeat("=", 70) . "\n";
echo "Total Kegiatan                : " . $allKegiatans->count() . "\n";
echo "Kegiatan Tanpa Risiko         : {$kegiatanTanpaRisiko}\n";
echo "Total Risiko Ditambahkan      : {$totalAdded}\n";
echo str_repeat("=", 70) . "\n\n";

if (count($kegiatanDiproses) > 0) {
    echo "📋 DETAIL KEGIATAN YANG DIPROSES:\n\n";
    foreach ($kegiatanDiproses as $idx => $data) {
        echo ($idx + 1) . ". {$data['id']} - {$data['judul']}\n";
        echo "   Unit: {$data['unit']}\n";
        echo "   Risiko ditambahkan ({$data['jumlah_risiko']}):\n";
        foreach ($data['risiko'] as $risiko) {
            echo "   - {$risiko}\n";
        }
        echo "\n";
    }
}

echo "✨ Proses selesai!\n";
echo "\n💡 Tips: Anda sekarang bisa membuka halaman Detail Unit Kerja untuk melihat\n";
echo "   semua risiko yang telah ditambahkan, lalu gunakan tombol 'Update Kegiatan'\n";
echo "   untuk menampilkannya di halaman Manajemen Risiko.\n\n";
