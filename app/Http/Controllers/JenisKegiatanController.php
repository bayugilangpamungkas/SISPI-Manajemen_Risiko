<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JenisKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $active = 19;
        $jenisKegiatan = JenisKegiatan::latest()->paginate(5); 
        return view('jenisKegiatan.index', compact('active','jenisKegiatan'));
    }

    public function search(Request $request)
    {
        $active = 19;
        $search = $request->input('search');
        $jenis = JenisKegiatan::Where('jenis', 'like', '%' . $search . '%')
            ->paginate(10);

        return view('jenisKegiatan.index', compact('active', 'jenis'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'jenis' => 'required|string|max:255',
        ]);

        JenisKegiatan::create([
            'jenis' => $request->jenis,
        ]);
        return redirect()->route('jenis-template.index')->with('success', 'Jenis Kegiatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JenisKegiatan  $jenisKegiatan
     * @return \Illuminate\Http\Response
     */
    public function show(JenisKegiatan $jenisKegiatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JenisKegiatan  $jenisKegiatan
     * @return \Illuminate\Http\Response
     */
    public function edit(JenisKegiatan $jenisKegiatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\JenisKegiatan  $jenisKegiatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JenisKegiatan $jenisKegiatan)
    {
        //
        $this->validate($request, [
            'jenis' => 'required|string|max:255',
        ]);

        $jenisKegiatan->update([
            'jenis' => $request->jenis,
        ]);
        return redirect()->route('jenis-template.index')->with('success', 'Jenis Kegiatan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JenisKegiatan  $jenisKegiatan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $jenisKegiatan = JenisKegiatan::findOrFail($id);
        $jenisKegiatan->delete();
        return redirect()->route('jenis-template.index')->with('success', 'Jenis Kegiatan berhasil dihapus.');
    }
}
