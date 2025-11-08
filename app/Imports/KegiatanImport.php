<?php

namespace App\Imports;

use App\Models\Kegiatan;
use App\Models\UnitKerja;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class KegiatanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $imported = 0;
        $updated = 0;
        $messages = [];

        // dd($rows);

        foreach ($rows as $row) {
            $unitKerja = UnitKerja::where('nama_unit_kerja', $row['unit_kerja'])->first();
            // Cari data yang sudah ada berdasarkan judul
            $existing = Kegiatan::where('judul', $row['judul'])->where('id_unit_kerja', $unitKerja->id)->first();

            if ($existing) {
                // Update data yang sudah ada
                $existing->update([
                    'id_unit_kerja' => $unitKerja->id,
                    'iku' => $row['iku'],
                    'sasaran' => $row['sasaran'],
                    'proker' => $row['proker'],
                    'indikator' => $row['indikator'],
                    'anggaran' => $row['anggaran'],
                ]);
                $updated++;
                $messages[] = "Data dengan judul '{$row['judul']}' berhasil diupdate.";
            } else {
                // Create data baru jika belum ada
                Kegiatan::create([
                    'id_unit_kerja' => $unitKerja->id,
                    'judul' => $row['judul'],
                    'iku' => $row['iku'],
                    'sasaran' => $row['sasaran'],
                    'proker' => $row['proker'],
                    'indikator' => $row['indikator'],
                    'anggaran' => $row['anggaran'],
                ]);
                $imported++;
            }
        }

        // Set flash message dengan detail import
        session()->flash('import_details', [
            'success' => "Berhasil import $imported data baru dan update $updated data.",
            'messages' => $messages
        ]);
    }
}
