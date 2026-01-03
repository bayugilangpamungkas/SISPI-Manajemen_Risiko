<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peta;
use App\Models\CommentPr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ManajemenRisikoController extends Controller
{
    /**
     * Display a listing of the resource with clustering
     */
    public function index(Request $request)
    {
        $active = 21;

        // Get filter parameters
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');

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

        // Build query with filters
        $query = Peta::with(['comment_prs.user'])
            ->whereYear('created_at', $tahun);

        // Filter by unit kerja
        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
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

        // Calculate statistics
        $statistics = [
            'total' => Peta::whereYear('created_at', $tahun)->count(),
            'high_risk' => Peta::whereYear('created_at', $tahun)
                ->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => Peta::whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => Peta::whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Low')->count(),
        ];

        return view('manajemen_risiko.index', compact(
            'active',
            'petas',
            'statistics',
            'cluster',
            'tahun',
            'unitKerja',
            'years',
            'unitKerjas'
        ));
    }

    /**
     * Display the specified resource
     */
    public function show($id)
    {
        $active = 21;
        $peta = Peta::with(['comment_prs.user', 'kegiatan'])->findOrFail($id);

        return view('manajemen_risiko.show', compact('active', 'peta'));
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
}
