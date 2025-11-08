<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{

    public function index()
    {
        $active = 17;
        $unitKerjas = UnitKerja::paginate(10);
        return view('unit.unit-kerja', compact('active', 'unitKerjas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit_kerja' => 'required|string|max:255',
        ]);

        UnitKerja::create([
            'nama_unit_kerja' => $request->nama_unit_kerja,
        ]);

        return redirect()->route('unit-kerja.index')->with('success', 'Unit Kerja berhasil ditmabahkan.');
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $request->validate([
            'nama_unit_kerja' => 'required|string|max:255',
        ]);

        $unitKerja->update([
            'nama_unit_kerja' => $request->nama_unit_kerja,
        ]);
        return redirect()->route('unit-kerja.index')->with('success', 'Unit Kerja berhasil diupdate.');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        $unitKerja->delete();
        return redirect()->route('unit-kerja.index')->with('success', 'Unit Kerja berhasil dihapus.');
    }
}
