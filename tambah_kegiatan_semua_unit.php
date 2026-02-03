<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Peta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "=== SCRIPT MENAMBAH DATA KEGIATAN KE SEMUA UNIT ===\n\n";

// Target jumlah kegiatan yang ingin dicapai
$TARGET_KEGIATAN = 50;
$TAHUN_TARGET = 2026;

// Ambil semua unit kerja di tahun 2026
$units = DB::table('petas')
    ->select('jenis', DB::raw('COUNT(*) as total'))
    ->whereYear('created_at', $TAHUN_TARGET)
    ->groupBy('jenis')
    ->get();

echo "📊 Ditemukan " . count($units) . " unit kerja di tahun {$TAHUN_TARGET}\n";
echo "🎯 Target: {$TARGET_KEGIATAN} kegiatan per unit\n\n";

$totalAdded = 0;
$processedUnits = 0;

foreach ($units as $unit) {
    $unitName = $unit->jenis;
    $currentTotal = $unit->total;

    // Hitung jumlah yang perlu ditambahkan
    $needToAdd = $TARGET_KEGIATAN - $currentTotal;

    if ($needToAdd <= 0) {
        echo "✓ {$unitName}: Sudah mencapai target ({$currentTotal} kegiatan)\n";
        continue;
    }

    echo "📌 {$unitName}: {$currentTotal} → {$TARGET_KEGIATAN} (tambah {$needToAdd})\n";

    // Tentukan kategori berdasarkan nama unit
    $kategori = 'Operational'; // Default
    if (strpos($unitName, 'Keuangan') !== false || strpos($unitName, 'Akuntansi') !== false) {
        $kategori = 'Financial';
    } elseif (strpos($unitName, 'DEWAS') !== false || strpos($unitName, 'SPI') !== false || strpos($unitName, 'SENAT') !== false) {
        $kategori = 'Compliance';
    } elseif (strpos($unitName, 'WADIR') !== false || strpos($unitName, 'Direktur') !== false) {
        $kategori = 'Strategic';
    }

    // Generate kode registrasi terakhir
    $lastKode = Peta::where('jenis', $unitName)
        ->whereYear('created_at', $TAHUN_TARGET)
        ->orderBy('kode_regist', 'desc')
        ->value('kode_regist');

    // Extract nomor urut dari kode
    $lastNumber = 0;
    if ($lastKode) {
        preg_match('/\/(\d+)\//', $lastKode, $matches);
        if (isset($matches[1])) {
            $lastNumber = (int)$matches[1];
        }
    }

    // Tambahkan data
    $added = 0;
    for ($i = 1; $i <= $needToAdd; $i++) {
        $nomorUrut = $lastNumber + $i;
        $kodeRegist = "MR/{$nomorUrut}/{$TAHUN_TARGET}";

        try {
            Peta::create([
                'judul' => "Kegiatan Tambahan {$i} - {$unitName}",
                'jenis' => $unitName,
                'anggaran' => '0',
                'kode_regist' => $kodeRegist,
                'kategori' => $kategori,
                'metode' => 'Metode standar',
                'tampil_manajemen_risiko' => 0,
                'uraian' => 'Kegiatan ditambahkan melalui script bulk insert otomatis',
                'pernyataan' => 'Belum ada pernyataan risiko',
                'skor_kemungkinan' => 1,
                'skor_dampak' => 1,
                'created_at' => Carbon::create($TAHUN_TARGET, 1, 1),
                'updated_at' => now(),
            ]);
            $added++;
        } catch (\Exception $e) {
            echo "   ❌ Error pada kegiatan {$i}: " . $e->getMessage() . "\n";
        }
    }

    // Verifikasi hasil
    $newCount = Peta::where('jenis', $unitName)
        ->whereYear('created_at', $TAHUN_TARGET)
        ->count();

    $newCountWithRisk = Peta::where('jenis', $unitName)
        ->whereYear('created_at', $TAHUN_TARGET)
        ->where('tampil_manajemen_risiko', 1)
        ->count();

    echo "   ✅ Berhasil menambahkan {$added} kegiatan\n";
    echo "   📊 Rasio sekarang: {$newCountWithRisk}/{$newCount}\n\n";

    $totalAdded += $added;
    $processedUnits++;
}

echo "\n=== SELESAI ===\n";
echo "✓ Total unit yang diproses: {$processedUnits}\n";
echo "✓ Total kegiatan ditambahkan: {$totalAdded}\n";
echo "\n💡 Silakan refresh halaman manajemen risiko untuk melihat perubahan.\n";
echo "💡 Semua rasio sekarang akan menampilkan: 0/{$TARGET_KEGIATAN}\n";
