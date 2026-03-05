<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratController extends Controller
{
    /** Daftar jenis surat — satu sumber kebenaran, dipakai store & update */
    private const JENIS_SURAT = [
        'Surat Tugas',
        'Surat Pemberitahuan Audit',
        'Surat Permintaan Data',
        'Nota Dinas',
        'Undangan',
        'Laporan Hasil Audit',
        'Berita Acara',
        'Permohonan',
        'Lainnya',
    ];

    public function index(Request $request)
    {
        $active     = 30;
        $jenisSurat = $request->input('jenis_surat', 'all');
        $status     = $request->input('status', 'all');
        $tahun      = $request->input('tahun', date('Y'));

        $query = Surat::with('creator')->whereYear('tanggal_surat', $tahun);

        if ($jenisSurat !== 'all') {
            $query->where('jenis_surat', $jenisSurat);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $surats = $query->orderBy('tanggal_surat', 'asc')->paginate(15);

        $years = Surat::selectRaw('YEAR(tanggal_surat) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');

        if ($years->isEmpty()) {
            $currentYear = date('Y');
            $years = collect(range($currentYear - 4, $currentYear))->reverse()->values();
        }

        $statistics = [
            'total' => Surat::whereYear('tanggal_surat', $tahun)->count(),
            'draft' => Surat::whereYear('tanggal_surat', $tahun)->where('status', 'Draft')->count(),
            'final' => Surat::whereYear('tanggal_surat', $tahun)->where('status', 'Final')->count(),
        ];

        return view('surat.index', compact('active', 'surats', 'jenisSurat', 'status', 'tahun', 'years', 'statistics'));
    }

    public function create()
    {
        $active     = 30;
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit_kerja', 'asc')->get();

        return view('surat.create', compact('active', 'unitKerjas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat'   => 'required|string|unique:surats,nomor_surat',
            'jenis_surat'   => 'nullable|string',
            'tujuan_surat'  => 'nullable|string|max:255',
            'perihal'       => 'required|string|max:255',
            'lampiran'      => 'nullable|string|max:100',
            'isi_surat'     => 'required|string',
            'tanggal_surat' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $surat = Surat::create([
                'nomor_surat'   => $request->nomor_surat,
                'jenis_surat'   => $request->input('jenis_surat', 'Lainnya'),
                'tujuan_surat'  => $request->input('tujuan_surat', '-'),
                'perihal'       => $request->perihal,
                'lampiran'      => $request->input('lampiran', '-') ?: '-',
                'isi_surat'     => $request->isi_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'status'        => 'Draft',
                'created_by'    => Auth::id(),
            ]);

            $this->generatePDF($surat);

            DB::commit();

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil dibuat! File PDF telah di-generate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $active = 30;
        $surat  = Surat::with('creator')->findOrFail($id);

        return view('surat.show', compact('active', 'surat'));
    }

    public function edit($id)
    {
        $active     = 30;
        $surat      = Surat::findOrFail($id);
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama_unit_kerja', 'asc')->get();

        return view('surat.edit', compact('active', 'surat', 'unitKerjas'));
    }

    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);

        $request->validate([
            'nomor_surat'   => 'required|string|unique:surats,nomor_surat,' . $id,
            'jenis_surat'   => 'nullable|string',
            'tujuan_surat'  => 'nullable|string|max:255',
            'perihal'       => 'required|string|max:255',
            'lampiran'      => 'nullable|string|max:100',
            'isi_surat'     => 'required|string',
            'tanggal_surat' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $surat->update([
                'nomor_surat'   => $request->nomor_surat,
                'jenis_surat'   => $request->input('jenis_surat', $surat->jenis_surat ?? 'Lainnya'),
                'tujuan_surat'  => $request->input('tujuan_surat', $surat->tujuan_surat ?? '-'),
                'perihal'       => $request->perihal,
                'lampiran'      => $request->input('lampiran', '-') ?: '-',
                'isi_surat'     => $request->isi_surat,
                'tanggal_surat' => $request->tanggal_surat,
            ]);

            $this->generatePDF($surat);

            DB::commit();

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil diperbarui! File PDF telah di-generate ulang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $surat = Surat::findOrFail($id);

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

    public function finalize($id)
    {
        try {
            Surat::findOrFail($id)->update(['status' => 'Final']);

            return redirect()->route('surat.index')
                ->with('success', '✅ Surat berhasil difinalisasi!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadPDF($id)
    {
        $surat = Surat::findOrFail($id);

        if (!$surat->file_pdf || !Storage::exists('public/surat_pdf/' . $surat->file_pdf)) {
            $this->generatePDF($surat);
        }

        return response()->download(
            storage_path('app/public/surat_pdf/' . $surat->file_pdf),
            $surat->file_pdf
        );
    }

    public function printPDF($id)
    {
        $surat = Surat::findOrFail($id);

        if (!$surat->file_pdf || !Storage::exists('public/surat_pdf/' . $surat->file_pdf)) {
            $this->generatePDF($surat);
        }

        return response()->file(
            storage_path('app/public/surat_pdf/' . $surat->file_pdf),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $surat->file_pdf . '"',
            ]
        );
    }

    private function generatePDF($surat)
    {
        $surat->load('creator');

        $ketuaSPI = \App\Models\User::where('id_level', 3)->first();

        $pdf = Pdf::loadView('surat.pdf_template', compact('surat', 'ketuaSPI'));

        $filename = 'Surat_' . str_replace(['/', ' '], '_', $surat->nomor_surat) . '.pdf';

        if (!Storage::exists('public/surat_pdf')) {
            Storage::makeDirectory('public/surat_pdf');
        }

        $pdf->save(storage_path('app/public/surat_pdf/' . $filename));

        $surat->update(['file_pdf' => $filename]);

        return $filename;
    }
}
