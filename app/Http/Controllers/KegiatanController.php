<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Http\Requests\StoreKegiatanRequest;
use App\Http\Requests\UpdateKegiatanRequest;
use App\Imports\KegiatanImport;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $active = 5;
        $query = Kegiatan::query();
        $unitKerjas = UnitKerja::all();

        // Filter berdasarkan judul jika ada search
        if ($request->has('search')) {
            $query->where('judul', 'LIKE', "%{$request->search}%");
        }

        // Filter berdasarkan tahun
        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $kegiatans = $query->paginate(10);
        if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2) {
            $kegiatans = $query->paginate(10);
        } else {
            $kegiatans = $query->where('id_unit_kerja', auth()->user()->id_unit_kerja)->paginate(10);
        }

        return view('kegiatan.index', compact('active', 'kegiatans', 'unitKerjas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreKegiatanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'id_unit_kerja' => 'required|exists:unit_kerjas,id',
            'judul' => 'required|string',
            'iku' => 'required|string',
            'sasaran' => 'required|string',
            'proker' => 'required|string',
            'indikator' => 'required|string',
            'anggaran' => 'required|string',
        ]);

        Kegiatan::create([
            'id_unit_kerja' => $request->id_unit_kerja,
            'judul' => $request->judul,
            'iku' => $request->iku,
            'sasaran' => $request->sasaran,
            'proker' => $request->proker,
            'indikator' => $request->indikator,
            'anggaran' => $request->anggaran,
        ]);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        return response()->json([
            'iku' => $kegiatan->iku,
            'sasaran' => $kegiatan->sasaran,
            'proker' => $kegiatan->proker,
            'indikator' => $kegiatan->indikator,
            'anggaran' => $kegiatan->anggaran,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\Response
     */
    public function edit(Kegiatan $kegiatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateKegiatanRequest  $request
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $kegiatan = Kegiatan::find($id);

        $kegiatan->update([
            'id_unit_kerja' => $request->id_unit_kerja,
            'judul' => $request->judul,
            'iku' => $request->iku,
            'sasaran' => $request->sasaran,
            'proker' => $request->proker,
            'indikator' => $request->indikator,
            'anggaran' => $request->anggaran,
        ]);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kegiatan $kegiatan)
    {
        //
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new KegiatanImport, $request->file('file'));

            if (session()->has('import_details')) {
                $details = session('import_details');
                return redirect()->back()
                    ->with('success', $details['success'])
                    ->with('warning', $details['warning'])
                    ->with('skipped_details', $details['skipped_details']);
            }

            return redirect()->back()->with('success', 'Semua data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteByYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        try {
            // Query to delete records for specific year
            $deleted = Kegiatan::whereYear('created_at', $request->year);

            // If user is not admin/supervisor, only delete from their unit
            if (Auth::user()->id_level != 1 && Auth::user()->id_level != 2) {
                $deleted = $deleted->where('id_unit_kerja', auth()->user()->id_unit_kerja);
            }

            $count = $deleted->count();
            $deleted->delete();

            return redirect()
                ->route('kegiatan.index')
                ->with('success', "Berhasil menghapus $count kegiatan dari tahun {$request->year}");
        } catch (\Exception $e) {
            return redirect()
                ->route('kegiatan.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
