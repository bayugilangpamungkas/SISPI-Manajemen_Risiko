<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peta;
use App\Models\CommentPr;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AuditeeController extends Controller
{
    /**
     * ========================================
     * AUDITEE METHODS (Unit Kerja)
     * ========================================
     */

    /**
     * Dashboard Auditee - Menampilkan risiko berdasarkan unit kerja mereka
     */
    public function auditeeIndex(Request $request)
    {
        $active = 21;
        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        // Get filter parameters
        $tahun = $request->input('tahun', date('Y'));
        $statusReview = $request->input('status_review', 'all');
        $kegiatanId = $request->input('kegiatan_id', 'all');

        // Get available years
        $years = Peta::selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($years->isEmpty()) {
            $currentYear = date('Y');
            $years = collect(range($currentYear - 4, $currentYear))->reverse()->values();
        }

        // Get user's unit kerja
        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;
        $unitKerjaId = $user->id_unit_kerja;

        // Get available kegiatan for this unit using id_unit_kerja
        $kegiatans = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaId)
            ->whereYear('created_at', $tahun)
            ->orderBy('judul')
            ->get();

        // Build query - hanya tampilkan risiko dari unit kerja user
        $query = Peta::with(['comment_prs.user', 'auditor', 'kegiatan'])
            ->where('jenis', $unitKerjaUser)
            ->whereYear('created_at', $tahun);

        // Filter by kegiatan
        if ($kegiatanId != 'all') {
            $query->where('kegiatan_id', $kegiatanId);
        }

        // Filter by status review
        if ($statusReview != 'all') {
            if ($statusReview == 'approved') {
                $query->where('status_telaah', 1);
            } elseif ($statusReview == 'rejected') {
                $query->where('koreksiPr', 'rejected');
            } else {
                $query->where(function ($q) {
                    $q->where('status_telaah', 0)->orWhereNull('status_telaah');
                })->where(function ($q) {
                    $q->where('koreksiPr', '!=', 'rejected')->orWhereNull('koreksiPr');
                });
            }
        }

        // Get paginated data, sorted by risk score (highest first)
        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->paginate(15);

        // Calculate statistics untuk unit kerja ini
        $statistics = [
            'total' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)->count(),
            'high_risk' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)
                ->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Low')->count(),
            'approved' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)
                ->where('status_telaah', 1)->count(),
            'pending' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)
                ->where(function ($q) {
                    $q->where('status_telaah', 0)->orWhereNull('status_telaah');
                })->count(),
            'rejected' => Peta::where('jenis', $unitKerjaUser)
                ->whereYear('created_at', $tahun)
                ->where('koreksiPr', 'rejected')->count(),
        ];

        $unitKerjas = collect(); // Empty for auditee
        $auditors = collect(); // Empty for auditee
        $auditorFilter = 'all';
        $cluster = 'all';
        $unitKerja = $unitKerjaUser;

        return view('manajemen_risiko.index', compact(
            'active',
            'petas',
            'statistics',
            'cluster',
            'tahun',
            'unitKerja',
            'statusReview',
            'years',
            'unitKerjas',
            'auditors',
            'auditorFilter',
            'kegiatans',
            'kegiatanId'
        ));
    }



    /**
     * Form untuk mengedit/mengisi data monitoring risiko
     */
    public function auditeeEdit($id)
    {
        $active = 21;
        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        $peta = Peta::where('jenis', $unitKerjaUser)->findOrFail($id);

        return view('manajemen_risiko.edit', compact('active', 'peta'));
    }

    /**
     * Update data monitoring risiko oleh Auditee
     */
    public function auditeeUpdate(Request $request, $id)
    {
        $request->validate([
            'pernyataan' => 'required|string',
            'uraian' => 'required|string',
            'metode' => 'required|string',
            'skor_kemungkinan' => 'required|integer|min:1|max:5',
            'skor_dampak' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        $peta = Peta::where('jenis', $unitKerjaUser)->findOrFail($id);

        $skorTotal = $request->skor_kemungkinan * $request->skor_dampak;

        // Tentukan tingkat risiko
        if ($skorTotal >= 20) {
            $tingkatRisiko = 'Extreme';
        } elseif ($skorTotal >= 15) {
            $tingkatRisiko = 'High';
        } elseif ($skorTotal >= 10) {
            $tingkatRisiko = 'Moderate';
        } else {
            $tingkatRisiko = 'Low';
        }

        $peta->update([
            'pernyataan' => $request->pernyataan,
            'uraian' => $request->uraian,
            'metode' => $request->metode,
            'skor_kemungkinan' => $request->skor_kemungkinan,
            'skor_dampak' => $request->skor_dampak,
            'tingkat_risiko' => $tingkatRisiko,
            'koreksiPr' => null, // Reset koreksi status
            'koreksiPr_at' => null,
        ]);

        return redirect()->route('manajemen-risiko.auditee.index')
            ->with('success', 'Data monitoring risiko berhasil diperbarui!');
    }

    /**
     * Submit data risiko ke Auditor
     */
    public function auditeeSubmit($id)
    {
        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        $peta = Peta::where('jenis', $unitKerjaUser)->findOrFail($id);

        // Cek apakah data sudah lengkap
        if (empty($peta->pernyataan) || empty($peta->uraian) || empty($peta->metode)) {
            return redirect()->back()->with('error', 'Lengkapi data monitoring risiko terlebih dahulu!');
        }

        // Cek apakah sudah ada auditor yang ditugaskan
        if (empty($peta->auditor_id)) {
            return redirect()->back()->with('error', 'Belum ada auditor yang ditugaskan untuk risiko ini!');
        }

        $peta->update([
            'koreksiPr' => 'submitted',
            'koreksiPr_at' => now(),
        ]);

        // Tambahkan komentar
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'comment' => 'Data monitoring risiko telah disubmit ke Auditor untuk direview.',
            'jenis' => 'analisis',
        ]);

        return redirect()->route('manajemen-risiko.auditee.index')
            ->with('success', 'Data berhasil disubmit ke Auditor untuk direview!');
    }

    /**
     * Export Excel untuk Auditee
     */
    public function auditeeExport(Request $request)
    {
        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;
        $tahun = $request->input('tahun', date('Y'));

        // Build query - hanya risiko dari unit kerja user
        $petas = Peta::with(['comment_prs'])
            ->where('jenis', $unitKerjaUser)
            ->whereYear('created_at', $tahun)
            ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("SISPI - " . $user->name)
            ->setTitle("Data Risiko - {$tahun}")
            ->setSubject("Laporan Data Risiko Unit Kerja");

        // Header
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN DATA RISIKO ' . strtoupper($unitKerjaUser) . ' TAHUN ' . $tahun);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Column headers
        $headers = ['No', 'Kategori', 'Judul', 'Kode Registrasi', 'Kemungkinan', 'Dampak', 'Skor Total', 'Tingkat Risiko', 'Status Review', 'Auditor', 'Jumlah Komentar'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '3', $header);
            $column++;
        }

        // Style header
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data rows
        $row = 4;
        $no = 1;
        foreach ($petas as $peta) {
            $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $peta->kategori);
            $sheet->setCellValue('C' . $row, $peta->judul);
            $sheet->setCellValue('D' . $row, $peta->kode_regist);
            $sheet->setCellValue('E' . $row, $peta->skor_kemungkinan);
            $sheet->setCellValue('F' . $row, $peta->skor_dampak);
            $sheet->setCellValue('G' . $row, $skorTotal);
            $sheet->setCellValue('H' . $row, $peta->tingkat_risiko);

            $status = 'Pending';
            if ($peta->status_telaah) {
                $status = 'Disetujui';
            } elseif ($peta->koreksiPr == 'rejected') {
                $status = 'Ditolak';
            } elseif ($peta->koreksiPr == 'submitted') {
                $status = 'Menunggu Review';
            }

            $sheet->setCellValue('I' . $row, $status);
            $sheet->setCellValue('J' . $row, $peta->auditor->name ?? '-');
            $sheet->setCellValue('K' . $row, $peta->comment_prs->count());

            // Apply borders
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'Data_Risiko_' . str_replace(' ', '_', $unitKerjaUser) . '_' . $tahun . '_' . date('Ymd_His') . '.xlsx';

        // Save file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Upload Excel dan Clustering untuk Auditee
     */
    public function auditeeUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;
        $unitKerjaId = $user->id_unit_kerja;

        try {
            $file = $request->file('file');

            // Import data menggunakan existing import class
            $import = new \App\Imports\PetaRisikoImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            // Update jenis (unit kerja) untuk semua data yang baru diimport
            // Ambil data yang baru diimport (berdasarkan timestamp terakhir)
            $recentPetas = Peta::where('jenis', '!=', $unitKerjaUser)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->get();

            foreach ($recentPetas as $peta) {
                $peta->update(['jenis' => $unitKerjaUser]);
            }

            // Hitung jumlah data yang berhasil diimport
            $importedCount = Peta::where('jenis', $unitKerjaUser)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();

            return redirect()->route('manajemen-risiko.auditee.index')
                ->with('success', "Berhasil mengupload dan clustering {$importedCount} data risiko! Data telah dikelompokkan berdasarkan tingkat risiko.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupload file: ' . $e->getMessage());
        }
    }

    /**
     * Download Template Excel untuk Auditee
     */
    public function auditeeDownloadTemplate()
    {
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("SISPI")
            ->setTitle("Template Peta Risiko")
            ->setSubject("Template Upload Data Peta Risiko");

        // Header
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'TEMPLATE DATA PETA RISIKO');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Panduan
        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', 'Panduan: Isi data sesuai kolom yang tersedia. Jangan mengubah nama kolom header!');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => 'FF0000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Column headers dengan nama yang sesuai dengan import
        $headers = [
            'nmkegiatan' => 'Nama Kegiatan',
            'probabilitas' => 'Probabilitas (1-5)',
            'dampak' => 'Dampak (1-5)',
            'kategori' => 'Kategori Risiko',
            'judul' => 'Judul Risiko',
            'pernyataan' => 'Pernyataan Risiko',
            'penyebab' => 'Penyebab',
            'akibat' => 'Akibat',
            'uraian' => 'Uraian',
            'tanggal_register' => 'Tanggal Register (dd/mm/yyyy)',
            'tanggal_revisi' => 'Tanggal Revisi (dd/mm/yyyy)',
            'metode' => 'Metode Analisis',
            'kode_regist' => 'Kode Registrasi'
        ];

        $col = 'A';
        foreach ($headers as $key => $header) {
            $sheet->setCellValue($col . '3', $key);
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        // Style header
        $sheet->getStyle('A3:M4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Contoh data (row 5)
        $exampleData = [
            'Audit Keuangan 2024',
            '4',
            '5',
            'Keuangan',
            'Risiko keterlambatan laporan keuangan',
            'Terdapat potensi keterlambatan dalam penyusunan laporan keuangan',
            'SDM terbatas, sistem manual',
            'Keterlambatan pengambilan keputusan, denda',
            'Laporan keuangan bulan Oktober belum selesai',
            '01/10/2024',
            '15/11/2024',
            'Analisis Kualitatif',
            'PR-2024-001'
        ];

        $col = 'A';
        foreach ($exampleData as $data) {
            $sheet->setCellValue($col . '5', $data);
            $col++;
        }

        // Style example row
        $sheet->getStyle('A5:M5')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '0070C0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Add note
        $sheet->mergeCells('A6:M6');
        $sheet->setCellValue('A6', 'Catatan: Baris 5 adalah contoh data. Hapus baris ini dan mulai input data Anda dari baris 6 ke bawah.');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => 'FF0000']],
        ]);

        // Auto-size columns
        foreach (range('A', 'M') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'Template_Peta_Risiko_' . date('Ymd') . '.xlsx';

        // Save file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }


    /**
     * Detail risiko untuk Auditee
     */
    public function auditeeShow($id)
    {
        $active = 21;
        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('manajemen-risiko.auditee.index')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun! Silakan hubungi Admin untuk mendaftarkan unit kerja Anda.'
            );
        }

        // Get user's unit kerja
        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        // Cari risiko berdasarkan ID
        $peta = Peta::with(['comment_prs.user', 'kegiatan', 'auditor'])->find($id);

        // Jika tidak ditemukan
        if (!$peta) {
            return redirect()->route('manajemen-risiko.auditee.index')
                ->with('error', 'Data risiko dengan ID ' . $id . ' tidak ditemukan!');
        }

        // Validasi akses - pastikan risiko milik unit kerja user
        if ($peta->jenis !== $unitKerjaUser) {
            return redirect()->route('manajemen-risiko.auditee.index')
                ->with('error', 'Anda tidak memiliki akses ke risiko ini. Risiko ini milik unit kerja: ' . $peta->jenis);
        }

        return view('manajemen_risiko.show', compact('active', 'peta'));
    }


    /**
     * Menampilkan Detail Risiko (Method baru)
     */
    public function auditeeShowDetail($id)
    {
        $active = 21;
        $user = Auth::user();

        // 1. CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun!'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        // 2. Cari risiko berdasarkan ID beserta relasinya
        $peta = Peta::with(['comment_prs.user', 'kegiatan', 'auditor'])->find($id);

        // 3. Jika data tidak ditemukan
        if (!$peta) {
            return redirect()->route('manajemen-risiko.auditee.index')
                ->with('error', 'Data risiko tidak ditemukan!');
        }

        // 4. Validasi akses: Pastikan risiko milik unit kerja user
        if ($peta->jenis !== $unitKerjaUser) {
            return redirect()->route('manajemen-risiko.auditee.index')
                ->with('error', 'Anda tidak memiliki akses ke detail risiko ini.');
        }

        // --- TAMBAHKAN LOGIKA PERHITUNGAN UNTUK VIEW DI SINI ---

        // 5. Hitung Skor Risiko (Asumsi kolom di DB adalah skor_kemungkinan dan skor_dampak)
        $skorTotal = ($peta->skor_kemungkinan ?? 0) * ($peta->skor_dampak ?? 0);

        // 6. Tentukan Badge Skor
        if ($skorTotal >= 15) {
            $badgeClass = 'badge-danger';
            $badgeText = 'Sangat Tinggi';
        } elseif ($skorTotal >= 10) {
            $badgeClass = 'badge-warning';
            $badgeText = 'Tinggi / Moderat';
        } else {
            $badgeClass = 'badge-success';
            $badgeText = 'Rendah';
        }

        // 7. Ambil Data Respon Auditee (Decode JSON dari kolom koreksi_pr atau sejenisnya)
        // Sesuaikan 'koreksi_pr' dengan nama kolom tempat Anda menyimpan jawaban auditee
        $auditeeResponse = json_decode($peta->koreksi_pr, true) ?? [];

        // 8. Tentukan apakah Form Read-Only
        // Form dikunci jika auditee sudah pernah mengirim (ada timestamp koreksiPr_at) 
        // atau status sudah 'Selesai' / 'Disetujui'
        $isApproved = ($peta->status === 'disetujui');
        $isReadOnly = !empty($peta->koreksiPr_at) || $isApproved;

        // 9. Tampilkan View dengan compact semua variabel yang dibutuhkan
        return view('manajemen_risiko.auditee_detail', compact(
            'active',
            'peta',
            'user',
            'skorTotal',
            'badgeClass',
            'badgeText',
            'auditeeResponse',
            'isReadOnly',
            'isApproved'
        ));
    }
}
