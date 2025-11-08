<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\TemplateDokumen;
use Illuminate\Http\Request;
use SebastianBergmann\Template\Template;

class TemplateDokumenController extends Controller
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
        $jenisDokumen = JenisKegiatan::with('templateDokumen')->get();
        // dd($jenisDokumen);
        return view('templateDokumen.index', compact('active', 'jenisDokumen'));
    }

    public function search(Request $request)
    {
        $active = 19;
        $search = $request->input('search');
        $templateDokumens = TemplateDokumen::where('judul', 'like', '%' . $search . '%')
            ->orWhere('jenis', 'like', '%' . $search . '%')->with('jenisKegiatan')
            ->paginate(10);
        $jenisDokumen = JenisKegiatan::all();


        return view('templateDokumen.index', compact('active', 'templateDokumens', 'jenisDokumen'));
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
        // dd($request);
        //
        $this->validate($request, [
            'judul' => 'required|string|max:255',
            'jenis' => 'required|numeric',
            'dokumen' => 'required',
        ]);

        TemplateDokumen::create([
            'judul' => $request->judul,
            'id_jenis' => $request->jenis,
            'dokumen' => $request->dokumen
        ]);

        $kegiatan = JenisKegiatan::where('id', $request->jenis)->first();
        $template = TemplateDokumen::latest()->first();
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;
        if ($request->hasFile('dokumen')) {
            $jenisDokumen = $request->judul;
            $extension = $request->file('dokumen')->getClientOriginalExtension();
            if ($request->jenis == 3) {
                $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            } else {
                $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '_Kegiatan ' . $kegiatan->jenis . '.' . $extension;
            }
            $request->file('dokumen')->move('template_dokumen/', $newFileName);
            $template->dokumen = $newFileName;
            $template->save();
        }

        return redirect()->route('template-dokumen.index')->with('success', 'Template Dokumen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TemplateDokumen  $templateDokumen
     * @return \Illuminate\Http\Response
     */
    public function show(TemplateDokumen $templateDokumen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TemplateDokumen  $templateDokumen
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateDokumen $templateDokumen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\TemplateDokumen  $templateDokumen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $templateDokumen = TemplateDokumen::find($id);

        $kegiatan = JenisKegiatan::where('id', $templateDokumen->id_jenis)->first();
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;
        if ($request->hasFile('dokumen')) {
            $jenisDokumen = $templateDokumen->judul;
            $extension = $request->file('dokumen')->getClientOriginalExtension();
            if ($templateDokumen->id_jenis == 3) {
                $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            } else {
                $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '_Kegiatan ' . $kegiatan->jenis . '.' . $extension;
            }
            $request->file('dokumen')->move('template_dokumen/', $newFileName);
            $templateDokumen->dokumen = $newFileName;
        }

        $templateDokumen->save();

        return redirect()->route('template-dokumen.index')->with('success', 'Template Dokumen berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $templateDokumen = TemplateDokumen::findOrFail($id);
        $templateDokumen->delete();

        return redirect()->route('template-dokumen.index')->with('success', 'Template Dokumen berhasil dihapus.');
    }
}
