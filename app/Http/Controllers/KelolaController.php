<?php

namespace App\Http\Controllers;
use App\Models\ManagementElement;
use App\Models\ManagementPengawasan;
use App\Models\Jawaban;
use App\Models\Simpulan;
use App\Models\JawabanKP;
use App\Models\Uraian;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TopicExport;
use App\Exports\TopicExportSubElemen;

use Illuminate\Http\Request;

class KelolaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $active = 18;
        $tahun = $request->input('tahun');
        if ($tahun) {
            $request->validate([
                'tahun' => 'integer|min:1900|max:' . date('Y'),
            ]);
        }

        $elements = ManagementElement::with([
            'ManagementSubElement.ManagementTopic.Uraian.Jawaban' => function ($query) use ($tahun) {
                if ($tahun) {
                    $query->where('tahun', $tahun);
                }
            }
        ])->get();

        return view('tataKelola.entitas', compact('active', 'elements', 'tahun'));
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
    public function jawabanStore(Request $request)
    {
        $jawabanData = $request->input('jawaban');
        $idUser = $request->input('id_user');
        $idUraian = $request->input('id_management_uraian');
        $tahun = $request->input('tahun'); 
        foreach ($jawabanData as $idManagementPengawasan => $data) {
            if (!empty($data['nilai'])) {
                $jawaban = JawabanKP::where('id_management_pengawasan', $idManagementPengawasan)
                ->where('id_user', $idUser)
                ->where('tahun', $tahun) 
                ->first();

            if ($jawaban) {
                $jawaban->nilai = $data['nilai'];
                $jawaban->evaluator = $data['evaluator'];
                $jawaban->tahun = $tahun;
                $jawaban->save();
            } else {
                JawabanKP::create([
                    'id_management_pengawasan' => $idManagementPengawasan,
                    'id_user' => $idUser,
                    'nilai' => $data['nilai'],
                    'evaluator' => $data['evaluator'],
                    'tahun' => $tahun, 
                    'status' => true, 
                ]);
            }
        }
    }

    $this->cekPengawasanDanSimpanJawaban($idUraian, $tahun); 
    return redirect()->back()->with('success', 'Jawaban berhasil disimpan atau diperbarui.');
}

public function cekPengawasanDanSimpanJawaban($uraianId, $tahun)
{
    $pengawasan = ManagementPengawasan::where('id_management_uraian', $uraianId)
    ->get();
    $semuaValid = $pengawasan->every(function($pengawasanItem) use ($tahun) {
        $jawabanKP = JawabanKP::where('id_management_pengawasan', $pengawasanItem->id)
                              ->where('id_user', auth()->user()->id)
                              ->where('tahun', $tahun) 
                              ->first();

        Log::info('Status KP untuk Pengawasan ID ' . $pengawasanItem->id . ' adalah: ' . ($jawabanKP ? $jawabanKP->status : 'Tidak ditemukan'));

        return $jawabanKP && $jawabanKP->status == 1;
    });

    $existingJawabanUraian = Jawaban::where('id_user', auth()->user()->id)
                                    ->where('id_management_uraian', $uraianId)
                                    ->where('tahun', $tahun) 
                                    ->first();

    if ($semuaValid) {
        if (!$existingJawabanUraian) {
            Log::info('Semua aktivitas pengawasan untuk uraian ID ' . $uraianId . ' valid. Menyimpan jawaban...');
            $jawabanUraian = new Jawaban;
            $jawabanUraian->id_user = auth()->user()->id;
            $jawabanUraian->id_management_uraian = $uraianId;
            $jawabanUraian->status = 1;
            $jawabanUraian->tahun = $tahun; 
            $jawabanUraian->save();
        } else {
            Log::info('Jawaban untuk uraian ID ' . $uraianId . ' sudah ada. Tidak perlu menyimpan status 1.');
        }
    }

    return back();
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = auth()->user();
        $tahunInput = $request->input('tahun'); 
        
        session(['tahun' => $tahunInput]);
        
        foreach ($request->input('jawaban', []) as $uraianId => $status) {
        $jawaban = Jawaban::where('id_user', $user->id)
            ->where('id_management_uraian', $uraianId)
            ->where('tahun', $tahunInput)
            ->first();

        if (!$jawaban) {
            $jawaban = new Jawaban();
            $jawaban->id_user = $user->id;
            $jawaban->id_management_uraian = $uraianId;
            $jawaban->tahun = $tahunInput; 
        }

        $jawaban->status = $status;

        if ($request->hasFile("berkas.$uraianId")) {
            $file = $request->file("berkas.$uraianId");
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');
            $jawaban->dokumen = str_replace('public/', '', $path);
        }

      
        $jawaban->save();
    }
    return redirect()->route('entitas.index')->with('success', 'Jawaban berhasil disimpan.');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   
    public function simpulanUpdate(Request $request)
    {
         
         $request->validate([
             'id_management_topic' => 'required|exists:uraians,id',
             'simpulan' => 'nullable|string',
             'improvement' => 'nullable|string',
             'tahun' => 'required|integer', 
         ]);
     
         $simpulanData = [
             'id_management_topic' => $request->input('id_management_topic'),
             'simpulan' => $request->input('simpulan'),
             'improvement' => $request->input('improvement'),
             'id_user' => auth()->user()->id, 
             'tahun' => $request->input('tahun'), 
         ];
     
         $simpulan = Simpulan::updateOrCreate(
             [
                 'id_management_topic' => $request->input('id_management_topic'),
                 'tahun' => $request->input('tahun') 
             ],
             $simpulanData
         );
     
         return redirect()->back()->with('success', 'Simpulan dan Improvement berhasil disimpan.');
     }
     
    public function exportExcel($topic_id)
    {
        return Excel::download(new TopicExport($topic_id), 'Topic-Jawaban.xlsx');
    }
    public function exportSubElemenExcel($sub_element_id)
    {
        return Excel::download(new TopicExportSubElemen($sub_element_id), 'SubElemen-Jawaban.xlsx');
    }

}
