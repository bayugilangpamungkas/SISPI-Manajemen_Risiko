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

class AuditorController extends Controller
{

    /**
     * ========================================
     * AUDITOR METHODS (Ketua, Anggota, Sekretaris)
     * ========================================
     */
    /**
     * Dashboard Auditor - Menampilkan risiko yang ditugaskan ke auditor yang login
     */
    public function auditorIndex(Request $request)
    {
        $active = 21;
        $user = Auth::user();

        // Get filter parameters
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');
        $statusReview = $request->input('status_review', 'all');

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

        // Get available unit kerja (hanya dari risiko yang ditugaskan ke auditor ini)
        $unitKerjas = Peta::where('auditor_id', $user->id)
            ->select('jenis as nama_unit_kerja')
            ->distinct()
            ->orderBy('jenis')
            ->get();

        // Get auditors list (for view compatibility)
        $auditors = collect();

        // Build query - hanya tampilkan risiko yang ditugaskan ke auditor ini
        $query = Peta::with(['comment_prs.user', 'auditor', 'kegiatan'])
            ->where('auditor_id', $user->id)
            ->whereYear('created_at', $tahun);

        // Filter by unit kerja
        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        // Filter by status review
        if ($statusReview != 'all') {
            if ($statusReview == 'reviewed') {
                $query->where('status_telaah', 1);
            } else {
                $query->where(function ($q) {
                    $q->where('status_telaah', 0)->orWhereNull('status_telaah');
                });
            }
        }

        // Filter by cluster (risk level)
        switch ($cluster) {
            case 'high':
                $query->whereIn('tingkat_risiko', ['Extreme', 'High']);
                break;
            case 'middle':
                $query->where('tingkat_risiko', 'Moderate');
                break;
            case 'low':
                $query->whereIn('tingkat_risiko', ['Low']);
                break;
        }

        // Get paginated data, sorted by risk score (highest first)
        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->paginate(15);

        // Calculate statistics untuk auditor ini
        $statistics = [
            'total' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)->count(),
            'high_risk' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Low')->count(),
            'reviewed' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where('status_telaah', 1)->count(),
            'pending' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where(function ($q) {
                    $q->where('status_telaah', 0)->orWhereNull('status_telaah');
                })->count(),
            'assigned_auditor' => 0,
        ];

        // Count notifikasi (risiko baru yang belum direview)
        $notificationCount = Peta::where('auditor_id', $user->id)
            ->where(function ($q) {
                $q->where('status_telaah', 0)->orWhereNull('status_telaah');
            })
            ->count();

