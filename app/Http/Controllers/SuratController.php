<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\Peta;
use App\Models\HasilAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $active = 30; // Menu ID untuk Manajemen Surat

        // Filter
        $jenisSurat = $request->input('jenis_surat', 'all');
        $status = $request->input('status', 'all');
        $tahun = $request->input('tahun', date('Y'));

        // Build query
        $query = Surat::with(['creator', 'petaRisiko', 'hasilAudit'])
            ->whereYear('tanggal_surat', $tahun);

        if ($jenisSurat != 'all') {
            $query->where('jenis_surat', $jenisSurat);
        }

        if ($status != 'all') {
            $query->where('status', $status);
        }

        $surats = $query->orderBy('tanggal_surat', 'desc')
            ->paginate(15);

        // Get available years
        $years = Surat::selectRaw('YEAR(tanggal_surat) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($years->isEmpty()) {
            $currentYear = date('Y');
            $years = collect(range($currentYear - 4, $currentYear))->reverse()->values();
        }

        // Statistics
        $statistics = [
            'total' => Surat::whereYear('tanggal_surat', $tahun)->count(),
            'draft' => Surat::whereYear('tanggal_surat', $tahun)->where('status', 'Draft')->count(),
            'final' => Surat::whereYear('tanggal_surat', $tahun)->where('status', 'Final')->count(),
        ];

        return view('surat.index', compact('active', 'surats', 'jenisSurat', 'status', 'tahun', 'years', 'statistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $active = 30;

        // Get data Unit Kerja
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit_kerja', 'asc')->get();

        // Get data untuk referensi
        $petaRisikos = Peta::where('tampil_manajemen_risiko', 1)
            ->orderBy('kode_regist', 'desc')
            ->get();

        $hasilAudits = HasilAudit::orderBy('created_at', 'desc')
            ->get();

        return view('surat.create', compact('active', 'unitKerjas', 'petaRisikos', 'hasilAudits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|string|unique:surats,nomor_surat',
            'jenis_surat' => 'required|in:Pemberitahuan,Undangan,Permohonan,Lainnya',
            'tujuan_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tipe_referensi' => 'required|in:Tanpa Referensi,Peta Risiko,Audit',
        ]);

        try {
            DB::beginTransaction();

            // Tentukan referensi_id berdasarkan tipe referensi
            $referensiId = null;
            if ($request->tipe_referensi == 'Peta Risiko') {
                $referensiId = $request->referensi_id_peta;
            } elseif ($request->tipe_referensi == 'Audit') {
                $referensiId = $request->referensi_id_audit;
            }

            // Create surat
            $surat = Surat::create([
                'nomor_surat' => $request->nomor_surat,
                'jenis_surat' => $request->jenis_surat,
                'tujuan_surat' => $request->tujuan_surat,
                'perihal' => $request->perihal,
                'isi_surat' => $request->isi_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'tipe_referensi' => $request->tipe_referensi,
                'referensi_id' => $referensiId,
                'status' => 'Draft',
                'created_by' => Auth::id(),
            ]);

            // Generate PDF
            $this->generatePDF($surat);

            DB::commit();

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil dibuat! File PDF telah di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $active = 30;
        $surat = Surat::with(['creator', 'petaRisiko', 'hasilAudit'])->findOrFail($id);

        return view('surat.show', compact('active', 'surat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $active = 30;
        $surat = Surat::findOrFail($id);

        // Get data Unit Kerja
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit_kerja', 'asc')->get();

        // Get data untuk referensi
        $petaRisikos = Peta::where('tampil_manajemen_risiko', 1)
            ->orderBy('kode_regist', 'desc')
            ->get();

        $hasilAudits = HasilAudit::orderBy('created_at', 'desc')
            ->get();

        return view('surat.edit', compact('active', 'surat', 'unitKerjas', 'petaRisikos', 'hasilAudits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);

        $request->validate([
            'nomor_surat' => 'required|string|unique:surats,nomor_surat,' . $id,
            'jenis_surat' => 'required|in:Pemberitahuan,Undangan,Permohonan,Lainnya',
            'tujuan_surat' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'isi_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'tipe_referensi' => 'required|in:Tanpa Referensi,Peta Risiko,Audit',
        ]);

        try {
            DB::beginTransaction();

            // Tentukan referensi_id berdasarkan tipe referensi
            $referensiId = null;
            if ($request->tipe_referensi == 'Peta Risiko') {
                $referensiId = $request->referensi_id_peta;
            } elseif ($request->tipe_referensi == 'Audit') {
                $referensiId = $request->referensi_id_audit;
            }

            // Update surat
            $surat->update([
                'nomor_surat' => $request->nomor_surat,
                'jenis_surat' => $request->jenis_surat,
                'tujuan_surat' => $request->tujuan_surat,
                'perihal' => $request->perihal,
                'isi_surat' => $request->isi_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'tipe_referensi' => $request->tipe_referensi,
                'referensi_id' => $referensiId,
            ]);

            // Re-generate PDF
            $this->generatePDF($surat);

            DB::commit();

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil diperbarui! File PDF telah di-generate ulang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $surat = Surat::findOrFail($id);

            // Delete PDF file
            if ($surat->file_pdf && Storage::exists('public/surat_pdf/' . $surat->file_pdf)) {
                Storage::delete('public/surat_pdf/' . $surat->file_pdf);
            }

            $surat->delete();

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Finalize surat (change status to Final)
     */
    public function finalize($id)
    {
        try {
            $surat = Surat::findOrFail($id);

            $surat->update(['status' => 'Final']);

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil difinalisasi!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF surat
     */
    public function downloadPDF($id)
    {
        $surat = Surat::findOrFail($id);

        if (!$surat->file_pdf || !Storage::exists('public/surat_pdf/' . $surat->file_pdf)) {
            // Generate PDF jika belum ada
            $this->generatePDF($surat);
        }

        $filePath = storage_path('app/public/surat_pdf/' . $surat->file_pdf);

        return response()->download($filePath, $surat->file_pdf);
    }

    /**
     * Generate PDF dari data surat
     */
    private function generatePDF($surat)
    {
        // Load surat dengan relasi
        $surat->load(['creator', 'petaRisiko', 'hasilAudit']);

        // Generate PDF
        $pdf = Pdf::loadView('surat.pdf_template', compact('surat'));

        // Save PDF
        $filename = 'Surat_' . str_replace(['/', ' '], '_', $surat->nomor_surat) . '.pdf';

        // Create directory if not exists
        if (!Storage::exists('public/surat_pdf')) {
            Storage::makeDirectory('public/surat_pdf');
        }

        $pdf->save(storage_path('app/public/surat_pdf/' . $filename));

        // Update file_pdf di database
        $surat->update(['file_pdf' => $filename]);

        return $filename;
    }
}
