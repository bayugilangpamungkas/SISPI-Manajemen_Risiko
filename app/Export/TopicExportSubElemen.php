<?php

namespace App\Exports;

use App\Models\Jawaban;
use App\Models\Simpulan;
use App\Models\ManagementSubElement;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TopicExportSubElemen implements FromArray, WithHeadings
{
    protected $sub_element_id;

public function __construct($sub_element_id)
{
    $this->sub_element_id = $sub_element_id;
}

public function array(): array
{
    $subElement = ManagementSubElement::with('ManagementTopic.Uraian')->find($this->sub_element_id);

    $data = [];
    $previousTopic = null; 
    $previousSimpulan = null; 
    $previousImprovement = null; 

    if ($subElement && $subElement->ManagementTopic) {
        foreach ($subElement->ManagementTopic as $topic) {
            $levels = $topic->Uraian->pluck('level')->unique()->sort();

            $uraianByLevel = [];
            foreach ($topic->Uraian as $uraian) {
                $uraianByLevel[$uraian->level][] = $uraian;
            }

            $simpulan = Simpulan::where('id_management_topic', $topic->id)->first();
            $simpulanText = $simpulan ? $simpulan->simpulan : '';
            $improvementText = $simpulan ? $simpulan->improvement : '';

            foreach ($uraianByLevel as $level => $uraians) {
                foreach ($uraians as $uraian) {
                    $row = [];

                    if ($previousTopic === $topic->topik) {
                        $row['Topic'] = '';
                    } else {
                        $row['Topic'] = $topic->topik;
                        $previousTopic = $topic->topik; 
                    }

                    foreach ($levels as $lvl) {
                        if (isset($uraianByLevel[$lvl]) && in_array($uraian, $uraianByLevel[$lvl])) {
                            $row['Level ' . $lvl] = $uraian->uraian;
                        } else {
                            $row['Level ' . $lvl] = '';
                        }

                        $row['Jawaban ' . $lvl] = '';
                    }

                    $jawaban = Jawaban::where('id_user', auth()->user()->id)
                        ->where('id_management_uraian', $uraian->id)
                        ->first();

                    if ($jawaban) {
                        $row['Jawaban ' . $uraian->level] = $jawaban->status == 1 ? 'Y' : 'T';
                    } else {
                        $row['Jawaban ' . $uraian->level] = 'Belum Dijawab';
                    }

                    if ($previousSimpulan === $simpulanText && $previousImprovement === $improvementText) {
                        $row['Simpulan'] = '';
                        $row['Improvement'] = '';
                    } else {
                        $row['Simpulan'] = $simpulanText;
                        $row['Improvement'] = $improvementText;
                        $previousSimpulan = $simpulanText; 
                        $previousImprovement = $improvementText; 
                    }

                    $data[] = $row;
                }
            }
        }
    }

    return $data;
}

public function headings(): array
{
    $subElement = ManagementSubElement::with('ManagementTopic.Uraian')->find($this->sub_element_id);

    $levels = [];
    if ($subElement) {
        foreach ($subElement->ManagementTopic as $topic) {
            $levels = array_merge($levels, $topic->Uraian->pluck('level')->unique()->toArray());
        }
    }
    $levels = array_unique($levels); 
    $headers = ['Topik']; 

    foreach ($levels as $level) {
        $headers[] = 'Level ' . $level ;
        $headers[] = ' ';
    }

    $headers[] = 'Simpulan';
    $headers[] = 'Improvement';

    return $headers;
}

}
