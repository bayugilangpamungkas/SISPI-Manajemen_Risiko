<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Jawaban;
use App\Models\Simpulan;
use App\Models\ManagementTopic;

class TopicExport implements FromArray, WithHeadings
{
    protected $topic_id;

    public function __construct($topic_id)
    {
        $this->topic_id = $topic_id;
    }

    public function array(): array
    {
        $topic = ManagementTopic::with('Uraian')->find($this->topic_id);

        $data = [];

        if ($topic && $topic->Uraian) {
            $levels = $topic->Uraian->pluck('level')->unique()->sort(); 

            $uraianByLevel = [];
            foreach ($topic->Uraian as $uraian) {
                $uraianByLevel[$uraian->level][] = $uraian;
            }

            $simpulan = Simpulan::where('id_management_topic', $this->topic_id)->first();
            $simpulanText = $simpulan ? $simpulan->simpulan : '-';
            $improvementText = $simpulan ? $simpulan->improvement : '-';

            foreach ($uraianByLevel as $level => $uraians) {
                foreach ($uraians as $uraian) {
                    $row = [];
                    
                    if (empty($data)) {
                        $row['Topic'] = $topic->topik;
                    } else {
                        $row['Topic'] = ''; 
                    }

                    foreach ($levels as $lvl) {
                        $row['Level ' . $lvl] = isset($uraianByLevel[$lvl]) && in_array($uraian, $uraianByLevel[$lvl]) ? $uraian->uraian : '';
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

                    if (empty($data)) {
                        $row['Simpulan'] = $simpulanText;
                        $row['Improvement'] = $improvementText;
                    } else {
                        $row['Simpulan'] = ''; 
                        $row['Improvement'] = ''; 
                    }

                    $data[] = $row;
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $topic = ManagementTopic::with('Uraian')->find($this->topic_id);

        $levels = $topic ? $topic->Uraian->pluck('level')->unique()->sort() : []; 
        $headers = ['Topic']; 

        foreach ($levels as $level) {
            $headers[] = 'Level ' . $level;
            $headers[] = '';
        }

        $headers = array_merge($headers, ['Simpulan', 'Improvement']);

        return $headers;
    }
}
