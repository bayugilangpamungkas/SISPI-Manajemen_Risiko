<?php

namespace App\Http\Controllers;

use App\Models\RTM;
use App\Http\Requests\StoreRTMRequest;
use App\Http\Requests\UpdateRTMRequest;
use App\Models\PIC_RTM;
use App\Models\Post;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RTMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $active = 4;
        $query = Post::query();

        // Filter berdasarkan judul jika ada search
        if ($request->has('search')) {
            $query->where('judul', 'LIKE', "%{$request->search}%");
        }

        // Filter berdasarkan tahun
        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Ambil data post dengan relasi RTM
        $posts = $query->with('rtm.pic_rtm') // Eager load relasi RTM
            ->whereNotNull('dokumen_tindak_lanjut')
            ->latest()
            ->where('status_task', 'approved')
            ->paginate(10);

        $pic = Post::where('status_task', 'approved')->distinct('tanggungjawab')->get();

        // dd($posts);

        return view('posts.RTM', compact('active', 'posts', 'pic'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $active = 4;
        $unit_kerja = UnitKerja::all();
        if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2) {
            $post = Post::whereNotNull('dokumen_tindak_lanjut')->get();
        } else {
            $post = Post::whereNotNull('dokumen_tindak_lanjut')->where('tanggungjawab', auth()->user()->name)->get();
        }

        return view('posts.createRTM', compact('active', 'post', 'unit_kerja'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRTMRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'id_rtm' => 'sometimes',
            'id_post' => 'sometimes',
            'id_unit_kerja' => 'sometimes|array',
            'temuan' => 'sometimes|required|string',
            'rekomendasi' => 'sometimes|required|string',
            'rencanaTinJut' => 'sometimes|required|string',
            'rencanaWaktuTinJut' => 'sometimes|required|date',
            'status_rtm' => 'sometimes|required|string',
        ]);

        if ($request->has('id_rtm')) {
            $rtm = RTM::find($request->id_post);

            if ($request->has('id_post')) {
                $rtm->id_post = $request->id_post;
            }
            if ($request->has('temuan')) {
                $rtm->temuan = $request->temuan;
            }
            if ($request->has('rekomendasi')) {
                $rtm->rekomendasi = $request->rekomendasi;
            }
            if ($request->has('rencanaTinJut')) {
                $rtm->rencanaTinJut = $request->rencanaTinJut;
            }
            if ($request->has('rencanaWaktuTinJut')) {
                $rtm->rencanaWaktuTinJut = $request->rencanaWaktuTinJut;
            }
            if ($request->has('status_rtm')) {
                $rtm->status_rtm = $request->status_rtm;
            }
            if ($request->has('id_unit_kerja')) {
                $rtm->pic_rtm()->delete();
                foreach ($request->id_unit_kerja as $unit_kerja) {
                    $rtm->pic_rtm()->create([
                        'id_rtm' => $rtm->id,
                        'id_unit_kerja' => $unit_kerja,
                    ]);
                }
            }

            $rtm->save();
        } else {
            RTM::create([
                'id_post' => $request->id_post,
                'temuan' => $request->temuan,
                'rekomendasi' => $request->rekomendasi,
                'rencanaTinJut' => $request->rencanaTinJut,
                'rencanaWaktuTinJut' => $request->rencanaWaktuTinJut,
                'status_rtm' => $request->status_rtm,
            ]);

            $rtm = RTM::latest()->first();

            foreach ($request->id_unit_kerja as $unit_kerja) {
                PIC_RTM::create([
                    'id_rtm' => $rtm->id,
                    'id_unit_kerja' => $unit_kerja,
                ]);
            }
        }


        return redirect()->route('rtm');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RTM  $rTM
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $post = Post::with(['rtm.pic'])->findOrFail($id);

        return response()->json([
            'rtm' => $post->rtm->map(function ($rtm) {
                return [
                    'rekomendasi' => $rtm->rekomendasi,
                    'rencanaTinJut' => $rtm->rencanaTinJut,
                    'rencanaWaktuTinJut' => $rtm->rencanaWaktuTinJut,
                    'status' => $rtm->status,
                    'pic' => $rtm->pic_rtm
                ];
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RTM  $rTM
     * @return \Illuminate\Http\Response
     */
    public function edit(RTM $rTM)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRTMRequest  $request
     * @param  \App\Models\RTM  $rTM
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRTMRequest $request, RTM $rTM)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RTM  $rTM
     * @return \Illuminate\Http\Response
     */
    public function destroy(RTM $rTM)
    {
        //
    }

    public function exportExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul kolom
        $sheet->setCellValue('A1', 'NO.');
        $sheet->setCellValue('B1', 'KEGIATAN');
        $sheet->setCellValue('C1', 'REKOMENDASI');
        $sheet->setCellValue('D1', 'RENCANA TINDAK LANJUT');
        $sheet->setCellValue('D2', 'TINDAK LANJUT RENCANA PERBAIKAN');
        $sheet->setCellValue('E2', 'PIC');
        $sheet->setCellValue('F2', 'WAKTU (Bulan dan Tahun)');
        $sheet->setCellValue('G1', 'STATUS (Open/Closed/In Progress)');

        // Merge cells untuk header
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:F1');
        $sheet->mergeCells('G1:G2');

        // Set alignment untuk header
        $headerStyle = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $sheet->getStyle('A1:G2')->applyFromArray($headerStyle);

        // Style header
        $sheet->getStyle('A1:G2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2EFDA',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Get data with filters
        $query = RTM::with(['pic_rtm.unitKerja', 'post'])
            ->whereHas('post', function ($q) use ($request) {
                if ($request->tahun) {
                    $q->whereYear('tanggal', $request->tahun);
                }
                if ($request->search) {
                    $q->where('judul', 'like', '%' . $request->search . '%');
                }
            });

        $rtms = $query->get();

        // Group RTMs by post (kegiatan)
        $groupedRtms = $rtms->groupBy('post.judul');

        $row = 3;
        $kegiatanNo = 1;

        foreach ($groupedRtms as $kegiatan => $rtmGroup) {
            $startRow = $row;

            foreach ($rtmGroup as $rtm) {
                $pic_names = $rtm->pic_rtm->map(function ($pic) {
                    return $pic->unitKerja->nama_unit_kerja;
                })->join(', ');

                $sheet->setCellValue('A' . $row, $kegiatanNo);
                $sheet->setCellValue('B' . $row, $kegiatan);
                $sheet->setCellValue('C' . $row, $rtm->rekomendasi);
                $sheet->setCellValue('D' . $row, $rtm->rencanaTinJut);
                $sheet->setCellValue('E' . $row, $pic_names);
                // Format tanggal
                if ($rtm->rencanaWaktuTinJut) {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($rtm->rencanaWaktuTinJut);
                    $sheet->setCellValue('F' . $row, $date);
                    $sheet->getStyle('F' . $row)
                        ->getNumberFormat()
                        ->setFormatCode('dd-mm-yyyy');
                } else {
                    $sheet->setCellValue('F' . $row, '');
                }
                $sheet->setCellValue('G' . $row, $rtm->status_rtm);

                // Style untuk status
                $statusColor = match ($rtm->status_rtm) {
                    'Open' => '68B984',       // hijau
                    'In Progress' => 'FFB84C', // kuning
                    'Closed' => 'F16767',      // merah
                    default => 'FFFFFF'
                };

                $sheet->getStyle('G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $statusColor],
                    ]
                ]);

                // Auto wrap text
                $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
                $sheet->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

                $row++;
            }

            // Merge cells untuk kegiatan dan nomor
            if ($startRow < ($row - 1)) {
                $sheet->mergeCells('A' . $startRow . ':A' . ($row - 1));
                $sheet->mergeCells('B' . $startRow . ':B' . ($row - 1));
                // Set vertical alignment to center for merged cells
                $sheet->getStyle('A' . $startRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B' . $startRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

            $kegiatanNo++;
        }

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Set borders for all cells
        $lastRow = $row - 1;
        $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $writer = new Xlsx($spreadsheet);

        // Set filename dengan tahun jika ada
        $filename = 'RTM_Report';
        if ($request->tahun) {
            $filename .= '_' . $request->tahun;
        }
        $filename .= '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
