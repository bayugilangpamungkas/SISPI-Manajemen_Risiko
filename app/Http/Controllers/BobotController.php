<?php

namespace App\Http\Controllers;
use App\Models\ManagementElement;
use App\Models\ManagementSubElement;

use Illuminate\Http\Request;

class BobotController extends Controller
{

    public function index()
    {
        $active = 18;
        $elements = ManagementElement::with([
            'ManagementSubElement.ManagementTopic.Uraian.Jawaban' 
        ])->get();
        foreach ($elements as $element) {
            foreach ($element->ManagementSubElement as $subElement) {
                foreach ($subElement->ManagementTopic as $topic) {
                    $maxLevels = $topic->Uraian->pluck('level')->unique()->count();
                    $totalScore = 0;
                    $levelCount = 0;
    
                    for ($i = 1; $i <= $maxLevels; $i++) {
                        $uraianIds = $topic->Uraian->where('level', $i)->pluck('id');
                        $jawabanCount = \App\Models\Jawaban::whereIn('id_management_uraian', $uraianIds)->count();
    
                        if ($jawabanCount > 0) {
                            $allStatusTrue = \App\Models\Jawaban::whereIn('id_management_uraian', $uraianIds)
                                ->pluck('status')
                                ->every(function ($status) {
                                    return $status == 1;
                                });
                            if ($allStatusTrue) {
                                $totalScore++;
                            }
                            $levelCount++;
                        }
                    }
                    $topic->average_score = $levelCount > 0 ? $totalScore / $levelCount : 0;
                }
            }
        }
        return view('tataKelola.bobot', compact('active', 'elements'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $totalBobotElement = array_sum($request->input('bobot_elemen', []));
        $totalBobotSubElement = [];
        
        foreach ($request->input('bobot_sub_elemen', []) as $subElementId => $bobotSub) {
            $subElement = ManagementSubElement::find($subElementId);
            $elementId = $subElement->id_management_element;
            
            if (!isset($totalBobotSubElement[$elementId])) {
                $totalBobotSubElement[$elementId] = 0;
            }
            $totalBobotSubElement[$elementId] += $bobotSub;
        }

        if ($totalBobotElement > 100) {
            return redirect()->back()->withErrors(['msg' => 'Gagal menyimpan. Total bobot elemen melebihi 100%.']);
        }

        foreach ($totalBobotSubElement as $elementId => $totalBobotSub) {
            if ($totalBobotSub > 100) {
                return redirect()->back()->withErrors(['msg' => 'Gagal menyimpan. Total bobot sub elemen pada elemen melebihi 100%.']);
            }
        }

        foreach ($request->input('bobot_elemen', []) as $elementId => $bobotElemen) {
            $element = ManagementElement::find($elementId);
            $element->bobot_elemen = $bobotElemen;
            $element->save();
        }
        foreach ($request->input('bobot_sub_elemen', []) as $subElementId => $bobotSub) {
            $subElement = ManagementSubElement::find($subElementId);
            $subElement->bobot_sub_elemen = $bobotSub;
            $subElement->save();
        }
        return redirect()->back()->with('success', 'Bobot berhasil diedit.');
    }
    
    public function destroy($id)
    {
        //
    }
}
