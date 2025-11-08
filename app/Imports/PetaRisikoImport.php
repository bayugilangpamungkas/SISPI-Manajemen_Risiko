<?php

namespace App\Imports;

use DateTime;
use App\Models\Peta;
use App\Models\UnitKerja;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PetaRisikoImport implements ToCollection, WithHeadingRow
{
    /**
     * Calculate risk level based on probability and impact scores
     *
     * @param int $probability
     * @param int $impact
     * @return string
     */
    private function calculateRiskLevel($probability, $impact)
    {
        $probability = (int)substr($probability, 0, 1);
        $impact = (int)substr($impact, 0, 1);
        $score = $probability * $impact;

        if ($score >= 21) {
            return "EXTREME";
        } elseif ($score >= 16) {
            return "HIGH";
        } elseif ($score >= 11) {
            return "MIDDLE";
        } elseif ($score >= 6) {
            return "LOW";
        } else {
            return "VERY LOW";
        }
    }

    private function convertExcelDate($serial) {
        if (empty($serial)) {
            return null;
        }
        
        // Excel menggunakan sistem 1900 date system
        // dimana 1 = 1/1/1900
        // Perlu dikurangi 2 karena Excel menganggap 1900 adalah tahun kabisat
        $unix_date = ($serial - 25569) * 86400;
        
        // Konversi ke format MySQL date (Y-m-d)
        return date('Y-m-d', $unix_date);
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $imported = 0;
        $updated = 0;
        $errors = [];
        $messages = [];
        // dd($rows);
        foreach ($rows as $row) {
            // Validasi data yang diperlukan
            if (!isset($row['nmkegiatan']) || !isset($row['probabilitas']) || !isset($row['dampak'])) {
                $errors[] = "Ada data yang tidak lengkap pada salah satu baris.";
                continue;
            }

            // Validasi unit kerja
            $unitKerja = UnitKerja::where('nama_unit_kerja', $row['nmunit'])->first();
            if (!$unitKerja) {
                $errors[] = "Unit kerja '{$row['nmunit']}' tidak ditemukan.";
                continue;
            }

            // Konversi probabilitas
            $skor_kemungkinan = match ($row['probabilitas']) {
                'Sangat Jarang' => 1,
                'Jarang' => 2,
                'Kadang-kadang' => 3,
                'Sering' => 4,
                'Sangat Sering' => 5,
                default => null
            };

            // Konversi dampak
            $skor_dampak = match ($row['dampak']) {
                'Sangat Sedikit Berpengaruh' => 1,
                'Sedikit Berpengaruh' => 2,
                'Cukup Berpengaruh' => 3,
                'Berpengaruh' => 4,
                'Sangat Berpengaruh' => 5,
                default => null
            };

            if ($skor_kemungkinan === null || $skor_dampak === null) {
                $errors[] = "Nilai probabilitas atau dampak tidak valid pada kegiatan '{$row['nmkegiatan']}'";
                continue;
            }

            $existing = Peta::where('judul', $row['nmkegiatan'])
                ->where('jenis', $unitKerja->nama_unit_kerja)
                ->first();

            $levelRisiko = $this->calculateRiskLevel($skor_kemungkinan, $skor_dampak);

            $data = [
                'kode_regist' => $row['idusulan'] ?? '',
                'judul' => $row['nmkegiatan'],
                'anggaran' => $row['nilrabusulan'] ?? 0,
                'jenis' => $unitKerja->nama_unit_kerja,
                'pernyataan' => $row['pernyataanrisiko'] ?? '',
                'uraian' => $row['uraiandampak'] ?? '',
                'metode' => $row['pengendalian'] ?? '',
                'kategori' => $row['resiko'] ?? '',
                'skor_dampak' => $skor_dampak,
                'skor_kemungkinan' => $skor_kemungkinan,
                'tingkat_risiko' => $levelRisiko,
                'waktu_telaah_subtansi' => $this->convertExcelDate($row['waktutelaahsubstansi'] ?? ''),
                'waktu_telaah_teknis' => $this->convertExcelDate($row['waktutelaahteknis'] ?? ''),
                'waktu_telaah_spi' => $this->convertExcelDate($row['waktutelaahspi'] ?? ''),
            ];

            if ($existing) {
                $existing->update($data);
                $updated++;
                $messages[] = "Data dengan judul '{$row['nmkegiatan']}' berhasil diupdate.";
            } else {
                Peta::create($data);
                $imported++;
            }
        }

        session()->flash('import_details', [
            'success' => "Berhasil import $imported data baru dan update $updated data.",
            'messages' => $messages,
            'errors' => $errors
        ]);
    }
}
