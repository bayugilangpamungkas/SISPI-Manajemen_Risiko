<?php

namespace App\Charts;

use App\Models\Post;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\Auth;

class TugasLaporChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\PieChart
    {
        $user = Auth::user();

        // Base query
        $query = Post::select('bidang', \DB::raw('count(*) as total'))
            ->groupBy('bidang');

        // Filter berdasarkan level user
        if ($user->id_level == 5) {
            $query->where('bidang', '(Reviu, Audit, Monev)')
                ->where('id_unit_kerja', $user->id_unit_kerja);
        } elseif (in_array($user->id_level, [3, 4, 6])) {
            $query->where(function ($q) use ($user) {
                $q->where('tanggungjawab', $user->name)
                    ->orWhereExists(function ($subquery) use ($user) {
                        $subquery->select(\DB::raw(1))
                            ->from('sertifikats')
                            ->whereColumn('sertifikats.id_post', 'posts.id')
                            ->where('sertifikats.id_user', $user->id);
                    });
            });
        }

        $postCounts = $query->get()
            ->pluck('total', 'bidang')
            ->toArray();

        // Jika tidak ada data, return chart kosong
        if (empty($postCounts)) {
            return $this->chart->pieChart()
                ->setTitle('Data Penugasan')
                ->setSubtitle('Data Penugasan SPI')
                ->addData([0])
                ->setLabels(['Tidak ada data']);
        }

        $labels = array_keys($postCounts);
        $data = array_values($postCounts);

        return $this->chart->pieChart()
            ->setTitle('Data Penugasan')
            ->setSubtitle('Data Penugasan SPI')
            ->addData($data)
            ->setLabels($labels);
    }
}
