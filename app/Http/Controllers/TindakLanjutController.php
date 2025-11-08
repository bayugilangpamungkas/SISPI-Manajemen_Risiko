<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\Post;
use App\Models\RTM;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TindakLanjutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $active = 3;
        $query = Post::query();

        // Filter berdasarkan judul jika ada search
        if ($request->has('search')) {
            $query->where('judul', 'LIKE', "%{$request->search}%");
        }

        // Filter berdasarkan tahun
        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $posts = $query->whereNotNull('dokumen_tindak_lanjut')
            ->latest()
            ->paginate(10);

        foreach ($posts as $post) {
            $namaKegiatan = JenisKegiatan::find($post->jenis)->jenis ?? '-';
            $post->jenis_kegiatan = $namaKegiatan;
        }

        $pic = Post::where('status_task', 'approved')->distinct('tanggungjawab')->get();
        return view('tindakLanjut.index', compact('active', 'posts', 'pic'));
    }

    public function searchRTM(Request $request)
    {
        $active = 3;
        $search = $request->input('search');
        $selectedYear = $request->input('year', date('Y'));

        // Query default untuk judul atau tahun
        $query = Post::query();

        // Jika input search adalah angka (kemungkinan tahun), filter berdasarkan tahun
        if (is_numeric($search)) {
            $query->whereYear('created_at', $search);
        } else {
            // Jika input bukan angka, anggap sebagai judul kegiatan
            $query->where('judul', 'LIKE', '%' . $search . '%');
        }

        // Ambil data yang sesuai dengan pencarian dan hak akses pengguna
        $post = $query->whereNotNull('dokumen_tindak_lanjut')
            ->latest()
            ->paginate(10);

        // Ambil tahun yang tersedia
        $years = Post::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // dd($post);

        return view('tindak-lanjut.index', compact('active', 'post', 'years', 'selectedYear'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $active = 3;
        if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2) {
            $posts = Post::with('rtm')->whereNotNull('dokumen_tindak_lanjut')->where('jenis', 2)->get();
        } else {
            $posts = Post::with('rtm')->whereNotNull('dokumen_tindak_lanjut')->where('jenis', 2)->where('tanggungjawab', auth()->user()->name)->get();
        }

        return view('tindakLanjut.create', compact('active', 'posts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRekomendasi(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|exists:posts,id',
            'rtm.*.temuan' => 'required|string',
            'rtm.*.rekomendasi' => 'required|string',
        ]);

        $post = Post::findOrFail($validatedData['judul']);

        RTM::where('id_post', $post->id)->delete();
        foreach ($validatedData['rtm'] as $rtmData) {
            $post->rtm()->create([
                'id_post' => $post->id,
                'temuan' => $rtmData['temuan'],
                'rekomendasi' => $rtmData['rekomendasi'],
            ]);
        }

        return redirect()->route('tindak-lanjut.index')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }

    public function getRTM($postId)
    {
        $rtms = Rtm::where('id_post', $postId)
            ->with('pic_rtm')
            ->get();

        return response()->json(['rtms' => $rtms]);
    }

    public function exportExcel(Request $request)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set judul kolom
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Judul');
            $sheet->setCellValue('C1', 'Jenis Kegiatan');
            $sheet->setCellValue('D1', 'Dokumen');
            $sheet->setCellValue('E1', 'Waktu Pengumpulan');
            $sheet->setCellValue('F1', 'Temuan');
            $sheet->setCellValue('G1', 'Rekomendasi');

            // Style header
            $sheet->getStyle('A1:G1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E2EFDA',
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Get data with filters
            $query = Post::with(['rtm'])
                ->when($request->search, function ($q) use ($request) {
                    return $q->where('judul_tindak_lanjut', 'like', '%' . $request->search . '%');
                })
                ->when($request->year, function ($q) use ($request) {
                    return $q->whereYear('tindakLanjut_at', $request->year);
                });

            $posts = $query->get();

            $row = 2;
            $no = 1;

            foreach ($posts as $post) {
                $startRow = $row;
                $rtmCount = $post->rtm->count();
                $namaKegiatan = JenisKegiatan::find($post->jenis)->jenis ?? '-';
                $post->jenis_kegiatan = $namaKegiatan;

                foreach ($post->rtm as $index => $rtm) {
                    if ($index == 0) {
                        $sheet->setCellValue('A' . $row, $no);
                        $sheet->setCellValue('B' . $row, $post->judul_tindak_lanjut);
                        $sheet->setCellValue('C' . $row, $post->jenis_kegiatan);
                        $sheet->setCellValue('D' . $row, $post->dokumen_tindak_lanjut);
                        $sheet->setCellValue('E' . $row, Carbon::parse($post->tindakLanjut_at)->format('d F Y'));
                    }

                    $sheet->setCellValue('F' . $row, $rtm->temuan);
                    $sheet->setCellValue('G' . $row, $rtm->rekomendasi);

                    // Styling untuk setiap baris
                    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                        ],
                    ]);

                    $row++;
                }

                // Merge cells jika ada multiple RTM
                if ($rtmCount > 1) {
                    $sheet->mergeCells('A' . $startRow . ':A' . ($row - 1));
                    $sheet->mergeCells('B' . $startRow . ':B' . ($row - 1));
                    $sheet->mergeCells('C' . $startRow . ':C' . ($row - 1));
                    $sheet->mergeCells('D' . $startRow . ':D' . ($row - 1));
                    $sheet->mergeCells('E' . $startRow . ':E' . ($row - 1));

                    // Center alignment untuk cell yang di-merge
                    $sheet->getStyle('A' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('B' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('C' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('D' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('E' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $no++;
            }

            // Set column width
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(40);
            $sheet->getColumnDimension('G')->setWidth(40);

            // Auto wrap text untuk kolom yang panjang
            $sheet->getStyle('B:G')->getAlignment()->setWrapText(true);

            // Set filename
            $filename = 'Tindak_Lanjut_Report';
            if ($request->year) {
                $filename .= '_' . $request->year;
            }
            $filename .= '_' . date('d-m-Y') . '.xlsx';

            // Create temporary file
            $writer = new Xlsx($spreadsheet);
            $temp_file = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($temp_file);

            // Return response untuk download
            return response()->download($temp_file, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }
}
