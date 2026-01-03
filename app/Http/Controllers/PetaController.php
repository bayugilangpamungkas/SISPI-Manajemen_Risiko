<?php

namespace App\Http\Controllers;

use App\Charts\TugasLaporChart;
use App\Models\Peta;
use App\Models\User;
use App\Models\DocumentHistory;
use App\Models\CommentPr;
use App\Models\Kegiatan;
use App\Models\KetuaPenelaah;
use App\Models\Post;
use App\Models\UnitKerja;
use App\Models\ImportedExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Imports\PetaRisikoImport;
use Maatwebsite\Excel\Facades\Excel;



class PetaController extends Controller
{
    public function index(Request $request, TugasLaporChart $tugasLaporChart)
    {
        $active = 7;
        //get user data yang sedang login
        $users = User::where('id', Auth::user()->id)->with('unitKerja')->first();

        //query to get data peta berdasarkan anggota
        $query = Peta::query();

        if ($users->id_level == 1 || $users->id_level == 2) {
            // Admin: dapat melihat semua data
            $showAll = true;

            // Mengambil data jenis yang dikelompokkan berdasarkan jenis untuk admin
            $jenisCount = Peta::select(
                'jenis',
                DB::raw('count(*) as total'),
                DB::raw("(SELECT anggota 
                     FROM petas p2 
                     WHERE p2.jenis = petas.jenis 
                        AND p2.anggota IS NOT NULL 
                        AND TRIM(p2.anggota) != ''
                     ORDER BY p2.created_at ASC 
                     LIMIT 1) as anggota"),
                DB::raw(
                    "(SELECT penelaah_peta 
                     FROM unit_kerjas 
                     WHERE nama_unit_kerja = petas.jenis 
                     LIMIT 1) as penelaah"
                ),
                DB::raw(
                    "(SELECT updated_at FROM petas p3 WHERE p3.jenis = petas.jenis ORDER BY p3.updated_at DESC LIMIT 1) as tahun"
                )
            )
                ->groupBy('jenis')
                ->paginate(5);
        } else {
            // Filter untuk pengguna yang mengunggah dokumen atau anggota yang ditugaskan
            $query->where(function ($q) use ($users) {
                $q->where('jenis', $users->unitKerja->nama_unit_kerja)
                    ->orWhere('anggota', 'LIKE', '%' . $users->name . '%')
                    ->orWhereHas('ketuaPenelaah', function ($q2) use ($users) {
                        $q2->where('id_ketua', $users->id);
                    });
            });

            // Mengambil data jenis yang dikelompokkan berdasarkan jenis untuk non-admin
            $jenisCount = Peta::select(
                'petas.jenis',
                DB::raw('count(*) as filtered_total'),
                DB::raw('(SELECT COUNT(*) FROM petas as p2 WHERE p2.jenis = petas.jenis) as total'),
                DB::raw("(SELECT anggota 
                     FROM petas p2 
                     WHERE p2.jenis = petas.jenis 
                        AND p2.anggota IS NOT NULL 
                        AND TRIM(p2.anggota) != ''
                     ORDER BY p2.created_at ASC 
                     LIMIT 1) as anggota"),
                DB::raw(
                    "(SELECT penelaah_peta 
                     FROM unit_kerjas 
                     WHERE nama_unit_kerja = petas.jenis 
                     LIMIT 1) as penelaah"
                ),
                DB::raw(
                    "(SELECT updated_at FROM petas p3 WHERE p3.jenis = petas.jenis ORDER BY p3.updated_at DESC LIMIT 1) as tahun"
                )
            )
                ->where(function ($q) use ($users) {
                    $q->where('jenis', $users->unitKerja->nama_unit_kerja)
                        ->orWhere('anggota', 'LIKE', '%' . $users->name . '%')
                        ->orWhereHas('ketuaPenelaah', function ($q2) use ($users) {
                            $q2->where('id_ketua', $users->id);
                        });
                })
                ->groupBy('jenis')
                ->paginate(5);

