<?php

namespace App\Http\Controllers;

use App\Charts\TugasLaporChart;
use App\Models\Peta;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{
    // public function dashboard(TugasLaporChart $tugasLaporChart)
    // {
    //     // Hitung total data
    //     $totalPosts = Post::count();
    //     // Hitung persentase laporan akhir yang sudah dikumpulkan dari semua data
    //     $submittedLaporanAkhir = Post::whereNotNull('laporan_akhir')->count();
    //     $laporanAkhirRate = $totalPosts > 0 ? ($submittedLaporanAkhir / $totalPosts) * 100 : 0;

    //     // Query untuk anggota yang ditugaskan
    //     $user = Auth::user();
    //     $assignedPostsQuery = Post::where(function ($query) use ($user) {
    //         $query->where('tanggungjawab', $user->name);
    //             // ->orWhere('anggota', 'LIKE', '%' . $user->name . '%');
    //     });

    //     // Ambil data anggota yang ditugaskan
    //     $assignedPosts = $assignedPostsQuery->get();

    //     // Ambil semua post untuk super admin dan admin
    //     $allPosts = collect([]);
    //     if ($user->id_level == 1 || $user->id_level == 2) {
    //         $allPosts = Post::all();
    //     } elseif ($user->id_level == 3 || $user->id_level == 4 || $user->id_level == 6) {
    //         $allPosts = Post::where('id_unit_kerja', $user->id_unit_kerja)->get();
    //     }

    //     // Hitung persentase laporan akhir untuk data yang ditugaskan
    //     $submittedLaporanAkhirAssigned = $assignedPostsQuery->whereNotNull('laporan_akhir')->count();
    //     $laporanAkhirRateAssigned = $assignedPosts->isNotEmpty() ? ($submittedLaporanAkhirAssigned / $assignedPosts->count()) * 100 : 0;

    //     // Hitung data yang di-approve oleh ketua
    //     // $approvedPosts = Post::whereNotNull('approvalReviu')
    //     //                      ->orWhereNotNull('approvalBerita')
    //     //                      ->orWhereNotNull('approvalPengesahan')
    //     //                      ->orWhereNotNull('approvalRubrik')
    //     //                      ->count();

    //     // // Hitung persentase data yang di-approve
    //     // $approvalRate = $totalPosts > 0 ? ($approvedPosts / $totalPosts) * 100 : 0;

    //     // Hitung jumlah data sesuai dengan bidang
    //     $bidangCounts = Post::select('bidang', \DB::raw('count(*) as total'))
    //         ->groupBy('bidang')
    //         ->pluck('total', 'bidang')
    //         ->toArray();

    //     return view('dashboard', [
    //         'tugasLaporChart' => $tugasLaporChart->build(),
    //         // 'approvalRate' => $approvalRate,
    //         'laporanAkhirRate' => $laporanAkhirRate,
    //         'laporanAkhirRateAssigned' => $laporanAkhirRateAssigned,
    //         'assignedPosts' => $assignedPosts,
    //         'allPosts' => $allPosts,
    //         'bidangCounts' => $bidangCounts,
    //         'user' => Auth::user(),
    //         'active' => 1,
    //     ]);
    // }

    public function dashboard(TugasLaporChart $tugasLaporChart)
    {
        $user = Auth::user();

        // Base query untuk posts
        $postsQuery = Post::query();

        // Filter berdasarkan level user
        if ($user->id_level == 5) {
            $postsQuery->where('bidang', '(Reviu, Audit, Monev)')
                ->where('id_unit_kerja', $user->id_unit_kerja);
        } elseif (in_array($user->id_level, [3, 4, 6])) {
            $postsQuery->where(function ($query) use ($user) {
                $query->where('tanggungjawab', $user->name)
                    ->orWhereExists(function ($subquery) use ($user) {
                        $subquery->select(\DB::raw(1))
                            ->from('sertifikats')
                            ->whereColumn('sertifikats.id_post', 'posts.id')
                            ->where('sertifikats.id_user', $user->id);
                    });
            });
        }

        // Hitung total data berdasarkan filter
        $totalPosts = $postsQuery->count();

        // Hitung persentase laporan akhir
        $submittedLaporanAkhir = $postsQuery->clone()->whereNotNull('laporan_akhir')->count();
        $laporanAkhirRate = $totalPosts > 0 ? ($submittedLaporanAkhir / $totalPosts) * 100 : 0;

        // Query untuk anggota yang ditugaskan
        $assignedPostsQuery = $postsQuery->clone();

        // Ambil data yang ditugaskan
        $assignedPosts = $assignedPostsQuery->get();

        // Ambil semua post berdasarkan level user
        $allPosts = collect([]);
        if ($user->id_level == 1 || $user->id_level == 2) {
            $allPosts = Post::all();
        } elseif ($user->id_level == 5) {
            $allPosts = Post::where('bidang', '(Reviu, Audit, Monev)')
                ->where('id_unit_kerja', $user->id_unit_kerja)
                ->get();
        } elseif (in_array($user->id_level, [3, 4, 6])) {
            $allPosts = Post::where(function ($query) use ($user) {
                $query->where('tanggungjawab', $user->name)
                    ->orWhereExists(function ($subquery) use ($user) {
                        $subquery->select(\DB::raw(1))
                            ->from('sertifikats')
                            ->whereColumn('sertifikats.id_post', 'posts.id')
                            ->where('sertifikats.id_user', $user->id);
                    });
            })->get();
        }

        // Hitung persentase laporan akhir untuk data yang ditugaskan
        $submittedLaporanAkhirAssigned = $assignedPostsQuery->whereNotNull('laporan_akhir')->count();
        $laporanAkhirRateAssigned = $assignedPosts->isNotEmpty() ? ($submittedLaporanAkhirAssigned / $assignedPosts->count()) * 100 : 0;

        // Hitung jumlah data sesuai dengan bidang
        $bidangQuery = Post::select('bidang', \DB::raw('count(*) as total'))
            ->groupBy('bidang');

        // Filter bidang berdasarkan level user
        if ($user->id_level == 5) {
            $bidangQuery->where('bidang', '(Reviu, Audit, Monev)')
                ->where('id_unit_kerja', $user->id_unit_kerja);
        } elseif (in_array($user->id_level, [3, 4, 6])) {
            $bidangQuery->where(function ($query) use ($user) {
                $query->where('tanggungjawab', $user->name)
                    ->orWhereExists(function ($subquery) use ($user) {
                        $subquery->select(\DB::raw(1))
                            ->from('sertifikats')
                            ->whereColumn('sertifikats.id_post', 'posts.id')
                            ->where('sertifikats.id_user', $user->id);
                    });
            });
        }

        $bidangCounts = $bidangQuery->pluck('total', 'bidang')->toArray();

        return view('dashboard', [
            'tugasLaporChart' => $tugasLaporChart->build(),
            'laporanAkhirRate' => $laporanAkhirRate,
            'laporanAkhirRateAssigned' => $laporanAkhirRateAssigned,
            'assignedPosts' => $assignedPosts,
            'allPosts' => $allPosts,
            'bidangCounts' => $bidangCounts,
            'user' => $user,
            'active' => 1,
        ]);
    }

    public function template()
    {
        // Path folder dokumen di dalam public
        $folderPath = ('all_template/');

        // Ambil semua file dari folder dokumen
        $files = File::files($folderPath);

        // Format data file
        $filesData = collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'path' => 'all_template/' . basename($file),
            ];
        });

        return view('template', ['files' => $filesData]);
    }


    public function search(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::where('judul', 'like', '%' . $search . '%')
            ->orWhere('deskripsi', 'like', '%' . $search . '%')
            ->orWhere('waktu', 'like', '%' . $search . '%')
            ->orWhere('tanggungjawab', 'LIKE', '%' . $search . '%')
            // ->orWhere('anggota', 'LIKE', '%' . $search . '%')
            ->paginate(10);

        return view('posts.reviewLaporan', compact('posts'));
    }
    public function searchKetua(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::where('judul', 'like', '%' . $search . '%')
            ->orWhere('deskripsi', 'like', '%' . $search . '%')
            ->orWhere('waktu', 'like', '%' . $search . '%')
            ->orWhere('tanggungjawab', 'like', '%' . $search . '%')
            ->paginate(10);

        return view('posts.reviewLaporanKetua', compact('posts'));
    }
    public function searchAkhir(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::whereNotNull('laporan_akhir') //filter yang telah upload laporan akhir
            ->where(function ($query) use ($search) {
                $query->where('judul', 'LIKE', '%' . $search . '%')
                    ->orWhere('deskripsi', 'LIKE', '%' . $search . '%');
            })
            ->paginate(10);
        return view('posts.laporanAkhir', compact('posts'));
    }
    public function searchTindakLanjut(Request $request)
    {
        $search = $request->input('search');
        $posts = Post::whereNotNull('dokumen_tindak_lanjut') //filter yang telah upload tindak lanjut
            ->where(function ($query) use ($search) {
                $query->where('judul_tindak_lanjut', 'LIKE', '%' . $search . '%');
            })
            ->paginate(10);
        return view('posts.dokumen_tindakLanjut', compact('posts'));
    }

    public function feedback()
    {
        return view('feedback');
    }
    public function feedback_web()
    {
        return view('feedback');
    }
}
