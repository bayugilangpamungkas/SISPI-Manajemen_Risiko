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


class ManajemenRisikoController extends Controller
{
    /**
     * Display a listing of the resource with clustering
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // REDIRECT: Jika user adalah Auditee, redirect ke halaman Auditee
        if ($user->Level && in_array($user->Level->name, ['Auditee', 'PIC'])) {
            return redirect()->route('manajemen-risiko.auditee.index');
        }

        // REDIRECT: Jika user adalah Auditor (Ketua, Anggota, Sekretaris), redirect ke halaman Auditor
        if ($user->Level && in_array($user->Level->name, ['Ketua', 'Anggota', 'Sekretaris'])) {
            return redirect()->route('manajemen-risiko.auditor.index');
        }

        $active = 21;

        // Get filter parameters
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');
        $auditorFilter = $request->input('auditor', 'all');

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

        // Get available unit kerja
        $unitKerjas = Peta::select('jenis as nama_unit_kerja')
            ->distinct()
            ->orderBy('jenis')
            ->get();

        // Get auditors (users with role Ketua, Anggota, Sekretaris)
        $auditors = User::whereHas('Level', function ($query) {
            $query->whereIn('name', ['Ketua', 'Anggota', 'Sekretaris']);
        })->get();

        // Build query with filters - HANYA TAMPILKAN DATA YANG SUDAH DITANDAI
        $query = Peta::with(['comment_prs.user', 'auditor', 'kegiatan'])
            ->where('tampil_manajemen_risiko', 1)  // Filter utama: hanya yang ditandai tampil
            ->whereYear('created_at', $tahun);

        // Filter by unit kerja
        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        // Filter by auditor
        if ($auditorFilter != 'all') {
            if ($auditorFilter == 'unassigned') {
                $query->whereNull('auditor_id');
            } else {
                $query->where('auditor_id', $auditorFilter);
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

        // Calculate statistics - HANYA UNTUK DATA YANG DITAMPILKAN
        $statistics = [
            'total' => Peta::where('tampil_manajemen_risiko', 1)
                ->whereYear('created_at', $tahun)->count(),
            'high_risk' => Peta::where('tampil_manajemen_risiko', 1)
                ->whereYear('created_at', $tahun)
                ->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => Peta::where('tampil_manajemen_risiko', 1)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => Peta::where('tampil_manajemen_risiko', 1)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Low')->count(),
            'assigned_auditor' => Peta::where('tampil_manajemen_risiko', 1)
                ->whereYear('created_at', $tahun)
                ->whereNotNull('auditor_id')->count(),
            'reviewed' => Peta::where('tampil_manajemen_risiko', 1)
                ->whereYear('created_at', $tahun)
                ->where('status_telaah', 1)->count(),
        ];

        return view('manajemen_risiko.index', compact(
            'active',
            'petas',
            'statistics',
            'cluster',
            'tahun',
            'unitKerja',
            'years',
            'unitKerjas',
            'auditors',
            'auditorFilter'
        ));
    }

    /**
     * Display the specified resource
     */
    public function show($id)
    {
        $active = 21;
        $peta = Peta::with(['comment_prs.user', 'kegiatan', 'auditor'])->findOrFail($id);

        return view('manajemen_risiko.show', compact('active', 'peta'));
    }

    /**
     * Assign auditor to risk
     */
    public function assignAuditor(Request $request, $id)
    {
        $request->validate([
            'auditor_id' => 'required|exists:users,id',
        ]);

        $peta = Peta::findOrFail($id);
        $peta->update([
            'auditor_id' => $request->auditor_id,
        ]);

        return redirect()->back()->with('success', 'Auditor berhasil ditugaskan!');
    }

