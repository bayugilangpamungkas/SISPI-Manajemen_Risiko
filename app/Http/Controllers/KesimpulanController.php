<?php

namespace App\Http\Controllers;

use App\Models\ManagementElement;
use App\Models\Uraian;
use App\Models\Jawaban;

use Illuminate\Http\Request;

class KesimpulanController extends Controller
{
    public function index(Request $request)
    {
        $active = 18;
        $tahun = $request->input('tahun');

        $elements = ManagementElement::with([
            'ManagementSubElement.ManagementTopic.Uraian.Jawaban' => function ($query) use ($tahun) {
                if ($tahun) {
                    $query->where('tahun', $tahun);
                }
            },
            'ManagementSubElement.ManagementTopic.Simpulan' => function ($query) use ($tahun) {
                if ($tahun) {
                    $query->where('tahun', $tahun);
                }
            }
        ])->get();

        $processedElements = [];

        $maxLevels = $elements->flatMap(function ($element) {
            return $element->ManagementSubElement->flatMap(function ($subElement) {
                return $subElement->ManagementTopic->flatMap(function ($topic) {
                    return $topic->Uraian->pluck('level')->unique();
                });
            });
        })->max();

        foreach ($elements as $element) {
            $elementScore = 0;
            $subElementsData = [];

            foreach ($element->ManagementSubElement as $subElement) {
                $subElementScore = 0;
                $topicData = [];

                foreach ($subElement->ManagementTopic as $topic) {
                    $score = 0;
                    $topicLevels = [];

                    $levels = $topic->Uraian->groupBy('level');

                    foreach (range(1, $maxLevels) as $i) {
                        if (isset($levels[$i])) {
                            $uraianIds = $levels[$i]->pluck('id');

                            $jawabanQuery = Jawaban::whereIn('id_management_uraian', $uraianIds);
                            if ($tahun) {
                                $jawabanQuery->where('tahun', $tahun);
                            }
                            $jawabanCount = $jawabanQuery->count();

                            $allStatusTrue = $jawabanCount > 0 && Jawaban::whereIn('id_management_uraian', $uraianIds)
                                ->when($tahun, function ($query) use ($tahun) {
                                    return $query->where('tahun', $tahun);
                                })
                                ->pluck('status')
                                ->every(fn($status) => $status == 1);

                            $topicLevels[$i] = $allStatusTrue ? 'Y' : 'T';
                            $score += $allStatusTrue ? 1 : 0;
                        } else {
                            $topicLevels[$i] = 'T';
                        }
                    }

                    // Get simpulan and improvement from related Simpulan model
                    $simpulanData = optional($topic->Simpulan)->first();
                    $simpulan = $simpulanData ? $simpulanData->simpulan : 'N/A';
                    $improvement = $simpulanData ? $simpulanData->improvement : 'N/A';

                    $topicData[] = [
                        'topik' => $topic->topik,
                        'levels' => $topicLevels,
                        'skor' => $score,
                        'simpulan' => $simpulan,
                        'improvement' => $improvement,
                    ];

                    $subElementScore += $score;
                }

                $bobotElemen = $element->bobot_elemen;
                $bobotSubElemen = $subElement->bobot_sub_elemen;

                $simpulanLevelElemen = count($topicData) > 0 ? $subElementScore / count($topicData) : 0;
                $skorSubElemen = $simpulanLevelElemen * ($bobotElemen / 100) * ($bobotSubElemen / 100);

                $elementScore += $skorSubElemen;

                $subElementsData[] = [
                    'sub_elemen' => $subElement->sub_elemen,
                    'topics' => $topicData,
                    'simpulan_level' => number_format($simpulanLevelElemen, 2),
                    'skor_sub_elemen' => number_format($skorSubElemen, 2),
                ];
            }

            $processedElements[] = [
                'elemen' => $element->elemen,
                'sub_elements' => $subElementsData,
                'total_skor_elemen' => number_format($elementScore, 2),
            ];
        }

        return view('tataKelola.kesimpulan', compact('active', 'processedElements', 'maxLevels', 'tahun'));
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
}
