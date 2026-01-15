<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK DATA PETA RISIKO ===" . PHP_EOL . PHP_EOL;

$petas = App\Models\Peta::with('kegiatan')->whereYear('created_at', 2026)->take(5)->get();

echo "Total data ditemukan: " . $petas->count() . PHP_EOL . PHP_EOL;

foreach($petas as $p) {
    echo "Peta ID: " . $p->id . PHP_EOL;
    echo "Unit Kerja: " . $p->jenis . PHP_EOL;
    echo "id_kegiatan: " . ($p->id_kegiatan ?? 'NULL') . PHP_EOL;
    echo "Kegiatan Object: " . ($p->kegiatan ? " EXISTS" : " NULL") . PHP_EOL;
    if ($p->kegiatan) {
        echo "Judul Kegiatan: " . $p->kegiatan->judul . PHP_EOL;
    }
    echo "---" . PHP_EOL . PHP_EOL;
}
