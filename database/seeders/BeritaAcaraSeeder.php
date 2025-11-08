<?php

namespace Database\Seeders;

use App\Models\BeritaAcara;
use App\Models\BeritaAcaraDocument;
use App\Models\BeritaAcaraImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedAssets();

        $minutes = [
            [
                'title' => 'Rapat Koordinasi Audit Internal Triwulan IV',
                'meeting_date' => now()->subDays(10)->toDateString(),
                'location' => 'Ruang Rapat Utama',
                'summary' => 'Pembahasan progres audit internal triwulan IV, evaluasi tindak lanjut rekomendasi, serta penetapan jadwal audit mendatang.',
                'documents' => [
                    [
                        'file_name' => 'Notulen_Rapat_Koordinasi_IV.pdf',
                        'file_path' => 'berita_acara/documents/notulen-koordinasi-iv.pdf',
                        'mime_type' => 'application/pdf',
                        'file_size' => Storage::disk('public')->size('berita_acara/documents/notulen-koordinasi-iv.pdf'),
                    ],
                ],
                'images' => [
                    [
                        'file_name' => 'rapat-koordinasi-1.png',
                        'file_path' => 'berita_acara/images/rapat-koordinasi-1.png',
                        'caption' => 'Tim auditor membahas agenda audit.',
                        'display_order' => 1,
                    ],
                    [
                        'file_name' => 'rapat-koordinasi-2.png',
                        'file_path' => 'berita_acara/images/rapat-koordinasi-2.png',
                        'caption' => 'Review tindak lanjut rekomendasi.',
                        'display_order' => 2,
                    ],
                ],
            ],
            [
                'title' => 'Pengesahan Hasil Audit Operasional Unit Produksi',
                'meeting_date' => now()->subDays(24)->toDateString(),
                'location' => 'Auditorium SISPI',
                'summary' => 'Konfirmasi hasil audit operasional unit produksi tahun berjalan dan penyampaian rekomendasi prioritas tinggi.',
                'documents' => [
                    [
                        'file_name' => 'Pengesahan_Hasil_Audit_Produksi.pdf',
                        'file_path' => 'berita_acara/documents/pengesahan-hasil-audit-produksi.pdf',
                        'mime_type' => 'application/pdf',
                        'file_size' => Storage::disk('public')->size('berita_acara/documents/pengesahan-hasil-audit-produksi.pdf'),
                    ],
                ],
                'images' => [
                    [
                        'file_name' => 'hasil-audit-produksi.png',
                        'file_path' => 'berita_acara/images/hasil-audit-produksi.png',
                        'caption' => 'Pemaparan hasil audit oleh ketua tim.',
                        'display_order' => 1,
                    ],
                ],
            ],
            [
                'title' => 'Monitoring Tindak Lanjut Rekomendasi Semester II',
                'meeting_date' => now()->subDays(37)->toDateString(),
                'location' => 'Ruang Monitoring',
                'summary' => 'Evaluasi status implementasi rekomendasi audit semester II dan perumusan dukungan lintas unit.',
                'documents' => [
                    [
                        'file_name' => 'Monitoring_Tindak_Lanjut_Semester_II.pdf',
                        'file_path' => 'berita_acara/documents/monitoring-tindak-lanjut-semester-ii.pdf',
                        'mime_type' => 'application/pdf',
                        'file_size' => Storage::disk('public')->size('berita_acara/documents/monitoring-tindak-lanjut-semester-ii.pdf'),
                    ],
                ],
                'images' => [],
            ],
        ];

        foreach ($minutes as $minute) {
            $beritaAcara = BeritaAcara::query()->updateOrCreate(
                ['title' => $minute['title'], 'meeting_date' => $minute['meeting_date']],
                [
                    'location' => $minute['location'],
                    'summary' => $minute['summary'],
                ]
            );

            $beritaAcara->documents()->delete();
            $beritaAcara->images()->delete();

            foreach ($minute['documents'] as $document) {
                BeritaAcaraDocument::create(array_merge($document, [
                    'berita_acara_id' => $beritaAcara->id,
                ]));
            }

            foreach ($minute['images'] as $image) {
                BeritaAcaraImage::create(array_merge($image, [
                    'berita_acara_id' => $beritaAcara->id,
                ]));
            }
        }
    }

    private function seedAssets(): void
    {
        $pdfSamples = [
            'berita_acara/documents/notulen-koordinasi-iv.pdf' => $this->minimalPdf('Notulen Rapat Koordinasi Triwulan IV'),
            'berita_acara/documents/pengesahan-hasil-audit-produksi.pdf' => $this->minimalPdf('Pengesahan Hasil Audit Operasional Unit Produksi'),
            'berita_acara/documents/monitoring-tindak-lanjut-semester-ii.pdf' => $this->minimalPdf('Monitoring Tindak Lanjut Rekomendasi Semester II'),
        ];

        foreach ($pdfSamples as $path => $content) {
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $content);
            }
        }

        $imageSamples = [
            'berita_acara/images/rapat-koordinasi-1.png' => base64_decode($this->placeholderPng()),
            'berita_acara/images/rapat-koordinasi-2.png' => base64_decode($this->placeholderPng('#1E40AF')),
            'berita_acara/images/hasil-audit-produksi.png' => base64_decode($this->placeholderPng('#0F172A')),
        ];

        foreach ($imageSamples as $path => $binary) {
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $binary);
            }
        }
    }

    private function minimalPdf(string $title): string
    {
        $cleanTitle = Str::of($title)->replace(['(', ')'], '')->toString();

        return "%PDF-1.4\n" .
            "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n" .
            "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n" .
            "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n" .
            "4 0 obj<</Length 71>>stream\n" .
            "BT /F1 24 Tf 72 780 Td ($cleanTitle) Tj ET\n" .
            "BT /F1 12 Tf 72 740 Td (Dokumen contoh untuk pengujian tampilan Berita Acara.) Tj ET\n" .
            "BT /F1 12 Tf 72 720 Td (Silakan ganti file ketika data riil tersedia.) Tj ET\n" .
            "endstream endobj\n" .
            "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n" .
            "xref\n0 6\n0000000000 65535 f\n0000000010 00000 n\n0000000061 00000 n\n0000000116 00000 n\n0000000274 00000 n\n0000000471 00000 n\ntrailer<</Size 6/Root 1 0 R>>\nstartxref\n574\n%%EOF\n";
    }

    private function placeholderPng(string $hex = '#1E3A8A'): string
    {
        $hex = ltrim($hex, '#');
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        $pngHeader = 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAFElEQVR4nO3BMQEAAADCoPVPbQ0PoAAAAAAAAH4NggAAAZWeD0YAAAAASUVORK5CYII=';
        $binary = base64_decode($pngHeader);

        if ($binary === false) {
            return $pngHeader;
        }

        // simple recolor by manipulating palette (works for this 1x1 png)
        if (strlen($binary) >= 45) {
            $binary[41] = chr($r);
            $binary[42] = chr($g);
            $binary[43] = chr($b);
        }

        return base64_encode($binary);
    }
}