        $auditorFilter = 'all';

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
            'notificationCount',
            'auditors',
            'auditorFilter'
        ));
    }


    /**
     * Detail risiko untuk Auditor - Review data
     */
    public function auditorShowDetail($id)
    {
        $active = 21;
        $user = Auth::user();

        // Pastikan auditor hanya bisa melihat risiko yang ditugaskan ke mereka
        $peta = Peta::with(['comment_prs.user', 'kegiatan', 'auditor'])
            ->where('auditor_id', $user->id)
            ->findOrFail($id);

        return view('manajemen_risiko.show', compact('active', 'peta'));
    }



    /**
     * Update template review oleh Auditor
     */
    public function auditorUpdateTemplate(Request $request, $id)
    {
        $user = Auth::user();

        // Pastikan auditor hanya update data miliknya
        // Saya hapus baris findOrFail kedua agar lebih efisien
        $peta = Peta::where('auditor_id', $user->id)->findOrFail($id);

        // Validasi
        $request->validate([
            'pengendalian' => 'required|string',
            'mitigasi' => 'required|string',
            'komentar_1' => 'required|string',
            'komentar_2' => 'required|string',
            'komentar_3' => 'required|string',
            'status_konfirmasi_auditee' => 'nullable|string',
            'status_konfirmasi_auditor' => 'nullable|string',
        ]);

        // Simpan hasil review ke tabel utama
        $peta->update([
            'pengendalian' => $request->pengendalian,
            'mitigasi' => $request->mitigasi,
            'status_konfirmasi_auditee' => $request->status_konfirmasi_auditee,
            'status_konfirmasi_auditor' => $request->status_konfirmasi_auditor,
        ]);

        // Simpan komentar auditor (gabung jadi satu log)
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'jenis' => 'analisis',
            'comment' =>
            "1. {$request->komentar_1}\n" .
                "2. {$request->komentar_2}\n" .
                "3. {$request->komentar_3}",
        ]);

        // dd($request->all());

        return redirect()
            ->route('manajemen-risiko.auditor.show-detail', $peta->id)
            ->with('success', 'Data berhasil diperbarui. Silakan unduh template untuk review pada langkah kedua.');
    }



    /**
     * Approve risiko - Jika data sesuai
     */
    public function auditorApprove(Request $request, $id)
    {
        $user = Auth::user();

        $peta = Peta::where('auditor_id', $user->id)->findOrFail($id);

        $peta->update([
            'status_telaah' => 1,
            'waktu_telaah_spi' => now(),
        ]);

        // Tambahkan komentar approval
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'comment' => $request->input('comment', 'Data risiko telah direview dan disetujui.'),
            'jenis' => 'analisis',
        ]);

        return redirect()->route('manajemen-risiko.auditor.index')
            ->with('success', 'Risiko berhasil disetujui dan akan dikirim ke Admin!');
    }



    /**
     * Reject risiko - Jika data tidak sesuai, kirim kembali ke auditee
     */
    public function auditorReject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        $peta = Peta::where('auditor_id', $user->id)->findOrFail($id);

        $peta->update([
            'status_telaah' => 0,
            'koreksiPr' => 'rejected',
            'koreksiPr_at' => now(),
        ]);

        // Tambahkan komentar rejection
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'jenis' => 'analisis',
        ]);

        return redirect()->route('manajemen-risiko.auditor.index')
            ->with('success', 'Risiko dikembalikan ke Auditee untuk revisi!');
    }



    /**
     * Generate laporan review untuk Admin
     */
    public function auditorGenerateReport(Request $request)
    {
        $user = Auth::user();
        $unitKerja = $request->input('unit_kerja');
        $tahun = $request->input('tahun', date('Y'));

        if (!$unitKerja || $unitKerja == 'all') {
            return redirect()->back()->with('error', 'Silakan pilih unit kerja terlebih dahulu!');
        }

        $petas = Peta::with(['comment_prs', 'auditor', 'kegiatan'])
            ->where('auditor_id', $user->id)
            ->where('jenis', $unitKerja)
            ->whereYear('created_at', $tahun)
            ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->get();

        if ($petas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data risiko untuk unit kerja ini!');
        }

        return view('manajemen_risiko.generate_report', compact('petas', 'unitKerja', 'tahun', 'user'));
    }



    /**
     * Export Excel untuk Auditor
     */
    public function auditorExport(Request $request)
    {
        $user = Auth::user();
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');

        // Build query - hanya risiko yang ditugaskan ke auditor ini
        $query = Peta::with(['comment_prs'])
            ->where('auditor_id', $user->id)
            ->whereYear('created_at', $tahun);

        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        switch ($cluster) {
            case 'high':
                $query->whereIn('tingkat_risiko', ['EXTREME', 'HIGH']);
                break;
            case 'middle':
                $query->where('tingkat_risiko', 'MIDDLE');
                break;
            case 'low':
                $query->whereIn('tingkat_risiko', ['LOW', 'VERY LOW']);
                break;
        }

        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("SISPI - " . $user->name)
            ->setTitle("Review Risiko - {$tahun}")
            ->setSubject("Laporan Review Risiko oleh Auditor");

        // Header
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'LAPORAN REVIEW RISIKO TAHUN ' . $tahun . ' - ' . strtoupper($user->name));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Column headers
        $headers = ['No', 'Unit Kerja', 'Kategori', 'Judul', 'Kode Registrasi', 'Kemungkinan', 'Dampak', 'Skor Total', 'Tingkat Risiko', 'Status Review', 'Jumlah Komentar', 'Tanggal Review'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '3', $header);
            $column++;
        }

        // Style header
        $sheet->getStyle('A3:L3')->applyFromArray([
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
            $sheet->setCellValue('B' . $row, $peta->jenis);
            $sheet->setCellValue('C' . $row, $peta->kategori);
            $sheet->setCellValue('D' . $row, $peta->judul);
            $sheet->setCellValue('E' . $row, $peta->kode_regist);
            $sheet->setCellValue('F' . $row, $peta->skor_kemungkinan);
            $sheet->setCellValue('G' . $row, $peta->skor_dampak);
            $sheet->setCellValue('H' . $row, $skorTotal);
            $sheet->setCellValue('I' . $row, $peta->tingkat_risiko);
            $sheet->setCellValue('J' . $row, $peta->status_telaah ? 'Direview' : 'Pending');
            $sheet->setCellValue('K' . $row, $peta->comment_prs->count());
            $sheet->setCellValue('L' . $row, $peta->waktu_telaah_spi ? date('d-m-Y', strtotime($peta->waktu_telaah_spi)) : '-');

            // Apply borders
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'Review_Risiko_' . str_replace(' ', '_', $user->name) . '_' . $tahun . '_' . date('Ymd_His') . '.xlsx';

        // Save file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }


    /**
     * Upload lampiran file pendukung oleh Auditor
     */
    public function auditorUploadLampiran(Request $request, $id)
    {
        $request->validate([
            'file_pendukung' => 'required|mimes:pdf,xls,xlsx|max:5120', // Max 5MB
        ]);

        $peta = Peta::findOrFail($id);

        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');

            // Buat nama file yang rapi
            $namaFile = 'Lampiran_Revisi_' . $peta->kode_regist . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Simpan file ke storage (folder public/lampiran_auditor)
            $path = $file->storeAs('public/lampiran_auditor', $namaFile);

            // Update database untuk menyimpan nama filenya
            // Asumsi: Anda memiliki kolom 'file_lampiran' di tabel petas
            $peta->update([
                'file_lampiran' => $namaFile,
                // Jika ingin otomatis mengubah status saat file dikirim:
                'status_konfirmasi_auditor' => 'perlu_revisi'
            ]);

            // dd($request->all());

            return redirect()->back()->with('success', 'File revisi berhasil diupload dan dikirim ke Auditee.');
        }

        return redirect()->back()->with('error', 'Gagal mengupload file.');
    }



    /**
     * Export PDF untuk Auditor
     */
    public function auditorExportPdf($id)
    {
        $peta = Peta::with(['kegiatan', 'auditor', 'comment_prs'])->findOrFail($id);
        $user = Auth::user();

        // Data yang akan dilempar ke view PDF
        $data = [
            'peta' => $peta,
            'user' => $user,
            'tanggal' => date('d F Y')
        ];

        // Menggunakan library DomPDF
        $pdf = Pdf::loadView('manajemen_risiko.auditor_pdf_template', $data);

        // Set landscape jika tabel terlalu lebar
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Monev_Risiko_' . $peta->kode_regist . '.pdf');
    }



    public function auditorShow($id)
    {
        abort(404);
    }

    public function auditorSendTemplate(Request $request, $id)
    {
        abort(404);
    }

    public function auditorUploadReport(Request $request, $id)
    {
        abort(404);
    }
}
