<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peta;
use App\Models\CommentPr;
use App\Models\User;
use App\Models\HasilAudit;
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
use Illuminate\Support\Facades\Mail;


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
        $perPage = 20;
        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->paginate($perPage);

        // ✅ HITUNG STATISTICS DARI QUERY YANG SAMA (tanpa pagination)
        $allPetasForStats = Peta::whereYear('created_at', $tahun)->get();
        $statistics = [
            'total' => $allPetasForStats->count(),
            'high_risk' => $allPetasForStats->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => $allPetasForStats->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => $allPetasForStats->where('tingkat_risiko', 'Low')->count(),
        ];

        // ✅ GET UNTUK PERHITUNGAN GROUPED (untuk display di table)
        $allPetasForGroup = Peta::with(['kegiatan'])
            ->whereYear('created_at', $tahun)
            ->get();

        $groupedPetas = $allPetasForGroup->groupBy('jenis');

        return view('manajemen_risiko.data_manajemen_risiko', compact(
            'petas',
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

        // ✅ Get SEMUA kegiatan untuk unit kerja ini
        $allKegiatans = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)
            ->orderBy('judul')
            ->get();

        // ✅ UBAH: Expand setiap risiko menjadi baris terpisah
        $expandedRows = [];

        foreach ($allKegiatans as $kegiatan) {
            // Get SEMUA peta risiko untuk kegiatan ini di tahun tertentu
            $petas = Peta::where('id_kegiatan', $kegiatan->id)
                ->whereYear('created_at', $tahun)
                ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
                ->get();

            $jumlahRisiko = $petas->count();
            $sudahTampil = $petas->where('tampil_manajemen_risiko', 1)->count();

            // Hitung total skor risiko untuk kegiatan ini
            $totalSkorRisiko = 0;
            foreach ($petas as $peta) {
                $totalSkorRisiko += ($peta->skor_kemungkinan * $peta->skor_dampak);
            }

            // Jika ada risiko, buat baris untuk SETIAP risiko
            if ($jumlahRisiko > 0) {
                foreach ($petas as $index => $peta) {
                    $expandedRows[] = [
                        'kegiatan' => $kegiatan,
                        'peta' => $peta, // Risiko spesifik untuk baris ini
                        'jumlah_risiko' => $jumlahRisiko,
                        'sudah_tampil' => $sudahTampil,
                        'total_skor_risiko' => $totalSkorRisiko,
                        'is_first_row' => $index === 0, // Untuk rowspan di view
                        'rowspan' => $jumlahRisiko, // Jumlah baris untuk rowspan
                    ];
                }
            } else {
                // Jika tidak ada risiko, tetap buat 1 baris
                $expandedRows[] = [
                    'kegiatan' => $kegiatan,
                    'peta' => null,
                    'jumlah_risiko' => 0,
                    'sudah_tampil' => 0,
                    'total_skor_risiko' => 0,
                    'is_first_row' => true,
                    'rowspan' => 1,
                ];
            }
        }

        // ✅ Sort berdasarkan total skor risiko (tertinggi ke terendah)
        usort($expandedRows, function ($a, $b) {
            return $b['total_skor_risiko'] <=> $a['total_skor_risiko'];
        });

        // ✅ Pagination manual
        $perPage = 20; // Naikkan karena sekarang per-risiko
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $rowsPaginated = array_slice($expandedRows, $offset, $perPage);

        // Create paginator
        $kegiatans = new \Illuminate\Pagination\LengthAwarePaginator(
            $rowsPaginated,
            count($expandedRows),
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

            // Dapatkan tahun dari risiko yang di-update (untuk redirect dengan filter tahun yang sama)
            $tahun = Peta::whereIn('id', $request->selected_ids)
                ->selectRaw('YEAR(created_at) as tahun')
                ->first()
                ->tahun ?? date('Y');

            // ✅ REDIRECT KE HALAMAN MANAJEMEN RISIKO (bukan ke data manajemen risiko)
            return redirect()
                ->route('manajemen-risiko.index', ['tahun' => $tahun])
                ->with('success', '✅ Berhasil! ' . count($request->selected_ids) . ' risiko telah ditampilkan di Manajemen Risiko!');
        } catch (\Exception $e) {
            // Kembali ke halaman sebelumnya dengan pesan error
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
        Log::info('=== TAMPILKAN KEGIATAN REQUEST ===');
        Log::info('Request Data:', $request->all());

        // Validasi
        $request->validate([
            'kegiatan_ids' => 'required|array|min:1',
            'kegiatan_ids.*' => 'required|string',
            'unit_kerja' => 'required|string',
            'tahun' => 'required|integer'
        ]);

        try {
            $kegiatanIds = $request->kegiatan_ids;
            $unitKerja = $request->unit_kerja;
            $tahun = $request->tahun;

            Log::info('Processing request:', [
                'unit_kerja' => $unitKerja,
                'tahun' => $tahun,
                'kegiatan_ids_count' => count($kegiatanIds),
                'kegiatan_ids' => $kegiatanIds
            ]);

            // 1. Cari unit kerja
            $unitKerjaModel = \App\Models\UnitKerja::where('nama_unit_kerja', $unitKerja)->first();

            if (!$unitKerjaModel) {
                return redirect()->back()->with('error', 'Unit kerja tidak ditemukan!');
            }

            Log::info('Unit kerja ditemukan:', [
                'id' => $unitKerjaModel->id,
                'nama' => $unitKerjaModel->nama_unit_kerja
            ]);

            // 2. Cek apakah kegiatan valid (berdasarkan id_kegiatan)
            $validKegiatanIds = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)
                ->whereIn('id_kegiatan', $kegiatanIds)
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

            // 3. Mulai transaction
            DB::beginTransaction();

            try {
                // 3A. Reset semua risiko dari unit kerja ini
                $resetCount = Peta::whereIn('id_kegiatan', function ($query) use ($unitKerjaModel) {
                    $query->select('id_kegiatan')
                        ->from('kegiatans')
                        ->where('id_unit_kerja', $unitKerjaModel->id);
                })
                    ->whereYear('created_at', $tahun)
                    ->update(['tampil_manajemen_risiko' => 0]);

                Log::info('Reset status tampil:', [
                    'jumlah_direset' => $resetCount,
                    'unit_kerja' => $unitKerja,
                    'tahun' => $tahun
                ]);

                // 3B. Tampilkan risiko dari kegiatan yang dipilih
                if (!empty($validKegiatanIds)) {
                    $updateCount = Peta::whereIn('id_kegiatan', $validKegiatanIds)
                        ->whereYear('created_at', $tahun)
                        ->update(['tampil_manajemen_risiko' => 1]);

                    Log::info('Update status tampil:', [
                        'jumlah_diupdate' => $updateCount,
                        'kegiatan_ids' => $validKegiatanIds
                    ]);
                }

                $jumlahKegiatanTampil = count($validKegiatanIds);

                // Hitung total risiko yang tampil
                $jumlahRisikoTampil = Peta::whereIn('id_kegiatan', function ($query) use ($unitKerjaModel) {
                    $query->select('id_kegiatan')
                        ->from('kegiatans')
                        ->where('id_unit_kerja', $unitKerjaModel->id);
                })
                    ->whereYear('created_at', $tahun)
                    ->where('tampil_manajemen_risiko', 1)
                    ->count();

                // Total semua kegiatan di unit
                $totalKegiatanUnit = \App\Models\Kegiatan::where('id_unit_kerja', $unitKerjaModel->id)->count();

                Log::info('Statistik setelah update:', [
                    'kegiatan_dipilih' => count($validKegiatanIds),
                    'risiko_diupdate' => $updateCount,
                    'total_kegiatan_unit' => $totalKegiatanUnit,
                    'catatan' => 'Jumlah kegiatan tampil mengikuti pilihan user'
                ]);

                // 5. Commit transaction
                DB::commit();

                // 6. Pesan sukses
                $successMessage = "✅ Berhasil memilih kegiatan untuk {$unitKerja}! ";
                $successMessage .= "{$jumlahKegiatanTampil} kegiatan dari {$totalKegiatanUnit} kegiatan telah dipilih. ";
                $successMessage .= "Total {$jumlahRisikoTampil} risiko siap ditampilkan.";

                // ✅ REDIRECT KE DATA MANAJEMEN RISIKO (bukan ke detail-unit lagi)
                return redirect()
                    ->route('manajemen-risiko.data', ['tahun' => $tahun])
                    ->with('success', $successMessage);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in tampilkanKegiatan: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "❌ Error: " . $e->getMessage());
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
     * Update audit template (save to hasil_audit table)
     */
    public function updateAuditorTemplate(Request $request, $id)
    {
        $request->validate([
            'pengendalian' => 'required|string',
            'mitigasi' => 'required|string',
            'komentar_1' => 'required|string',
            'komentar_2' => 'required|string',
            'komentar_3' => 'required|string',
            'status_konfirmasi_auditee' => 'nullable|string',
            'status_konfirmasi_auditor' => 'nullable|string',
        ]);

        $user = Auth::user();
        $peta = Peta::with('kegiatan')->findOrFail($id);

        // Calculate score
        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // Determine level
        if ($skorTotal >= 15) {
            $levelText = 'HIGH';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
        } else {
            $levelText = 'LOW';
        }

        // Calculate residual risk
        if ($skorTotal >= 20) {
            $residualText = 'Extreme';
        } elseif ($skorTotal >= 15) {
            $residualText = 'High';
        } elseif ($skorTotal >= 10) {
            $residualText = 'Moderate';
        } else {
            $residualText = 'Low';
        }

        // Create or update hasil audit
        $hasilAudit = HasilAudit::updateOrCreate(
            [
                'peta_id' => $peta->id,
                'auditor_id' => $user->id,
                'tahun_anggaran' => date('Y'),
            ],
            [
                'komentar_1' => $request->komentar_1,
                'komentar_2' => $request->komentar_2,
                'komentar_3' => $request->komentar_3,
                'pengendalian' => $request->pengendalian,
                'mitigasi' => $request->mitigasi,
                'status_konfirmasi_auditee' => $request->status_konfirmasi_auditee ?? null,
                'status_konfirmasi_auditor' => $request->status_konfirmasi_auditor ?? null,
                'unit_kerja' => $peta->jenis,
                'kode_risiko' => $peta->kode_regist,
                'kegiatan' => $peta->kegiatan->judul ?? $peta->judul,
                'level_risiko' => $levelText,
                'risiko_residual' => $residualText,
                'skor_total' => $skorTotal,
                'nama_pemonev' => $user->name,
                'nip_pemonev' => $user->nip,
            ]
        );

        return redirect()->back()->with('success', 'Data audit berhasil disimpan!');
    }

    /**
     * Export audit template to PDF
     */
    public function exportAuditorPdf($id)
    {
        $user = Auth::user();
        $peta = Peta::with(['comment_prs.user', 'kegiatan', 'auditor'])->findOrFail($id);

        // Get hasil audit if exists
        $hasilAudit = HasilAudit::where('peta_id', $peta->id)
            ->where('auditor_id', $user->id)
            ->where('tahun_anggaran', date('Y'))
            ->first();

        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // LEVEL
        if ($skorTotal >= 20) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 15) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
            $badgeClass = 'bg-warning text-dark';
        } else {
            $levelText = 'LOW';
            $badgeClass = 'bg-success text-white';
        }

        // RESIDUAL
        if ($skorTotal >= 20) {
            $residualText = 'Extreme';
            $residualClass = 'bg-danger text-white';
        } elseif ($skorTotal >= 15) {
            $residualText = 'High';
            $residualClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $residualText = 'Moderate';
            $residualClass = 'bg-info text-dark';
        } else {
            $residualText = 'Low';
            $residualClass = 'bg-success text-white';
        }

        $pdf = Pdf::loadView('manajemen_risiko.export_audit_pdf', compact(
            'peta',
            'hasilAudit',
            'user',
            'skorTotal',
            'levelText',
            'badgeClass',
            'residualText',
            'residualClass'
        ));

        $filename = 'Audit_' . $peta->kode_regist . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Upload lampiran audit
     */
    public function uploadAuditorLampiran(Request $request, $id)
    {
        $request->validate([
            'file_pendukung' => 'required|file|mimes:pdf,xls,xlsx|max:10240',
        ]);

        $peta = Peta::findOrFail($id);
        $user = Auth::user();

        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            $filename = 'audit_' . $peta->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/audit_lampiran', $filename);

            // Update hasil audit with file path
            HasilAudit::where('peta_id', $peta->id)
                ->where('auditor_id', $user->id)
                ->where('tahun_anggaran', date('Y'))
                ->update([
                    'file_lampiran' => $filename,
                ]);

            return redirect()->back()->with('success', 'File berhasil dikirim ke Auditee!');
        }

        return redirect()->back()->with('error', 'File gagal diupload!');
    }

    /**
     * Display hasil audit index (for Admin)
     */
    public function hasilAuditIndex(Request $request)
    {
        $active = 23; // Menu ID untuk Hasil Audit
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');
        $auditorFilter = $request->input('auditor', 'all');

        // Get available years
        $years = HasilAudit::selectRaw('DISTINCT tahun_anggaran')
            ->orderBy('tahun_anggaran', 'desc')
            ->pluck('tahun_anggaran');

        // Get unit kerjas
        $unitKerjas = DB::table('unit_kerjas')
            ->select('nama_unit_kerja')
            ->distinct()
            ->orderBy('nama_unit_kerja')
            ->get();

        // Get auditors
        $auditors = User::whereHas('Level', function ($q) {
            $q->whereIn('name', ['Ketua', 'Anggota', 'Sekretaris']);
        })->get();

        // Build query
        $query = HasilAudit::with(['peta', 'auditor'])
            ->where('tahun_anggaran', $tahun);

        if ($unitKerja != 'all') {
            $query->where('unit_kerja', $unitKerja);
        }

        if ($auditorFilter != 'all') {
            $query->where('auditor_id', $auditorFilter);
        }

        $hasilAudits = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('manajemen_risiko.hasil_audit.index', compact(
            'active',
            'hasilAudits',
            'tahun',
            'unitKerja',
            'auditorFilter',
            'years',
            'unitKerjas',
            'auditors'
        ));
    }

    /**
     * Show hasil audit detail (for Admin to print)
     */
    public function hasilAuditShow($id)
    {
        $active = 23;
        $hasilAudit = HasilAudit::with(['peta.kegiatan', 'auditor'])->findOrFail($id);
        $peta = $hasilAudit->peta;

        // Calculate score
        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // LEVEL
        if ($skorTotal >= 20) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 15) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
            $badgeClass = 'bg-warning text-dark';
        } else {
            $levelText = 'LOW';
            $badgeClass = 'bg-success text-white';
        }

        // RESIDUAL
        if ($skorTotal >= 20) {
            $residualText = 'Extreme';
            $residualClass = 'bg-danger text-white';
        } elseif ($skorTotal >= 15) {
            $residualText = 'High';
            $residualClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $residualText = 'Moderate';
            $residualClass = 'bg-info text-dark';
        } else {
            $residualText = 'Low';
            $residualClass = 'bg-success text-white';
        }

        return view('manajemen_risiko.hasil_audit.show', compact(
            'active',
            'hasilAudit',
            'peta',
            'skorTotal',
            'levelText',
            'badgeClass',
            'residualText',
            'residualClass'
        ));
    }

    /**
     * Print hasil audit (PDF)
     */
    public function hasilAuditPrint($id)
    {
        $hasilAudit = HasilAudit::with(['peta.kegiatan', 'auditor'])->findOrFail($id);
        $peta = $hasilAudit->peta;
        $user = $hasilAudit->auditor;

        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // LEVEL
        if ($skorTotal >= 20) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 15) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
            $badgeClass = 'bg-warning text-dark';
        } else {
            $levelText = 'LOW';
            $badgeClass = 'bg-success text-white';
        }

        // RESIDUAL
        if ($skorTotal >= 20) {
            $residualText = 'Extreme';
            $residualClass = 'bg-danger text-white';
        } elseif ($skorTotal >= 15) {
            $residualText = 'High';
            $residualClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $residualText = 'Moderate';
            $residualClass = 'bg-info text-dark';
        } else {
            $residualText = 'Low';
            $residualClass = 'bg-success text-white';
        }

        $pdf = Pdf::loadView('manajemen_risiko.export_audit_pdf', compact(
            'peta',
            'hasilAudit',
            'user',
            'skorTotal',
            'levelText',
            'badgeClass',
            'residualText',
            'residualClass'
        ));

        $filename = 'Hasil_Audit_' . $peta->kode_regist . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Upload scan hasil audit (untuk admin)
     */
    public function uploadScanHasilAudit(Request $request, $id)
    {
        $request->validate([
            'file_scan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'keterangan_scan' => 'nullable|string|max:500',
        ], [
            'file_scan.required' => 'File scan wajib diupload',
            'file_scan.mimes' => 'Format file harus JPG, PNG, atau PDF',
            'file_scan.max' => 'Ukuran file maksimal 5MB',
        ]);

        try {
            $hasilAudit = HasilAudit::with(['auditor', 'peta'])->findOrFail($id);

            // Hapus file scan lama jika ada
            if ($hasilAudit->file_scan) {
                Storage::delete('public/scan_hasil_audit/' . $hasilAudit->file_scan);
            }

            // Upload file scan baru
            if ($request->hasFile('file_scan')) {
                $file = $request->file('file_scan');
                $extension = $file->getClientOriginalExtension();
                $filename = 'scan_' . str_replace(['/', '\\'], '_', $hasilAudit->kode_risiko) . '_' . time() . '.' . $extension;

                // Simpan file ke storage
                $file->storeAs('public/scan_hasil_audit', $filename);

                // Update database
                $hasilAudit->update([
                    'file_scan' => $filename,
                    'keterangan_scan' => $request->keterangan_scan,
                    'tanggal_upload_scan' => now(),
                ]);

                Log::info('File scan berhasil diupload', [
                    'hasil_audit_id' => $hasilAudit->id,
                    'kode_risiko' => $hasilAudit->kode_risiko,
                    'filename' => $filename,
                    'uploaded_by' => Auth::user()->name,
                ]);

                // ✅ KIRIM EMAIL NOTIFIKASI
                $uploader = Auth::user();
                $filePath = 'public/scan_hasil_audit/' . $filename;

                Log::info('🚀 Memulai pengiriman email...', [
                    'file_path' => $filePath,
                    'file_exists' => Storage::exists($filePath)
                ]);

                // ✅ HANYA KIRIM KE gilangb256@gmail.com
                try {
                    Mail::to('gilangb256@gmail.com')
                        ->send(new \App\Mail\ScanHasilAuditUploaded($hasilAudit, $uploader, $filePath));

                    Log::info('✅ Email berhasil dikirim ke gilangb256@gmail.com');
                } catch (\Exception $e) {
                    Log::error('❌ Error kirim email: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                }

                return redirect()
                    ->route('manajemen-risiko.hasil-audit.index')
                    ->with('success', '✅ File scan berhasil diupload untuk ' . $hasilAudit->kode_risiko . '! Email notifikasi telah dikirim ke gilangb256@gmail.com');
            }

            return redirect()->back()->with('error', 'File scan tidak ditemukan!');
        } catch (\Exception $e) {
            Log::error('❌ Error upload scan hasil audit: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat upload file: ' . $e->getMessage());
        }
    }
}
