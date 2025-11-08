<?php

namespace App\Http\Controllers;
use App\Models\ManagementElement;
use App\Models\Jawaban;

use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $active = 18;
        // Retrieve the 'tahun' from the request, default to null if not provided
        $tahun = $request->input('tahun');

        // Validate the 'tahun' input
        if ($tahun) {
            $request->validate([
                'tahun' => 'integer|min:1900|max:' . date('Y'),
            ]);
        }

        // Eager load relationships with conditional filtering on 'Jawaban' based on 'tahun'
        $elements = ManagementElement::with([
            'ManagementSubElement.ManagementTopic.Uraian.Jawaban' => function ($query) use ($tahun) {
                if ($tahun) {
                    $query->where('tahun', $tahun);
                }
            }
        ])->get();

        // Return the view with the data and the selected 'tahun'
        return view('tataKelola.validasi', compact('active', 'elements', 'tahun'));
    }

    // Add the verify method to handle verification requests
    public function verifyJawaban($id)
    {
        $jawaban = Jawaban::findOrFail($id);
        $jawaban->validasi_at = now();
        $jawaban->save();

        return redirect()->back()->with('success', 'Jawaban berhasil diverifikasi.');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function verify($id)
{
    $jawaban = Jawaban::find($id);

    if ($jawaban) {
        $jawaban->validasi_at = now(); // Simpan waktu dan tanggal saat verifikasi
      
        $jawaban->save();

        return redirect()->back()->with('success', 'Verifikasi berhasil.');
    }

    return redirect()->back()->withErrors('Jawaban tidak ditemukan.');
}
}