    /**
     * Store comment for a risk
     */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|in:keuangan,analisis,mitigasi',
            'comment' => 'required|string|max:1000',
        ]);

        $peta = Peta::findOrFail($id);

        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'jenis' => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Update status telaah
     */
    public function updateStatus(Request $request, $id)
    {
        $peta = Peta::findOrFail($id);

        $peta->update([
            'status_telaah' => $request->status_telaah,
            'waktu_telaah_spi' => now(),
        ]);

        return redirect()->back()->with('success', 'Status risiko berhasil diperbarui!');
    }

    /**
     * Generate report per unit
     */
    public function generateReport(Request $request)
    {
        $unitKerja = $request->input('unit_kerja');
        $tahun = $request->input('tahun', date('Y'));

        if (!$unitKerja || $unitKerja == 'all') {
            return redirect()->back()->with('error', 'Silakan pilih unit kerja terlebih dahulu!');
        }

        $petas = Peta::with(['comment_prs', 'auditor', 'kegiatan'])
            ->where('jenis', $unitKerja)
            ->whereYear('created_at', $tahun)
            ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->get();

        if ($petas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data risiko untuk unit kerja ini!');
        }

        return view('manajemen_risiko.generate_report', compact('petas', 'unitKerja', 'tahun'));
    }

    /**
     * Upload report files
     */
    public function uploadReport(Request $request, $id)
    {
        $request->validate([
            'laporan_unit' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'laporan_spi' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $peta = Peta::findOrFail($id);

        if ($request->hasFile('laporan_unit')) {
            // Delete old file if exists
            if ($peta->laporan_unit) {
                Storage::delete('public/laporan_unit/' . $peta->laporan_unit);
            }

            $file = $request->file('laporan_unit');
            $filename = time() . '_unit_' . $file->getClientOriginalName();
            $file->storeAs('public/laporan_unit', $filename);
            $peta->laporan_unit = $filename;
        }

        if ($request->hasFile('laporan_spi')) {
            // Delete old file if exists
            if ($peta->laporan_spi) {
                Storage::delete('public/laporan_spi/' . $peta->laporan_spi);
            }

            $file = $request->file('laporan_spi');
            $filename = time() . '_spi_' . $file->getClientOriginalName();
            $file->storeAs('public/laporan_spi', $filename);
            $peta->laporan_spi = $filename;
        }

        $peta->save();

        return redirect()->back()->with('success', 'Laporan berhasil diupload!');
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');

        // Build query with same filters
        $query = Peta::with(['comment_prs'])
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
            ->setCreator("SISPI")
            ->setTitle("Manajemen Risiko - {$tahun}")
            ->setSubject("Laporan Manajemen Risiko");

        // Header
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'LAPORAN MANAJEMEN RISIKO TAHUN ' . $tahun);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Column headers
        $headers = ['No', 'Unit Kerja', 'Kategori', 'Judul', 'Kode Registrasi', 'Kemungkinan', 'Dampak', 'Skor Total', 'Tingkat Risiko', 'Status', 'Jumlah Komentar'];
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
            $sheet->setCellValue('B' . $row, $peta->jenis);
            $sheet->setCellValue('C' . $row, $peta->kategori);
            $sheet->setCellValue('D' . $row, $peta->judul);
            $sheet->setCellValue('E' . $row, $peta->kode_regist);
            $sheet->setCellValue('F' . $row, $peta->skor_kemungkinan);
            $sheet->setCellValue('G' . $row, $peta->skor_dampak);
            $sheet->setCellValue('H' . $row, $skorTotal);
            $sheet->setCellValue('I' . $row, $peta->tingkat_risiko);
            $sheet->setCellValue('J' . $row, $peta->status_telaah ? 'Ditelaah' : 'Pending');
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
        $filename = 'Manajemen_Risiko_' . $tahun . '_' . date('Ymd_His') . '.xlsx';

        // Save file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Detail Risiko per Kegiatan (untuk Modal di Detail Unit Kerja)
     */
    public function detailRisikoKegiatan($unitKerja, $kegiatanId, Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // Get kegiatan
        $kegiatan = \App\Models\Kegiatan::findOrFail($kegiatanId);

        // Get all peta risiko untuk kegiatan ini
        $petas = Peta::where('id_kegiatan', $kegiatanId)
            ->whereYear('created_at', $tahun)
            ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->get();

        // Return HTML untuk modal
        $html = view('manajemen_risiko.partials.detail_risiko_kegiatan', compact('kegiatan', 'petas', 'unitKerja', 'tahun'))->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Halaman Data Manajemen Risiko (Clustering)
     * Route: GET /manajemen-risiko/data
     * HANYA ADMIN yang bisa akses halaman ini
     */
    public function dataManajemenRisiko(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');
        $kegiatanId = $request->input('id_kegiatan', 'all');
        $cluster = $request->input('cluster', 'all');

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

        // Get available unit kerja
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit_kerja')->get();


        // Get available kegiatan (filtered by unit kerja if selected)
        $kegiatansQuery = \App\Models\Kegiatan::query();
        if ($unitKerja != 'all') {
            $selectedUnitKerja = \App\Models\UnitKerja::where('nama_unit_kerja', $unitKerja)->first();
            if ($selectedUnitKerja) {
                $kegiatansQuery->where('id_unit_kerja', $selectedUnitKerja->id);
            }
        }
        $kegiatans = $kegiatansQuery->orderBy('judul')->get();

        // Query untuk mengambil data
        $query = Peta::with(['kegiatan'])
            ->whereYear('created_at', $tahun);

        // Filter by unit kerja
        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        // Filter by kegiatan
        if ($kegiatanId != 'all') {
            $query->where('id_kegiatan', $kegiatanId);
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

        // ✅ AMBIL DATA DENGAN PAGINATION BIASA
        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->paginate(20);

        // ✅ HITUNG STATISTICS DARI QUERY YANG SAMA (tanpa pagination)
        $allPetasForStats = clone $query;
        $allPetas = $allPetasForStats->get();

        $statistics = [
            'total' => $allPetas->count(),
            'high_risk' => $allPetas->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => $allPetas->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => $allPetas->where('tingkat_risiko', 'Low')->count(),
        ];

        // ✅ GET UNTUK PERHITUNGAN GROUPED (untuk display di table)
        $allPetasForGroup = Peta::with(['kegiatan'])
            ->whereYear('created_at', $tahun)
            ->get();

        $groupedPetas = $allPetasForGroup->groupBy('jenis');

        return view('manajemen_risiko.data_manajemen_risiko', compact(
            'petas',           // Untuk pagination
            'groupedPetas',    // Untuk perhitungan jumlah kegiatan
            'statistics',
            'tahun',
            'unitKerja',
            'kegiatanId',
            'cluster',
            'years',
            'unitKerjas',
            'kegiatans'
        ));
    }

    /**
     * Detail Unit Kerja - Halaman untuk kelola visibilitas risiko per kegiatan
     * Route: GET /manajemen-risiko/detail-unit/{unitKerja}
     */
    public function detailUnitKerja($unitKerja, Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // Get unit kerja model
        $unitKerjaModel = \App\Models\UnitKerja::where('nama_unit_kerja', $unitKerja)->first();

        if (!$unitKerjaModel) {
            return redirect()->route('manajemen-risiko.data')->with('error', 'Unit kerja tidak ditemukan!');
        }

        // ✅ PERBAIKAN: Get SEMUA kegiatan untuk unit kerja ini
        $allKegiatans = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)
            ->orderBy('judul')
            ->get();

        // ✅ PERBAIKAN: Prepare data kegiatan dengan risiko - TAMPILKAN SEMUA
        $kegiatanWithRisiko = [];
        foreach ($allKegiatans as $kegiatan) {
            // Get SEMUA peta risiko untuk kegiatan ini di tahun tertentu
            $petas = Peta::where('id_kegiatan', $kegiatan->id)
                ->whereYear('created_at', $tahun)
                ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
                ->get();

            // ✅ Hitung total skor risiko
            $totalSkorRisiko = 0;
            foreach ($petas as $peta) {
                $totalSkorRisiko += ($peta->skor_kemungkinan * $peta->skor_dampak);
            }

            // ✅ Tambahkan ke array (termasuk yang tidak punya risiko)
            $kegiatanWithRisiko[] = [
                'kegiatan' => $kegiatan,
                'petas' => $petas,
                'jumlah_risiko' => $petas->count(),
                'sudah_tampil' => $petas->where('tampil_manajemen_risiko', 1)->count(),
                'total_skor_risiko' => $totalSkorRisiko,
            ];
        }

        // ✅ Sort berdasarkan total skor risiko (tertinggi ke terendah)
        usort($kegiatanWithRisiko, function ($a, $b) {
            return $b['total_skor_risiko'] <=> $a['total_skor_risiko'];
        });

        // ✅ Pagination manual (karena sudah di-sort)
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $kegiatanPaginated = array_slice($kegiatanWithRisiko, $offset, $perPage);

        // Create paginator
        $kegiatans = new \Illuminate\Pagination\LengthAwarePaginator(
            $kegiatanPaginated,
            count($kegiatanWithRisiko),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ✅ Calculate statistics untuk unit kerja ini
        $totalKegiatan = $allKegiatans->count();

        $allRisiko = Peta::whereIn('id_kegiatan', $allKegiatans->pluck('id'))
            ->whereYear('created_at', $tahun)
            ->get();

        $totalRisiko = $allRisiko->count();
        $totalTampil = $allRisiko->where('tampil_manajemen_risiko', 1)->count();

        $totalSkorUnit = 0;
        foreach ($allRisiko as $peta) {
            $totalSkorUnit += ($peta->skor_kemungkinan * $peta->skor_dampak);
        }

        $statistics = [
            'total_kegiatan' => $totalKegiatan,
            'total_risiko' => $totalRisiko,
            'total_tampil' => $totalTampil,
            'total_skor_unit' => $totalSkorUnit,
        ];

        return view('manajemen_risiko.detail_unit_kerja', compact(
            'unitKerja',
            'unitKerjaModel',
            'tahun',
            'kegiatanWithRisiko',
            'kegiatans',
            'statistics'
        ));
    }

    /**
     * Update tampil manajemen risiko (centang data untuk ditampilkan)
     * Route: POST /manajemen-risiko/data/update-tampil
     */
    public function updateTampilManajemenRisiko(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:petas,id',
        ]);

        try {
            Peta::whereIn('id', $request->selected_ids)->update([
                'tampil_manajemen_risiko' => 1
            ]);

            // UBAH BAGIAN INI: Kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back()->with('success', count($request->selected_ids) . ' risiko berhasil ditampilkan!');
        } catch (\Exception $e) {
            // UBAH BAGIAN INI: Kembali dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Sembunyikan data dari manajemen risiko
     * Route: POST /manajemen-risiko/data/hide-tampil
     */
    public function hideTampilManajemenRisiko(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:petas,id',
        ]);

        try {
            // Update selected items to be hidden
            Peta::whereIn('id', $request->selected_ids)->update([
                'tampil_manajemen_risiko' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => count($request->selected_ids) . ' risiko berhasil disembunyikan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tampilkanKegiatan(Request $request)
    {
        // DEBUG: Log request untuk troubleshooting
        Log::info('=== TAMPILKAN KEGIATAN REQUEST ===');
        Log::info('Request Data:', $request->all());

        // Validasi input
        $validator = Validator::make($request->all(), [
            'kegiatan_ids' => 'required|array|min:1',
            'kegiatan_ids.*' => 'required|integer|exists:kegiatans,id',
            'unit_kerja' => 'required|string|max:255',
            'tahun' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 5)
        ], [
            'kegiatan_ids.required' => 'Pilih minimal satu kegiatan',
            'kegiatan_ids.array' => 'Format data kegiatan tidak valid',
            'kegiatan_ids.min' => 'Pilih minimal satu kegiatan',
            'kegiatan_ids.*.exists' => 'Salah satu kegiatan tidak ditemukan di database',
            'unit_kerja.required' => 'Unit kerja harus diisi',
            'tahun.required' => 'Tahun harus diisi',
            'tahun.integer' => 'Format tahun tidak valid'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            Log::error('Validasi gagal:', $validator->errors()->toArray());
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. ' . implode(' ', $validator->errors()->all()));
        }

        try {
            // Ambil data dari request
            $kegiatanIds = $request->kegiatan_ids;
            $unitKerja = trim($request->unit_kerja);
            $tahun = (int) $request->tahun;

            Log::info('Processing request:', [
                'unit_kerja' => $unitKerja,
                'tahun' => $tahun,
                'kegiatan_ids_count' => count($kegiatanIds),
                'kegiatan_ids' => $kegiatanIds
            ]);

            // 1. Cari unit kerja berdasarkan nama
            $unitKerjaModel = \App\Models\UnitKerja::where('nama_unit_kerja', $unitKerja)->first();

            if (!$unitKerjaModel) {
                Log::error('Unit kerja tidak ditemukan: ' . $unitKerja);
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Unit kerja '{$unitKerja}' tidak ditemukan dalam database.");
            }

            Log::info('Unit kerja ditemukan:', [
                'id' => $unitKerjaModel->id,
                'nama' => $unitKerjaModel->nama_unit_kerja
            ]);

            // 2. Cari semua kegiatan yang valid untuk unit kerja ini
            $validKegiatanIds = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)
                ->whereIn('id', $kegiatanIds)
                ->pluck('id')
                ->toArray();

            if (empty($validKegiatanIds)) {
                Log::error('Tidak ada kegiatan valid untuk unit ini');
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Tidak ada kegiatan valid yang ditemukan untuk unit kerja ini.');
            }

            Log::info('Kegiatan valid ditemukan:', [
                'count' => count($validKegiatanIds),
                'ids' => $validKegiatanIds
            ]);

            // 3. Mulai database transaction untuk konsistensi data
            DB::beginTransaction();

            try {
                // 3A. Reset SEMUA risiko dari unit kerja ini di tahun tertentu (tidak tampil)
                $resetCount = Peta::where('jenis', $unitKerja)
                    ->whereYear('created_at', $tahun)
                    ->update(['tampil_manajemen_risiko' => 0]);

                Log::info('Reset status tampil:', [
                    'jumlah_direset' => $resetCount,
                    'unit_kerja' => $unitKerja,
                    'tahun' => $tahun
                ]);

                // 3B. Tampilkan hanya risiko dari kegiatan yang dipilih
                if (!empty($validKegiatanIds)) {
                    $updateCount = Peta::whereIn('id_kegiatan', $validKegiatanIds)
                        ->whereYear('created_at', $tahun)
                        ->update(['tampil_manajemen_risiko' => 1]);

                    Log::info('Update status tampil:', [
                        'jumlah_diupdate' => $updateCount,
                        'kegiatan_ids' => $validKegiatanIds
                    ]);
                }

                // 4. Hitung statistik setelah update
                // 4A. Hitung jumlah kegiatan yang memiliki risiko TAMPIL
                $jumlahKegiatanTampil = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)
                    ->whereHas('petas', function ($query) use ($tahun) {
                        $query->whereYear('created_at', $tahun)
                            ->where('tampil_manajemen_risiko', 1);
                    })
                    ->distinct()
                    ->count('id');

                // 4B. Hitung total risiko yang TAMPIL untuk kegiatan yang dipilih
                $jumlahRisikoTampil = Peta::whereIn('id_kegiatan', $validKegiatanIds)
                    ->whereYear('created_at', $tahun)
                    ->where('tampil_manajemen_risiko', 1)
                    ->count();

                // 4C. Hitung total semua kegiatan di unit kerja (untuk informasi)
                $totalKegiatanUnit = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)->count();

                // 4D. Hitung total semua risiko di unit kerja (untuk informasi)
                $totalRisikoUnit = Peta::where('jenis', $unitKerja)
                    ->whereYear('created_at', $tahun)
                    ->count();

                Log::info('Statistik setelah update:', [
                    'kegiatan_tampil' => $jumlahKegiatanTampil,
                    'risiko_tampil' => $jumlahRisikoTampil,
                    'total_kegiatan_unit' => $totalKegiatanUnit,
                    'total_risiko_unit' => $totalRisikoUnit
                ]);

                // 5. Commit transaction
                DB::commit();

                // 6. Clear cache jika menggunakan cache
                if (\Illuminate\Support\Facades\Cache::supportsTags()) {
                    \Illuminate\Support\Facades\Cache::tags(['manajemen-risiko', 'kegiatan'])->flush();
                }

                // Hapus cache spesifik
                $cacheKey = 'kegiatan_tampil_' . $unitKerjaModel->id . '_' . $tahun;
                \Illuminate\Support\Facades\Cache::forget($cacheKey);

                // 7. Siapkan pesan sukses
                $successMessage = "✅ **Update Berhasil!**\n\n";
                $successMessage .= "**Detail Update:**\n";
                $successMessage .= "• Unit Kerja: **{$unitKerja}**\n";
                $successMessage .= "• Tahun: **{$tahun}**\n";
                $successMessage .= "• Kegiatan Dipilih: **" . count($validKegiatanIds) . "** dari {$totalKegiatanUnit}\n";
                $successMessage .= "• Kegiatan Ditampilkan: **{$jumlahKegiatanTampil}**\n";
                $successMessage .= "• Risiko Ditampilkan: **{$jumlahRisikoTampil}** dari {$totalRisikoUnit}\n\n";
                $successMessage .= "Jumlah kegiatan di halaman Data Manajemen Risiko sekarang akan menampilkan: **{$jumlahKegiatanTampil} kegiatan**";

                // 8. Redirect dengan data lengkap
                return redirect()
                    ->route('manajemen-risiko.detail-unit', [
                        'unitKerja' => $unitKerja,
                        'tahun' => $tahun
                    ])
                    ->with([
                        'success' => $successMessage,
                        'statistics' => [
                            'kegiatan_dipilih' => count($validKegiatanIds),
                            'kegiatan_tampil' => $jumlahKegiatanTampil,
                            'risiko_tampil' => $jumlahRisikoTampil,
                            'total_kegiatan' => $totalKegiatanUnit,
                            'total_risiko' => $totalRisikoUnit
                        ],
                        'auto_refresh' => true, // Flag untuk auto-refresh halaman utama
                        'updated_unit' => $unitKerja,
                        'updated_tahun' => $tahun
                    ]);
            } catch (\Exception $e) {
                // Rollback transaction jika error
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Error:', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', "❌ **Database Error:** " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', "❌ **Terjadi Kesalahan Sistem:** " . $e->getMessage());
        }
    }

    /**
     * Download Template PDF
     */
    public function downloadTemplatePDF(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');

        // Build query
        $query = Peta::whereYear('created_at', $tahun);

        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')->get();

        $pdf = Pdf::loadView('manajemen_risiko.template_pdf', compact('petas', 'tahun', 'unitKerja'));

        $filename = 'Template_Manajemen_Risiko_' . $tahun . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Download Template Excel
     */
    public function downloadTemplateExcel(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');

        // Build query
        $query = Peta::whereYear('created_at', $tahun);

        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['No', 'Unit Kerja', 'Kategori', 'Judul', 'Kemungkinan', 'Dampak', 'Skor', 'Tingkat'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Add data
        $row = 2;
        $no = 1;
        foreach ($petas as $peta) {
            $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $peta->jenis);
            $sheet->setCellValue('C' . $row, $peta->kategori);
            $sheet->setCellValue('D' . $row, $peta->judul);
            $sheet->setCellValue('E' . $row, $peta->skor_kemungkinan);
            $sheet->setCellValue('F' . $row, $peta->skor_dampak);
            $sheet->setCellValue('G' . $row, $skorTotal);
            $sheet->setCellValue('H' . $row, $peta->tingkat_risiko);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Manajemen_Risiko_' . $tahun . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }



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

        // 4. Validasi akses: Pastikan risiko milik unit kerja user yang sedang login
        if ($peta->jenis !== $unitKerjaUser) {
            return redirect()->route('manajemen-risiko.auditee.index')
                ->with('error', 'Anda tidak memiliki akses ke detail risiko ini.');
        }

        // 5. Tampilkan View
        // Pastikan Anda sudah membuat file view: resources/views/manajemen_risiko/show_detail.blade.php
        // Jika ingin menggunakan tampilan yang sama dengan auditeeShow, ganti jadi 'manajemen_risiko.show'
        return view('manajemen_risiko.auditee_detail', compact('active', 'peta'));
    }
}
