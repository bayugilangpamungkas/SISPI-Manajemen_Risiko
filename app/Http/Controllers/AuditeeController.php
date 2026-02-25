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
        // ✅ FILTER UTAMA: Hanya tampilkan data yang sudah di-clustering (tampil_manajemen_risiko = 1)
        $query = Peta::with(['comment_prs.user', 'auditor', 'kegiatan'])
            ->where('jenis', $unitKerjaUser)
            ->where('tampil_manajemen_risiko', 1)  // ✅ FILTER INI WAJIB ADA!
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
     * Detail Audit Wawancara untuk Auditee
     * WORKFLOW:
     * - Jika Auditor sudah submit hasil audit (Completed/Not Completed): Auditee bisa konfirmasi
     * - Jika status = final: Read-only
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

        // ✅ CEK STATUS AUDIT
        $statusAudit = $peta->status_audit;

        // Decode pertanyaan dari auditor dan jawaban auditee
        $questions = $peta->questions; // Menggunakan accessor dari Model
        $responses = $peta->responses; // Menggunakan accessor dari Model

        // Get hasil audit if exists (untuk melihat penilaian auditor)
        $hasilAudit = \App\Models\HasilAudit::where('peta_id', $peta->id)
            ->where('tahun_anggaran', date('Y'))
            ->first();

        // Decode penilaian auditor (jika sudah review)
        $penilaianAuditor = [];
        if ($hasilAudit && $hasilAudit->penilaian_data) {
            $penilaianAuditor = json_decode($hasilAudit->penilaian_data, true) ?? [];
        }

        // ✅ PERBAIKAN: Tentukan mode view berdasarkan NEW WORKFLOW (sesuai revisi dosen)
        $viewMode = 'read_only'; // Default

        // ✅ Mode view untuk Auditee HANYA bergantung pada status audit & Auditor
        // Auditee TIDAK PERNAH input hasil audit (pengendalian, mitigasi, komentar)
        // Auditee HANYA bisa konfirmasi atau tindak lanjut

        if ($peta->isAuditFinal()) {
            $viewMode = 'final'; // Audit sudah final, semua read-only
        }
        // Jika tidak final, view mode akan ditentukan di blade berdasarkan status_konfirmasi_auditor

        // Hitung Skor Risiko
        $skorTotal = ($peta->skor_kemungkinan ?? 0) * ($peta->skor_dampak ?? 0);

        // ✅ GUNAKAN VIEW YANG SUDAH ADA: manajemen_risiko.show
        return view('manajemen_risiko.show', compact(
            'active',
            'peta',
            'user',
            'skorTotal',
            'questions',
            'responses',
            'statusAudit',
            'viewMode',
            'hasilAudit',
            'penilaianAuditor'
        ));
    }

    /**
     * Submit Response Audit oleh Auditee
     * WORKFLOW:
     * - Menjawab pertanyaan dari auditor
     * - Konfirmasi hasil review dari auditor
     */
    public function auditeeSubmitResponse(Request $request, $id)
    {
        $user = Auth::user();

        // CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun!'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        // Cari risiko berdasarkan ID
        $peta = Peta::where('jenis', $unitKerjaUser)->findOrFail($id);

        // ✅ CEK: Tentukan mode action
        $action = $request->input('action');

        // ========================================
        // ✅ NEW WORKFLOW (SESUAI REQUIREMENT DOSEN)
        // ========================================

        if ($action === 'final_approval') {
            // ✅ MODE BARU 1: AUDITEE APPROVE HASIL AUDIT (KETIKA AUDITOR STATUS = COMPLETED)

            // Validasi: Auditor harus sudah set status = Completed
            if ($peta->status_konfirmasi_auditor !== 'Completed') {
                return redirect()->back()->with('error', 'Auditor belum menyelesaikan audit! Anda tidak dapat melakukan approval.');
            }

            $request->validate([
                'catatan_auditee' => 'nullable|string',
            ]);

            // Update status konfirmasi auditee = Completed
            $peta->update([
                'status_konfirmasi_auditee' => 'Completed',
            ]);

            // Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => 'Auditee telah menyetujui (approve) hasil audit. ' .
                    ($request->catatan_auditee ? 'Catatan: ' . $request->catatan_auditee : 'Tidak ada catatan tambahan.'),
            ]);

            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('success', '✅ Approval berhasil! Hasil audit telah Anda setujui. Menunggu finalisasi dari Auditor.');
        
        } elseif ($action === 'reject_audit') {
            // ✅✅ MODE BARU: AUDITEE TOLAK HASIL AUDIT (KETIKA AUDITOR STATUS = COMPLETED)
            // TOMBOL TOLAK: Auditee meminta Auditor untuk memperbaiki hasil audit per-item

            // Validasi: Auditor harus sudah set status = Completed
            if ($peta->status_konfirmasi_auditor !== 'Completed') {
                return redirect()->back()->with('error', 'Auditor belum menyelesaikan audit! Anda tidak dapat melakukan penolakan.');
            }

            $request->validate([
                'catatan_penolakan' => 'required|string|min:10',
            ], [
                'catatan_penolakan.required' => 'Catatan penolakan wajib diisi!',
                'catatan_penolakan.min' => 'Catatan penolakan minimal 10 karakter untuk menjelaskan alasan penolakan.',
            ]);

            // ✅ SIMPAN DATA PENOLAKAN
            $rejectionData = [
                'catatan_penolakan' => $request->catatan_penolakan,
                'rejected_by' => $user->name,
                'rejected_at' => now()->toDateTimeString(),
                'status' => 'rejected_by_auditee',
            ];

            // ✅ UPDATE STATUS AUDIT:
            // - Reset status konfirmasi Auditor agar bisa edit ulang
            // - Simpan catatan penolakan
            $peta->update([
                'status_konfirmasi_auditor' => null, // ✅ RESET agar Auditor bisa edit ulang
                'status_konfirmasi_auditee' => null, // ✅ RESET status Auditee
                'catatan_revisi' => json_encode($rejectionData), // ✅ Simpan catatan penolakan
            ]);

            // ✅ Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => '❌ Auditee MENOLAK hasil audit. Alasan: ' . substr($request->catatan_penolakan, 0, 150) .
                    (strlen($request->catatan_penolakan) > 150 ? '...' : '') .
                    ' | Menunggu perbaikan dari Auditor.',
            ]);

            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('warning', '⚠️ Hasil audit telah ditolak. Auditor akan menerima notifikasi untuk melakukan perbaikan per-item sesuai catatan Anda.');
        
        } elseif ($action === 'submit_follow_up') {
            // ✅ MODE BARU 2: AUDITEE SUBMIT TINDAK LANJUT (KETIKA AUDITOR STATUS = NOT COMPLETED)

            // Validasi: Auditor harus sudah set status = Not Completed
            if ($peta->status_konfirmasi_auditor !== 'Not Completed') {
                return redirect()->back()->with('error', 'Auditor tidak meminta tindak lanjut! Action tidak valid.');
            }

            $request->validate([
                'catatan_tindak_lanjut' => 'required|string',
                'link_data_dukung' => 'nullable|url',  // ✅ VALIDASI LINK (OPSIONAL)
                'status_konfirmasi_auditee' => 'required|in:Completed,Not Completed',
            ]);

            // ✅ Simpan catatan tindak lanjut DAN link data dukung
            $tindakLanjutData = [
                'catatan_tindak_lanjut' => $request->catatan_tindak_lanjut,
                'link_data_dukung' => $request->link_data_dukung,  // ✅ SIMPAN LINK
                'status_auditee' => $request->status_konfirmasi_auditee,
                'submitted_at' => now()->toDateTimeString(),
                'submitted_by' => $user->name,
            ];

            // Update peta dengan data tindak lanjut
            $peta->update([
                'status_konfirmasi_auditee' => $request->status_konfirmasi_auditee,
                'catatan_revisi' => json_encode($tindakLanjutData), // Simpan di catatan_revisi sebagai JSON
            ]);

            // ✅ Log activity dengan informasi link
            $commentText = 'Auditee telah mengirim tindak lanjut. Status: ' . $request->status_konfirmasi_auditee .
                '. Catatan: ' . substr($request->catatan_tindak_lanjut, 0, 100) .
                (strlen($request->catatan_tindak_lanjut) > 100 ? '...' : '');

            if ($request->link_data_dukung) {
                $commentText .= ' | Link data dukung: ' . $request->link_data_dukung;
            }

            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => $commentText,
            ]);

            $successMessage = $request->status_konfirmasi_auditee === 'Completed'
                ? '✅ Tindak lanjut berhasil dikirim! Status Anda: Completed. Menunggu review dari Auditor.'
                : '⏳ Tindak lanjut berhasil dikirim! Status Anda: Not Completed (masih dalam proses).';

            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('success', $successMessage);

            // ========================================
            // OLD WORKFLOW (TETAP DIPERTAHANKAN)
            // ========================================

        } elseif ($action === 'answer_questions') {
            // Validasi: Hanya bisa jawab jika status memungkinkan
            if (!$peta->auditeeCanAnswer()) {
                return redirect()->back()->with('error', 'Anda tidak dapat menjawab pertanyaan pada status ini!');
            }

            $request->validate([
                'answers' => 'required|array|min:1',
                'answers.*.answer' => 'required|string',
                'answers.*.links' => 'nullable|array',
                'answers.*.links.*' => 'nullable|url',
                'answers.*.notes' => 'nullable|string',
            ]);

            // Format jawaban ke JSON
            $answersData = [];
            foreach ($request->answers as $index => $item) {
                $answersData[] = [
                    'no' => $index + 1,
                    'answer' => $item['answer'],
                    'links' => $item['links'] ?? [],
                    'notes' => $item['notes'] ?? '',
                    'answered_at' => now()->toDateTimeString(),
                ];
            }

            // Simpan ke auditee_response
            $peta->update([
                'auditee_response' => json_encode($answersData),
            ]);

            // Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => 'Auditee telah menjawab ' . count($answersData) . ' pertanyaan audit wawancara.',
            ]);

            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('success', 'Jawaban berhasil disimpan! Menunggu review dari Auditor.');
        } elseif ($action === 'confirm_review') {
            // Validasi: Hanya bisa konfirmasi jika auditor sudah review
            if (!$peta->auditeeCanConfirm()) {
                return redirect()->back()->with('error', 'Belum ada hasil review dari Auditor untuk dikonfirmasi!');
            }

            $request->validate([
                'catatan_auditee' => 'nullable|string',
            ]);

            // Update status konfirmasi auditee
            $peta->update([
                'status_konfirmasi_auditee' => 'confirmed',
            ]);

            // Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => 'Auditee telah mengkonfirmasi hasil review audit. ' . ($request->catatan_auditee ? 'Catatan: ' . $request->catatan_auditee : ''),
            ]);

            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('success', 'Konfirmasi berhasil! Menunggu finalisasi dari Admin/Auditor.');
        } elseif ($action === 'submit_revision') {
            // Validasi: Hanya bisa submit revisi jika status = need_revision
            if (!$peta->auditeeNeedRevision()) {
                return redirect()->back()->with('error', 'Tidak ada permintaan revisi yang aktif!');
            }

            $request->validate([
                'answers' => 'required|array|min:1',
                'answers.*.answer' => 'required|string',
                'answers.*.links' => 'nullable|array',
                'answers.*.links.*' => 'nullable|url',
                'answers.*.notes' => 'nullable|string',
            ]);

            // Format jawaban revisi ke JSON
            $revisedAnswersData = [];
            foreach ($request->answers as $index => $item) {
                $revisedAnswersData[] = [
                    'no' => $index + 1,
                    'answer' => $item['answer'],
                    'links' => $item['links'] ?? [],
                    'notes' => $item['notes'] ?? '',
                    'revised_at' => now()->toDateTimeString(),
                ];
            }

            // ✅ Update auditee_response dengan jawaban yang sudah direvisi
            // ✅ Set status_konfirmasi_auditor = 'revision_submitted'
            $peta->update([
                'auditee_response' => json_encode($revisedAnswersData),
                'status_konfirmasi_auditor' => 'revision_submitted', // Menunggu konfirmasi auditor
                'catatan_revisi' => null, // Clear catatan revisi setelah disubmit
            ]);

            // Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => 'Auditee telah mengirim revisi jawaban. Total ' . count($revisedAnswersData) . ' item telah diperbaiki.',
            ]);

            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('success', 'Revisi berhasil dikirim! Menunggu konfirmasi dari Auditor.');
        } else {
            return redirect()->back()->with('error', 'Action tidak valid!');
        }
    }

    /**
     * ✅ REJECT AUDIT - Auditee menolak hasil audit dan meminta perbaikan dari Auditor
     * Route: PUT /auditee/manajemen-risiko/{id}/reject-audit
     * 
     * WORKFLOW:
     * 1. Auditee memberikan catatan penolakan (alasan spesifik)
     * 2. Status konfirmasi Auditor di-RESET agar bisa edit ulang
     * 3. Auditor akan melihat catatan penolakan di halaman detail
     * 4. Auditor dapat mengedit ulang hasil audit per-item
     */
    public function rejectAudit(Request $request, $id)
    {
        $user = Auth::user();

        // 1. CEK: Pastikan user memiliki unit kerja
        if (!$user->unitKerja || !$user->unitKerja->nama_unit_kerja) {
            return redirect()->route('dashboard')->with(
                'error',
                'Anda belum terdaftar di unit kerja manapun!'
            );
        }

        $unitKerjaUser = $user->unitKerja->nama_unit_kerja;

        // 2. Cari risiko berdasarkan ID (hanya milik unit kerja user)
        $peta = Peta::where('jenis', $unitKerjaUser)->findOrFail($id);

        // 3. VALIDASI: Auditor harus sudah set status = Completed
        if ($peta->status_konfirmasi_auditor !== 'Completed') {
            return redirect()->back()->with(
                'error', 
                'Auditor belum menyelesaikan audit! Anda tidak dapat melakukan penolakan.'
            );
        }

        // 4. VALIDASI: Pastikan audit belum final
        if ($peta->isAuditFinal()) {
            return redirect()->back()->with(
                'error', 
                'Audit sudah final! Anda tidak dapat melakukan penolakan.'
            );
        }

        // 5. VALIDASI INPUT: Catatan penolakan wajib diisi (minimal 10 karakter)
        $request->validate([
            'catatan_penolakan' => 'required|string|min:10|max:2000',
        ], [
            'catatan_penolakan.required' => 'Catatan penolakan wajib diisi!',
            'catatan_penolakan.min' => 'Catatan penolakan minimal 10 karakter untuk menjelaskan alasan penolakan secara detail.',
            'catatan_penolakan.max' => 'Catatan penolakan maksimal 2000 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // 6. ✅ SIMPAN DATA PENOLAKAN ke field catatan_revisi (JSON format)
            $rejectionData = [
                'status' => 'rejected_by_auditee',
                'catatan_penolakan' => trim($request->catatan_penolakan),
                'rejected_by' => $user->name,
                'rejected_by_id' => $user->id,
                'rejected_at' => now()->toDateTimeString(),
            ];

            // 7. ✅ UPDATE STATUS AUDIT:
            //    - Reset status_konfirmasi_auditor = NULL (agar Auditor bisa edit ulang)
            //    - Reset status_konfirmasi_auditee = NULL
            //    - Simpan catatan penolakan
            $peta->update([
                'status_konfirmasi_auditor' => null, // ✅ RESET agar Auditor bisa INPUT ULANG
                'status_konfirmasi_auditee' => null, // ✅ RESET status Auditee
                'catatan_revisi' => json_encode($rejectionData), // ✅ Simpan catatan penolakan
            ]);

            // 8. ✅ LOG ACTIVITY ke comment_prs table
            $commentText = '❌ AUDIT DITOLAK oleh Auditee (' . $user->name . ')' . PHP_EOL . PHP_EOL;
            $commentText .= 'Alasan penolakan:' . PHP_EOL;
            $commentText .= substr($request->catatan_penolakan, 0, 200);
            
            if (strlen($request->catatan_penolakan) > 200) {
                $commentText .= '... (lihat detail lengkap di halaman audit)';
            }

            $commentText .= PHP_EOL . PHP_EOL;
            $commentText .= '→ Status audit kembali ke: MENUNGGU PERBAIKAN AUDITOR';
            $commentText .= PHP_EOL;
            $commentText .= '→ Auditor dapat mengedit ulang hasil audit sesuai catatan penolakan.';

            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => $commentText,
            ]);

            DB::commit();

            // 9. ✅ REDIRECT dengan pesan sukses
            return redirect()
                ->route('manajemen-risiko.auditee.show-detail', $peta->id)
                ->with('warning', '⚠️ Hasil audit telah ditolak. Auditor akan menerima notifikasi dan dapat melakukan perbaikan per-item sesuai catatan Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saat reject audit: ' . $e->getMessage());
            
            return redirect()->back()->with(
                'error', 
                'Terjadi kesalahan saat memproses penolakan audit. Silakan coba lagi.'
            );
        }
    }
}
