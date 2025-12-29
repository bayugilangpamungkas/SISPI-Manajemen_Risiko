<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peta;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;

class ManajemenRisikoController extends Controller
{
    /**
     * Display clustering data peta risiko
     */
    public function index(Request $request)
    {
        $active = 21;

        // Filter tahun
        $tahun = $request->input('tahun', date('Y'));

        // Clustering berdasarkan Unit Kerja
        $clusteringByUnit = Peta::select(
            'jenis',
            DB::raw('COUNT(*) as total_risiko'),
            DB::raw('AVG(skor_kemungkinan * skor_dampak) as rata_rata_skor'),
            DB::raw('MAX(skor_kemungkinan * skor_dampak) as skor_tertinggi'),
            DB::raw('MIN(skor_kemungkinan * skor_dampak) as skor_terendah'),
            DB::raw('SUM(CASE WHEN tingkat_risiko = "EXTREME" THEN 1 ELSE 0 END) as extreme'),
            DB::raw('SUM(CASE WHEN tingkat_risiko = "HIGH" THEN 1 ELSE 0 END) as high'),
            DB::raw('SUM(CASE WHEN tingkat_risiko = "MIDDLE" THEN 1 ELSE 0 END) as middle'),
            DB::raw('SUM(CASE WHEN tingkat_risiko = "LOW" THEN 1 ELSE 0 END) as low'),
            DB::raw('SUM(CASE WHEN tingkat_risiko = "VERY LOW" THEN 1 ELSE 0 END) as very_low')
        )
            ->whereYear('created_at', $tahun)
            ->groupBy('jenis')
            ->orderByDesc('skor_tertinggi')
            ->get();

        // Clustering berdasarkan Tingkat Risiko
        $clusteringByRisk = Peta::select(
            'tingkat_risiko',
            DB::raw('COUNT(*) as total'),
            DB::raw('AVG(skor_kemungkinan * skor_dampak) as rata_rata_skor')
        )
            ->whereYear('created_at', $tahun)
            ->groupBy('tingkat_risiko')
            ->orderByRaw("FIELD(tingkat_risiko, 'EXTREME', 'HIGH', 'MIDDLE', 'LOW', 'VERY LOW')")
            ->get();

        // Clustering berdasarkan Kategori Risiko
        $clusteringByCategory = Peta::select(
            'kategori',
            DB::raw('COUNT(*) as total'),
            DB::raw('AVG(skor_kemungkinan * skor_dampak) as rata_rata_skor')
        )
            ->whereYear('created_at', $tahun)
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        // Data untuk Chart - Top 10 Risiko Tertinggi
        $topRisks = Peta::selectRaw('kode_regist, judul, jenis, (skor_kemungkinan * skor_dampak) as skor_total, tingkat_risiko')
            ->whereYear('created_at', $tahun)
            ->orderByDesc('skor_total')
            ->limit(10)
            ->get();

        // Statistik Umum
        $totalRisiko = Peta::whereYear('created_at', $tahun)->count();
        $risikoExtreme = Peta::where('tingkat_risiko', 'EXTREME')->whereYear('created_at', $tahun)->count();
        $risikoHigh = Peta::where('tingkat_risiko', 'HIGH')->whereYear('created_at', $tahun)->count();
        $totalUnitKerja = Peta::whereYear('created_at', $tahun)->distinct('jenis')->count('jenis');

        // Tahun yang tersedia
        $years = Peta::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('manajemen-risiko.index', compact(
            'active',
            'clusteringByUnit',
            'clusteringByRisk',
            'clusteringByCategory',
            'topRisks',
            'totalRisiko',
            'risikoExtreme',
            'risikoHigh',
            'totalUnitKerja',
            'tahun',
            'years'
        ));
    }

    /**
     * Detail clustering per unit kerja
     */
    public function detailUnit($jenis, Request $request)
    {
        $active = 21;
        $tahun = $request->input('tahun', date('Y'));

        $risikos = Peta::where('jenis', $jenis)
            ->whereYear('created_at', $tahun)
            ->orderByDesc(DB::raw('skor_kemungkinan * skor_dampak'))
            ->get();

        return view('manajemen-risiko.detail-unit', compact('active', 'risikos', 'jenis', 'tahun'));
    }
}