            $showAll = false;
        }


        //get filtering data from request
        $anggota = $request->input('anggota');

        if ($anggota) {
            $query->where('anggota', 'LIKE', '%' . $anggota . '%');
        }

        //get filtered petas
        $petas = $query->latest()->with('comment_prs.user')->get();

        //Perhitungan jumlah approve
        if ($showAll) {
            // Untuk Admin: hitung semua data
            $approvedCount = Peta::where('approvalPr', 'approved')->count();
            $rejectedCount = Peta::where('approvalPr', 'rejected')->count();
        } else {
            // Untuk Pengguna Biasa: hitung berdasarkan filter
            $approvedCount = $query->where('approvalPr', 'approved')->count();
            $rejectedCount = $query->where('approvalPr', 'rejected')->count();
        }

        $unitKerjas = UnitKerja::all();

        $years = Peta::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedYear = $request->input('year', date('Y'));

        $highImpactActivities = Peta::selectRaw('kode_regist, (skor_kemungkinan * skor_dampak) as skor_total')
            ->whereYear('created_at', $selectedYear)
            ->orderBy('skor_total', 'desc')
            ->get();

        $totalHighImpactActivities = $highImpactActivities->count();

        $chartData = [
            'labels' => $highImpactActivities->pluck('kode_regist'),
            'datasets' => [
                [
                    'label' => 'Skor Pengaruh',
                    'data' => $highImpactActivities->pluck('skor_total'),
                    'backgroundColor' => $this->generateColors($totalHighImpactActivities),
                ]
            ]
        ];

        return view('pr.petaRisiko', compact('active', 'petas', 'approvedCount', 'rejectedCount', 'unitKerjas', 'jenisCount', 'years', 'selectedYear', 'chartData', 'totalHighImpactActivities'))
            ->with('tugasLaporChart', $tugasLaporChart->build())
            ->with('users', $users);
    }

    private function generateColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }
        return $colors;
    }

    public function tabelUnitKerja(Request $request, $unitKerja)
    {
        $active = 7;
        $tahun = $request->input('tahun');
        $petas = Peta::where('jenis', $unitKerja)
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })
            ->get();

        // Inisialisasi array untuk matrix dan risk distribution
        $matrix = [];
        $riskDistribution = [
            'Sangat Tinggi' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Tinggi' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Sedang' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Rendah' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Sangat Rendah' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ]
        ];

        foreach ($petas as $peta) {
            // Untuk matrix
            $key = 'R-' . $peta->skor_dampak . '-' . $peta->skor_kemungkinan;
            if (!isset($matrix[$key])) {
                $matrix[$key] = [];
            }
            $matrix[$key][] = $peta->kode_regist;

            // Untuk risk distribution
            $score = $peta->skor_dampak * $peta->skor_kemungkinan;
            $level = '';

            if ($score >= 20) {
                $level = 'Sangat Tinggi';
            } elseif ($score >= 15) {
                $level = 'Tinggi';
            } elseif ($score >= 10) {
                $level = 'Sedang';
            } elseif ($score >= 5) {
                $level = 'Rendah';
            } else {
                $level = 'Sangat Rendah';
            }

            $riskDistribution[$level]['total']++;
            if ($peta->status_telaah == 1) {
                $riskDistribution[$level]['telaah']++;
            } else {
                $riskDistribution[$level]['belum_telaah']++;
            }
        }

        return view('pr.tabelUnitKerja', compact('active', 'matrix', 'unitKerja', 'tahun', 'riskDistribution'));
    }

    public function searchPetaRisiko(Request $request)
    {
        $active = 7;
        $search = $request->input('search');
        $selectedYear = $request->input('year', date('Y'));
        $user = Auth::user();
        $users = User::where('id', Auth::user()->id)->with('unitKerja')->first();

        $query = Peta::query();

        if (is_numeric($search)) {
            $query->whereYear('created_at', $search);
        } else {
            $query->where('judul', 'LIKE', '%' . $search . '%');
        }

        if ($user->id_level != 1 && $user->id_level != 2) {
            $query->where(function ($query) use ($user) {
                $query->where('nama', $user->name)
                    ->orWhere('anggota', 'LIKE', '%' . $user->name . '%');
            });
        }

        $petas = $query->paginate(10);

        $approvedCount = $query->clone()->where('approvalPr', 'approved')->count();
        $rejectedCount = $query->clone()->where('approvalPr', 'rejected')->count();

        // Modifikasi query jenisCount untuk menampilkan penelaah
        $jenisCount = Peta::select(
            'jenis',
            DB::raw('count(*) as total'),
            DB::raw("(SELECT anggota 
                 FROM petas p2 
                 WHERE p2.jenis = petas.jenis 
                    AND p2.anggota IS NOT NULL 
                    AND TRIM(p2.anggota) != ''
                 ORDER BY p2.created_at ASC 
                 LIMIT 1) as anggota"),
            DB::raw(
                "(SELECT penelaah_peta 
                 FROM unit_kerjas 
                 WHERE nama_unit_kerja = petas.jenis 
                 LIMIT 1) as penelaah"
            )
        )
            ->where(function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->whereYear('created_at', $search);
                } else {
                    $query->where('judul', 'LIKE', '%' . $search . '%');
                }
            })
            ->groupBy('jenis')
            ->paginate(5);

        $unitKerjas = UnitKerja::all();

        $years = Peta::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $highImpactActivities = Peta::selectRaw('kode_regist, (skor_kemungkinan * skor_dampak) as skor_total')
            ->whereYear('created_at', $selectedYear)
            ->orderBy('skor_total', 'desc')
            ->get();

        $totalHighImpactActivities = $highImpactActivities->count();

        $chartData = [
            'labels' => $highImpactActivities->pluck('kode_regist'),
            'datasets' => [
                [
                    'label' => 'Skor Pengaruh',
                    'data' => $highImpactActivities->pluck('skor_total'),
                    'backgroundColor' => $this->generateColors($totalHighImpactActivities),
                ]
            ]
        ];

        return view('pr.petaRisiko', compact(
            'active',
            'petas',
            'approvedCount',
            'rejectedCount',
            'jenisCount',
            'unitKerjas',
            'years',
            'selectedYear',
            'chartData',
            'totalHighImpactActivities',
            'users'
        ));
    }

    public function tabelMatrik(Request $request)
    {
        $active = 7;
        $tahun = $request->input('tahun');
        $petas = Peta::when($tahun, function ($query, $tahun) {
            return $query->whereYear('created_at', $tahun);
        })->get();

        // Inisialisasi array untuk menyimpan data telaah per level risiko
        $riskDistribution = [
            'Sangat Tinggi' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Tinggi' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Sedang' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Rendah' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ],
            'Sangat Rendah' => [
                'total' => 0,
                'telaah' => 0,
                'belum_telaah' => 0
            ]
        ];

        foreach ($petas as $peta) {
            $score = $peta->skor_dampak * $peta->skor_kemungkinan;
            $level = '';

            // Tentukan level risiko
            if ($score >= 20) {
                $level = 'Sangat Tinggi';
            } elseif ($score >= 15) {
                $level = 'Tinggi';
            } elseif ($score >= 10) {
                $level = 'Sedang';
            } elseif ($score >= 5) {
                $level = 'Rendah';
            } else {
                $level = 'Sangat Rendah';
            }

            // Increment total dan status telaah
            $riskDistribution[$level]['total']++;
            if ($peta->status_telaah == 1) {
                $riskDistribution[$level]['telaah']++;
            } else {
                $riskDistribution[$level]['belum_telaah']++;
            }
        }

        return view('pr.tabelPeta', compact('active', 'riskDistribution', 'tahun'));
    }

    public function create()
    {
        $active = 7;
        $unitKerjas = UnitKerja::all();
        $post = Post::all();
        if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2) {
            $kegiatan = Kegiatan::all();
        } else {
            $kegiatan = Kegiatan::where('id_unit_kerja', auth()->user()->id_unit_kerja)->get();
        }

        return view('pr.tambahDokPR', compact('active', 'unitKerjas', 'kegiatan'))->with([
            'user' => Auth::user(),
            'post' => $post,
        ]);
    }


    public function edit($id)
    {
        $active = 7;
        $unitKerjas = UnitKerja::all();
        $post = Post::all();
        $kegiatan = Kegiatan::all();
        $petas = Peta::findOrFail($id);

        return view('pr.editDokPR', compact('active', 'unitKerjas', 'kegiatan'))->with([
            'petas' => $petas,
            'user' => Auth::user(),
            'post' => $post,
        ]);
    }


    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        // Validate form
        $this->validate($request, [
            'id_kegiatan'     => 'required',
            'pernyataan'     => 'required',
            'kategori'     => 'required',
            'uraian'     => 'required',
            'metode'     => 'required',
            'skor_kemungkinan'     => 'required',
            'skor_dampak'     => 'required',
        ]);

        // Generate kode_regist based on unit kerja (jenis)
        $latestPeta = Peta::where('jenis', Auth::user()->unitKerja->nama_unit_kerja)->latest()->first();

        if ($latestPeta) {
            // Cek apakah kode_regist memiliki format yang valid
            $kodeParts = explode('_', $latestPeta->kode_regist);
            if (count($kodeParts) > 1) {
                $count = intval($kodeParts[1]) + 1;
            } else {
                $count = 1; // Default jika format tidak sesuai
            }
        } else {
            $count = 1; // Jika tidak ada Peta sebelumnya
        }

        $user = Auth::user();
        $unit_kerja = UnitKerja::where('id', $user->id_unit_kerja)->first();
        $kode_regist = $unit_kerja->nama_unit_kerja . '_' . $count;

        // Fungsi untuk menghitung Level Risiko
        function calculateRiskLevel($probability, $impact)
        {
            $probability = (int)substr($probability, 0, 1); // Ambil digit pertama
            $impact = (int)substr($impact, 0, 1);           // Ambil digit pertama
            $score = $probability * $impact;

            if ($score >= 21) {
                return "EXTREME";
            } elseif ($score >= 16) {
                return "HIGH";
            } elseif ($score >= 11) {
                return "MIDDLE";
            } elseif ($score >= 6) {
                return "LOW";
            } else {
                return "VERY LOW";
            }
        }

        // Hitung level risiko
        $levelRisiko = calculateRiskLevel($request->skor_kemungkinan, $request->skor_dampak);

        // Create peta
        Peta::create([
            'id_kegiatan' => $request->id_kegiatan,
            'jenis'     => $unit_kerja->nama_unit_kerja,
            'nama'      => $user->name,
            'kode_regist'     => $kode_regist,
            'pernyataan'     => $request->pernyataan,
            'kategori'     => $request->kategori,
            'uraian'     => $request->uraian,
            'metode'     => $request->metode,
            'skor_kemungkinan'     => $request->skor_kemungkinan,
            'skor_dampak'     => $request->skor_dampak,
            'tingkat_risiko'     => $levelRisiko, // Menyimpan Level Risiko ke database
        ]);

        // Redirect to index
        return redirect()->route('petas.index')->with('success', 'Dokumen berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $active = 7;
        // dd($request->all());
        //validate form
        $this->validate($request, [
            // 'id_kegiatan'     => 'required',
            // 'dokumen'   => 'mimes:xls,xlsx',
            // 'kode_regist'     => 'required',
            'judul'    => 'required',
            'jenis'     => 'required',
            'anggaran'     => 'required',
            'pernyataan'     => 'required',
            'kategori'     => 'required',
            'uraian'     => 'required',
            'metode'     => 'required',
            'skor_kemungkinan'     => 'required',
            'skor_dampak'     => 'required',
        ]);

        //update peta
        $data = $request->all();
        // dd($data);
        $petas = Peta::find($id);
        $jenis = $petas->jenis;
        $petas->update($data);

        return redirect()->route('petaRisikoDetail', $jenis)->with('success', 'Dokumen berhasil diupdate');
    }

    //Upload sesuai unit kerja
    public function uploadDokumenByJenis(Request $request, $jenis)
    {
        $request->validate([
            'dokumen' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;
        $jenisDokumen = 'Dokumen PR';
        $extension = $request->file('dokumen')->getClientOriginalExtension();

        $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        $request->file('dokumen')->move('dokumenPR/', $newFileName);

        // Simpan informasi dokumen ke database
        // Misalnya, update dokumen untuk jenis tertentu
        Peta::where('jenis', $jenis)->update(['dokumen' => $newFileName]);

        return redirect()->route('petaRisikoDetail', $jenis)->with('success', 'Dokumen berhasil diunggah.');
    }

    //Edit Data
    public function updateData(Request $request, $jenis)
    {
        // Validasi form
        $this->validate($request, [
            'dokumen' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        // Cari data Peta berdasarkan jenis
        $petas = Peta::where('jenis', $jenis)->get();

        // Simpan riwayat dokumen lama dan update dokumen baru untuk setiap entri
        foreach ($petas as $peta) {
            // Simpan riwayat dokumen lama
            if ($peta->dokumen) {
                DocumentHistory::create([
                    'peta_id' => $peta->id,
                    'dokumen' => $peta->dokumen,
                    'uploaded_at' => now(),
                    'status' => $peta->approvalPr,
                ]);
            }

            $currentDate = date('dmY_His');
            $pengunggah = auth()->user()->name;

            // Update dokumen baru
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                // Debugging
                if ($file->isValid()) {
                    $jenisDokumen = 'Dokumen PR';
                    $extension = $file->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $file->move(public_path('dokumenPR/'), $newFileName);
                    $peta->dokumen = $newFileName;
                } else {
                    $error = $file->getErrorMessage();
                    Log::error("File upload error: $error");
                    return redirect()->back()->with('error', 'File upload error: ' . $error);
                }
            }

            // Update status dokumen
            $peta->approvalPr = 'Pending';
            $peta->save();
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diupdate');
    }

    //Hapus Data
    public function destroy($id)
    {
        $petas = Peta::find($id);
        $jenis = $petas->jenis;
        $petas->delete();

        return redirect()->route('petaRisikoDetail', $jenis)->with('success', 'Data berhasil dihapus.');
        // Hapus record dari database
    }

    public function tugas($jenis)
    {
        $active = 7;
        // Cari data berdasarkan jenis (unit kerja)
        $peta = Peta::where('jenis', $jenis)->firstOrFail();

        // Ambil semua user untuk ditampilkan sebagai opsi penelaah
        $users = User::all();

        return view('pr.tambahPR', compact('active', 'peta', 'users', 'jenis'))->with([
            'user' => Auth::user(),
        ]);
    }

    public function tambahtugas(Request $request, $jenis)
    {
        $request->validate([
            // 'waktu'     => 'required|min:1',
            'anggota'     => 'required',
        ], [
            'anggota.required' => 'Anggota field is required.',
        ]);

        // Cari peta risiko berdasarkan jenis (unit kerja)
        $peta = Peta::where('jenis', $jenis)->firstOrFail();

        // Simpan tugas baru
        // $peta->waktu = $request->waktu;
        $peta->anggota = $request->anggota;
        $peta->save();

        return redirect()->route('petaRisikoDetail', ['jenis' => $jenis])->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function tambahTugasKetua(Request $request, $jenis)
    {
        $request->validate([
            'waktu'     => 'required|min:1',
        ], [
            'waktu.required' => 'Waktu field is required.',
        ]);

        // Cari peta risiko berdasarkan jenis (unit kerja)
        $peta = Peta::where('jenis', $jenis)->firstOrFail();

        // Simpan tugas baru
        $peta->waktu = $request->waktu;
        $peta->save();

        // $unit = UnitKerja::where('nama_unit_kerja', 'LIKE', '%' . $jenis . '%')->firstOrFail();
        $ketua = User::where('id_level', 3)->firstOrFail();

        KetuaPenelaah::create([
            'id_peta' => $peta->id,
            'id_ketua' => $ketua->id,
        ]);

        return redirect()->route('petaRisikoDetail', ['jenis' => $jenis])->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function tambahAnggota($jenis)
    {
        $active = 7;
        // Cari data berdasarkan jenis (unit kerja)
        $peta = Peta::where('jenis', $jenis)->firstOrFail();

        // Ambil semua user untuk ditampilkan sebagai opsi penelaah
        $users = User::all();

        return view('pr.tambahPR', compact('active', 'peta', 'users', 'jenis'))->with([
            'user' => Auth::user(),
        ]);
    }

    public function storeAnggota(Request $request, $jenis)
    {
        $request->validate([
            'anggota'     => 'required',
        ], [
            'anggota.required' => 'Anggota field is required.',
        ]);

        // Cari peta risiko berdasarkan jenis (unit kerja)
        $peta = Peta::where('jenis', $jenis)->firstOrFail();

        // Simpan tugas baru
        $peta->anggota = $request->anggota;
        $peta->save();

        return redirect()->route('petaRisikoDetail', ['jenis' => $jenis])->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function detailPR($id)
    {
        $active = 7;
        $petas = Peta::with('ketuaPenelaah')->findOrFail($id);
        $comment_prs_aspek = CommentPr::where('jenis', 'keuangan')->where('peta_id', $petas->id)->get();

        $comment_prs_analisis = CommentPr::where('jenis', 'analisis')->where('peta_id', $petas->id)->get();
        return view('pr.detailPR', compact('active', 'petas', 'comment_prs_aspek', 'comment_prs_analisis'));
    }

    public function exportExcelPR(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'idUsulan');
        $sheet->setCellValue('B1', 'nmKegiatan');
        $sheet->setCellValue('C1', 'nilRabUsulan');
        $sheet->setCellValue('D1', 'nmUnit');
        $sheet->setCellValue('E1', 'pernyataanRisiko');
        $sheet->setCellValue('F1', 'uraianDampak');
        $sheet->setCellValue('G1', 'pengendalian');
        $sheet->setCellValue('H1', 'resiko');
        $sheet->setCellValue('I1', 'probabilitas');
        $sheet->setCellValue('J1', 'dampak');
        $sheet->setCellValue('K1', 'waktuTelaahSubstansi');
        $sheet->setCellValue('L1', 'waktuTelaahTeknis');
        $sheet->setCellValue('M1', 'waktuTelaahSPI');

        // Styling Header
        $sheet->getStyle('A1:M1')->applyFromArray([
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
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Baris awal untuk data
        $row = 2;

        // Get data kegiatan
        $petas = Peta::all();

        // Loop data kegiatan
        foreach ($petas as $peta) {
            // Konversi balik skor ke text
            $probabilitas = match ($peta->skor_kemungkinan) {
                1 => 'Sangat Jarang',
                2 => 'Jarang',
                3 => 'Kadang-kadang',
                4 => 'Sering',
                5 => 'Sangat Sering',
                default => '-'
            };

            $dampak = match ($peta->skor_dampak) {
                1 => 'Sangat Sedikit Berpengaruh',
                2 => 'Sedikit Berpengaruh',
                3 => 'Cukup Berpengaruh',
                4 => 'Berpengaruh',
                5 => 'Sangat Berpengaruh',
                default => '-'
            };

            // Format tanggal
            $waktuTelaahSubstansi = $peta->waktu_telaah_subtansi ? date('d/m/Y', strtotime($peta->waktu_telaah_subtansi)) : '-';
            $waktuTelaahTeknis = $peta->waktu_telaah_teknis ? date('d/m/Y', strtotime($peta->waktu_telaah_teknis)) : '-';
            $waktuTelaahSPI = $peta->waktu_telaah_spi ? date('d/m/Y', strtotime($peta->waktu_telaah_spi)) : '-';

            $sheet->setCellValue('A' . $row, $peta->kode_regist);
            $sheet->setCellValue('B' . $row, $peta->judul);
            $sheet->setCellValue('C' . $row, $peta->anggaran);
            $sheet->setCellValue('D' . $row, $peta->jenis);
            $sheet->setCellValue('E' . $row, $peta->pernyataan);
            $sheet->setCellValue('F' . $row, $peta->uraian);
            $sheet->setCellValue('G' . $row, $peta->metode);
            $sheet->setCellValue('H' . $row, $peta->kategori);
            $sheet->setCellValue('I' . $row, $probabilitas);
            $sheet->setCellValue('J' . $row, $dampak);
            $sheet->setCellValue('K' . $row, $waktuTelaahSubstansi);
            $sheet->setCellValue('L' . $row, $waktuTelaahTeknis);
            $sheet->setCellValue('M' . $row, $waktuTelaahSPI);

            // Tambahkan border untuk setiap baris
            $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $row++;
        }

        $sheet->getStyle('A2:M' . $row)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ]);

        // Atur lebar kolom otomatis
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Peta_Risiko';
        if ($request->year) {
            $filename .= '_' . $request->year;
        }
        $filename .= '_' . date('d-m-Y') . '.xlsx';

        // Simpan file sementara
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        // Return response untuk download
        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }
    //detail per jenis
    public function detailByJenis(Request $request, $jenis)
    {
        $active = 7;
        $search = $request->input('search');

        // Buat query dasar dengan join ke tabel kegiatan
        $query = Peta::query()
            ->with('comment_prs')
            ->with('ketuaPenelaah')
            ->where('jenis', $jenis);

        if (is_numeric($search)) {
            // Jika input adalah angka, cari berdasarkan tahun
            $query->whereYear('petas.created_at', $search);
        } else {
            // Jika input bukan angka, cari berdasarkan judul di tabel kegiatan
            $query->where('judul', 'LIKE', '%' . $search . '%');
        }

        // Pastikan select kolom yang dibutuhkan untuk menghindari konflik nama kolom
        $query->select('petas.*');

        $data = $query->paginate(10);

        // Query lainnya tetap sama
        $firstPeta = Peta::with('comment_prs')
            ->with('ketuaPenelaah')
            ->where('jenis', $jenis)
            ->oldest()
            ->first();

        $id_unit_kerja = UnitKerja::where('nama_unit_kerja', $jenis)->first();
        $penelaah = User::all();
        $unitKerja = UnitKerja::all();

        $comment_prs_aspek = CommentPr::where('jenis', 'keuangan')->whereHas('peta', function ($query) use ($jenis) {
            $query->where('jenis', $jenis);
        })->get();

        $comment_prs_analisis = CommentPr::where('jenis', 'analisis')->whereHas('peta', function ($query) use ($jenis) {
            $query->where('jenis', $jenis);
        })->get();

        $telatsubstansi = Peta::where('jenis', $jenis)->where('waktu_telaah_subtansi', null)->get();
        $telatteknis = Peta::where('jenis', $jenis)->where('waktu_telaah_teknis', null)->get();
        $telatspi = Peta::where('jenis', $jenis)->where('waktu_telaah_spi', null)->get();

        return view('pr.petaRisikoDetail', compact('active', 'data', 'jenis', 'unitKerja', 'comment_prs_aspek', 'comment_prs_analisis', 'penelaah', 'firstPeta', 'telatsubstansi', 'telatteknis', 'telatspi'));
    }

    public function exportExcelPRJenis(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'idUsulan');
        $sheet->setCellValue('B1', 'nmKegiatan');
        $sheet->setCellValue('C1', 'nilRabUsulan');
        $sheet->setCellValue('D1', 'nmUnit');
        $sheet->setCellValue('E1', 'pernyataanRisiko');
        $sheet->setCellValue('F1', 'uraianDampak');
        $sheet->setCellValue('G1', 'pengendalian');
        $sheet->setCellValue('H1', 'resiko');
        $sheet->setCellValue('I1', 'probabilitas');
        $sheet->setCellValue('J1', 'dampak');
        $sheet->setCellValue('K1', 'waktuTelaahSubstansi');
        $sheet->setCellValue('L1', 'waktuTelaahTeknis');
        $sheet->setCellValue('M1', 'waktuTelaahSPI');

        // Styling Header
        $sheet->getStyle('A1:M1')->applyFromArray([
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
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Baris awal untuk data
        $row = 2;

        // Get data kegiatan
        $petas = Peta::where('jenis', $request->jenis)->get();

        // Loop data kegiatan
        foreach ($petas as $peta) {
            // Konversi balik skor ke text
            $probabilitas = match ($peta->skor_kemungkinan) {
                1 => 'Sangat Jarang',
                2 => 'Jarang',
                3 => 'Kadang-kadang',
                4 => 'Sering',
                5 => 'Sangat Sering',
                default => '-'
            };

            $dampak = match ($peta->skor_dampak) {
                1 => 'Sangat Sedikit Berpengaruh',
                2 => 'Sedikit Berpengaruh',
                3 => 'Cukup Berpengaruh',
                4 => 'Berpengaruh',
                5 => 'Sangat Berpengaruh',
                default => '-'
            };

            // Format tanggal
            $waktuTelaahSubstansi = $peta->waktu_telaah_subtansi ? date('d/m/Y', strtotime($peta->waktu_telaah_subtansi)) : '-';
            $waktuTelaahTeknis = $peta->waktu_telaah_teknis ? date('d/m/Y', strtotime($peta->waktu_telaah_teknis)) : '-';
            $waktuTelaahSPI = $peta->waktu_telaah_spi ? date('d/m/Y', strtotime($peta->waktu_telaah_spi)) : '-';

            $sheet->setCellValue('A' . $row, $peta->kode_regist);
            $sheet->setCellValue('B' . $row, $peta->judul);
            $sheet->setCellValue('C' . $row, $peta->anggaran);
            $sheet->setCellValue('D' . $row, $peta->jenis);
            $sheet->setCellValue('E' . $row, $peta->pernyataan);
            $sheet->setCellValue('F' . $row, $peta->uraian);
            $sheet->setCellValue('G' . $row, $peta->metode);
            $sheet->setCellValue('H' . $row, $peta->kategori);
            $sheet->setCellValue('I' . $row, $probabilitas);
            $sheet->setCellValue('J' . $row, $dampak);
            $sheet->setCellValue('K' . $row, $waktuTelaahSubstansi);
            $sheet->setCellValue('L' . $row, $waktuTelaahTeknis);
            $sheet->setCellValue('M' . $row, $waktuTelaahSPI);

            // Tambahkan border untuk setiap baris
            $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $row++;
        }

        $sheet->getStyle('A2:M' . $row)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
            ],
        ]);

        // Atur lebar kolom otomatis
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Peta_Risiko - ' . $request->jenis;
        if ($request->year) {
            $filename .= '_' . $request->year;
        }
        $filename .= '_' . date('d-m-Y') . '.xlsx';

        // Simpan file sementara
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        // Return response untuk download
        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }

    //approval
    public function approve($id)
    {
        $peta = Peta::find($id);
        if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2 || Auth::user()->id_level == 3 || Auth::user()->id_level == 4 || Auth::user()->id_level == 6) {
            $currentTimestamp = now();
            $peta->approvalPr = 'approved';
            $peta->approvalPr_at = $currentTimestamp;
            $peta->save();

            return redirect()->route('petaRisikoDetail', $peta->jenis)->with('success', 'Dokumen berhaisl di-approve.');
        }
        return redirect()->route('petaRisikoDetail', $peta->jenis)->with('error', 'Anda tidak memiliki hak akses untuk approve dokumen ini.');
    }

    // Disapproval
    public function disapprove($id)
    {
        $peta = Peta::find($id);
        if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2 || Auth::user()->id_level == 3 || Auth::user()->id_level == 4 || Auth::user()->id_level == 6) {
            $currentTimestamp = now();
            $peta->approvalPr = 'rejected';
            $peta->approvalPr_at = $currentTimestamp;
            $peta->save();

            return redirect()->route('petaRisikoDetail', $peta->jenis)->with('success', 'Dokumen berhaisl ditolak.');
        }
        return redirect()->route('petaRisikoDetail', $peta->jenis)->with('error', 'Anda tidak memiliki hak akses untuk menolak dokumen ini.');
    }

    //Komentar Penelaah
    public function postComment(Request $request, $id)
    {
        // $peta = Peta::findOrFail($id);

        // Validasi input komentar
        $request->validate([
            'comment' => 'required|string',
        ]);
        $jenis = $request->input('jenis');

        // Simpan komentar ke dalam database
        CommentPr::create([
            'user_id' => auth()->id(),
            'peta_id' => $id,
            'jenis' => $jenis,
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil disimpan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        // dd($request->all());/

        try {
            // Ambil file dari request
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName(); // Nama asli file

            // Inisialisasi jumlah baris
            $rowCount = 0;

            // Import data dan hitung jumlah baris
            Excel::import(new PetaRisikoImport, $file);

            // Hitung jumlah baris dalam file Excel
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rowCount = $sheet->getHighestRow() - 1; // Dikurangi 1 untuk header


            // Simpan informasi ke database
            ImportedExcel::create([
                'nama_file' => $fileName,
                'jumlah_data' => $rowCount,
                'uploaded_by' => auth()->user()->name,
            ]);

            // Cek apakah ada session import_details
            if (session()->has('import_details')) {
                $details = session('import_details');
                return redirect()->back()
                    ->with('success', $details['success'])
                    ->with('warning', $details['warning'])
                    ->with('skipped_details', $details['skipped_details']);
            }

            return redirect()->back()->with('success', "Semua data berhasil diimport! ($rowCount baris disimpan)");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function penelaahPeta()
    {
        $active = 7;
        $unitKerjas = UnitKerja::all();
        $users = User::whereNot('id_level', 5)->get();
        return view('pr.penelaahPeta', compact('active', 'unitKerjas', 'users'));
    }

    public function updatePenelaahPeta(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'penelaah' => 'required|array',
            'penelaah.*' => 'nullable|string|max:255',
        ]);

        // Loop through the submitted data and update each unit kerja
        foreach ($request->penelaah as $unitKerjaId => $penelaah) {
            // Skip if penelaah is empty and no changes needed
            if (empty($penelaah)) {
                continue;
            }

            UnitKerja::where('id', $unitKerjaId)->update([
                'penelaah_peta' => $penelaah
            ]);
        }

        // return redirect()->route('peta.penelaah')
        //     ->with('success', 'Penelaah Peta berhasil diupdate.');
        return redirect()->back();
    }
}
