<?php

namespace App\Http\Controllers;

use App\Charts\TugasLaporChart;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\JenisKegiatan;
use App\Models\TemplateDokumen;
use App\Models\UnitKerja;
use App\Models\Sertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\IOFactory;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Exception\InvalidImageException;
use App\Models\RTM;
use App\Models\PIC_RTM;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index(Request $request, TugasLaporChart $tugasLaporChart)
    {
        $active = 2;
        //get user data
        $users = Auth::user();

        // Initialize pendingPosts as null
        $pendingPosts = null;

        //query to get posts based on tanggungjawab and anggota
        $query = Post::query();

        // For Ketua (id_level = 3) and Superadmin (id_level = 1)
        if ($users->id_level == 1 || $users->id_level == 3) {
            $pendingPosts = $query->where('status_task', 'pending')->latest()->paginate(5);
        }

        $approvedQuery = Post::query();
        if ($request->input('search')) {
            $search = $request->input('search');
            $approvedQuery->where('judul', 'like', '%' . $search . '%')
                ->orWhere('deskripsi', 'like', '%' . $search . '%')
                ->orWhere('waktu', 'like', '%' . $search . '%')
                ->orWhere('tanggungjawab', 'LIKE', '%' . $search . '%');
            // ->orWhere('anggota', 'LIKE', '%' . $search . '%');
        }

        if ($users->id_level == 1 || $users->id_level == 2 || $users->id_level == 3) {
            //Admin: see all post
            $approvedPosts = $approvedQuery->where('status_task', 'approved')->latest()->paginate(5);
        } else {
            $approvedPosts = $approvedQuery->where(function ($q) use ($users) {
                $q->where('tanggungjawab', $users->name)
                    ->orWhere('id_unit_kerja', $users->id_unit_kerja)
                    ->orWhereHas('sertifikat', function ($query) use ($users) {
                        $query->where('id_user', $users->id);
                    });
            })->where('status_task', 'approved')->latest()->paginate(5);
        }

        //get filtering data from request
        $tanggungjawab = $request->input('tanggungjawab');
        // $anggota = $request->input('anggota');

        if ($tanggungjawab) {
            $query->where('tanggungjawab', 'LIKE', '%' . $tanggungjawab . '%')
                ->orWhere('status_task', 'approved'); //Hanya tampilkan tugas yg telah diapprove
        }
        // if ($anggota) {
        //     $query->where('anggota', 'LIKE', '%' . $anggota . '%')
        //         ->orWhere('status_task', 'approved'); //Hanya tampilkan tugas yg telah diapprove
        // }

        //get filtered posts
        $posts = $query->latest()->paginate(5);
        $jenisKegiatan = JenisKegiatan::all()->keyBy('id');

        //render view with posts
        return view('posts.reviewLaporan', compact('active', 'posts', 'approvedPosts', 'pendingPosts', 'jenisKegiatan'))
            ->with('tugasLaporChart', $tugasLaporChart->build())
            ->with('users', $users)
            ->with('tanggungjawab', $tanggungjawab);
        // ->with('anggota', $anggota);
    }

    public function laporanAkhir(Request $request, TugasLaporChart $tugasLaporChart)
    {
        $active = 2;
        //get user data
        $users = Auth::user();

        //query to get posts based on tanggungjawab and anggota
        $query = Post::query();

        if ($users->id_level == 1 || $users->id_level == 2 || $users->id_level == 3 || $users->id_level == 6) {
            //Admin: see all post
        } else {
            $query->where(function ($q) use ($users) {
                $q->where('tanggungjawab', $users->name);
                // ->orWhere('anggota', 'LIKE', '%' . $users->name . '%');
            });
        }

        //get filtering data from request
        $tanggungjawab = $request->input('tanggungjawab');
        // $anggota = $request->input('anggota');

        if ($tanggungjawab) {
            $query->where('tanggungjawab', 'LIKE', '%' . $tanggungjawab . '%');
        }
        // if ($anggota) {
        //     $query->where('anggota', 'LIKE', '%' . $anggota . '%');
        // }

        // Filter only posts that have laporan_akhir
        $query->whereNotNull('laporan_akhir');

        //get filtered posts
        $posts = $query->with('unitKerja')->latest()->paginate(5);
        $jenisKegiatan = JenisKegiatan::all()->keyBy('id');

        //render view with posts
        return view('posts.laporanAkhir', compact('active', 'posts', 'jenisKegiatan'))
            ->with('tugasLaporChart', $tugasLaporChart->build())
            ->with('users', $users)
            ->with('tanggungjawab', $tanggungjawab);
        // ->with('anggota', $anggota);
    }
    public function reviewKetua(Request $request, TugasLaporChart $tugasLaporChart)
    {
        $active = 2;
        //get user data
        $users = Auth::user();

        //query to get posts based on tanggungjawab and anggota
        $query = Post::query();

        if ($users->id_level == 1 || $users->id_level == 2 || $users->id_level == 3 || $users->id_level == 6) {
            //Admin: see all post
        } else {
            $query->where(function ($q) use ($users) {
                $q->where('tanggungjawab', $users->name);
                // ->orWhere('anggota', 'LIKE', '%' . $users->name . '%');
            });
        }

        //get filtering data from request
        $tanggungjawab = $request->input('tanggungjawab');
        // $anggota = $request->input('anggota');

        if ($tanggungjawab) {
            $query->where('tanggungjawab', 'LIKE', '%' . $tanggungjawab . '%');
        }
        // if ($anggota) {
        //     $query->where('anggota', 'LIKE', '%' . $anggota . '%');
        // }

        //get filtered posts
        $posts = $query->where('status_task', 'approved')->latest()->paginate(5);

        //render view with posts
        return view('posts.reviewLaporanKetua', compact('active', 'posts'))
            ->with('tugasLaporChart', $tugasLaporChart->build())
            ->with('users', $users)
            ->with('tanggungjawab', $tanggungjawab);
        // ->with('anggota', $anggota);
    }
    public function print()
    {
        //get posts
        $posts = Post::all();
        return view('posts.print', compact('posts'))->with([
            'user' => Auth::user(),
        ]);
    }
    public function print_id()
    {
        //get posts
        $posts = Post::all();
        return view('posts.print_id', compact('posts'))->with([
            'user' => Auth::user(),
        ]);
    }
    public function printpdf()
    {
        //get posts
        $posts = Post::all();
        $html = view('posts.printpdf', compact('posts'))->with([
            'user' => Auth::user(),
        ]);

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }

    public function create()
    {
        $pic = User::where('id_level', '!=', 5)->get();
        // $anggota = User::select('id', 'name', 'id_unit_kerja')
        //         ->where('id_level', 5)
        //         ->groupBy('id_unit_kerja', 'id', 'name')
        //         ->get();
        $unitKerja = UnitKerja::all();
        $jenisKegiatan = JenisKegiatan::all();
        $active = 2;
        return view('posts.tambahTugas', compact('active', 'pic', 'jenisKegiatan', 'unitKerja'));
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */

    public function store(Request $request)
    {
        // dd($request->all());
        //validate form
        if ($request->jenis == 1) {
            $this->validate($request, [
                'waktu'     => 'required|min:1',
                'unitkerja' => 'required',
                'anggota' => 'required|array',
                'tempat'     => 'required|min:1',
                'jenis'     => 'required|min:1',
                'judul'     => 'required|min:1',
                'deskripsi'     => 'required|min:1',
                'bidang'     => 'required|min:1',
                'status_task'   => 'pending',
            ]);
        } else {
            $this->validate($request, [
                'waktu'     => 'required|min:1',
                'unitkerja' => 'required',
                // 'anggota'   => 'required',
                'tempat'     => 'required|min:1',
                'jenis'     => 'required|min:1',
                'judul'     => 'required|min:1',
                'deskripsi'     => 'required|min:1',
                'bidang'     => 'required|min:1',
                'tanggungjawab' => 'required',
                'status_task'   => 'pending',
            ], [
                // 'anggota.required' => 'Anggota field is required.',
                'tanggungjawab.required' => 'Tanggungjawab field is required.'
            ]);
        }
        //create post
        // dd($request->unitkerja);
        Post::create([
            'waktu'     => $request->waktu,
            'id_unit_kerja' => $request->unitkerja,
            // 'anggota'   => $request->anggota,
            'tempat'    => $request->tempat,
            'jenis'     => $request->jenis,
            'judul'     => $request->judul,
            'deskripsi'  => $request->deskripsi,
            'bidang'     => $request->bidang,
            'tanggungjawab' => $request->tanggungjawab,
        ]);

        if ($request->jenis == 1) {
            foreach ($request->anggota as $anggota) {
                $post = Post::latest()->first();
                Sertifikat::create([
                    'id_post' => $post->id,
                    'id_user' => $anggota,
                ]);
            }
        }

        // $posts = Post::latest()->first();
        // $currentDate = date('dmY_His');
        // $pengunggah = auth()->user()->name;
        // if ($request->hasFile('dokumen')) {
        //     $jenisDokumen = 'Dokumen Reviu';
        //     $extension = $request->file('dokumen')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;

        //     $request->file('dokumen')->move('dokumenrev/', $newFileName);
        //     $posts->dokumen = $newFileName;
        //     $posts->save();
        // }
        // if ($request->hasFile('templateA')) {
        //     $jenisDokumen = 'Template Berita';
        //     $extension = $request->file('templateA')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('templateA')->move('template_berita/', $newFileName);
        //     $posts->templateA = $newFileName;
        //     $posts->save();
        // }
        // if ($request->hasFile('templateB')) {
        //     $jenisDokumen = 'Template Pengesahan';
        //     $extension = $request->file('templateB')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('templateB')->move('template_pengesahan/', $newFileName);
        //     $posts->templateB = $newFileName;
        //     $posts->save();
        // }
        // if ($request->hasFile('rubrik')) {
        //     $jenisDokumen = 'Template Rubrik';
        //     $extension = $request->file('rubrik')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('rubrik')->move('template_rubrik/', $newFileName);
        //     $posts->rubrik = $newFileName;
        //     $posts->save();
        // }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    //edit post
    public function edit($id)
    {
        $active = 2;
        $post = Post::findOrFail($id);
        $unitKerja = UnitKerja::all();
        $jenisKegiatan = JenisKegiatan::all();
        $pic = User::all();

        // Ambil ID anggota yang terkait dengan post ini
        $selectedAnggota = Sertifikat::where('id_post', $id)
            ->pluck('id_user')
            ->toArray();

        // Tambahkan selected anggota ke post object
        $post->anggota = $selectedAnggota;
        dd($post);
        return view('posts.edit', compact('post', 'unitKerja', 'jenisKegiatan', 'pic', 'active'));
    }

    //approval tugas
    public function approve_task($id)
    {
        $post = Post::findOrFail($id);
        $post->status_task = 'approved';
        $post->save();
        return redirect()->route('posts.index')->with(['success' => 'Tugas berhasil disetujui!']);
    }

    public function disapprove_task($id)
    {
        $post = Post::findOrFail($id);
        $post->status_task = 'rejected';
        $post->save();
        return redirect()->route('posts.index')->with(['success' => 'Tugas berhasil ditolak!']);
    }

    //submit tugas
    public function submit(Request $request, $id)
    {
        $request->validate([
            'file_type' => 'required|in:hasilReviu,hasilBerita,hasilPengesahan,hasilRubrik',
            'hasilReviu' => 'nullable|mimes:doc,docx|max:10240',
            'hasilBerita' => 'nullable|mimes:doc,docx|max:10240',
            'hasilPengesahan' => 'nullable|mimes:doc,docx|max:10240',
            'hasilRubrik' => 'nullable|mimes:xls,xlsx|max:10240',
        ]);

        $posts = Post::findOrFail($id);
        $currentTimestamp = now();
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;
        $fileType = $request->input('file_type');

        switch ($fileType) {
            case 'hasilReviu':
                if ($request->hasFile('hasilReviu')) {
                    $jenisDokumen = 'Hasil Dokumen Reviu';
                    $extension = $request->file('hasilReviu')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;

                    $request->file('hasilReviu')->move('hasil_reviu/', $newFileName);
                    $posts->hasilReviu = $newFileName;
                    $posts->hasilReviu_uploaded_at = $currentTimestamp;
                }
                break;
            case 'hasilBerita':
                if ($request->hasFile('hasilBerita')) {
                    $jenisDokumen = 'Hasil Berita Acara';
                    $extension = $request->file('hasilBerita')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('hasilBerita')->move('hasil_berita/', $newFileName);
                    $posts->hasilBerita = $newFileName;
                    $posts->hasilBerita_uploaded_at = $currentTimestamp;
                }
                break;
            case 'hasilPengesahan':
                if ($request->hasFile('hasilPengesahan')) {
                    $jenisDokumen = 'Hasil Lembar Pengesahan';
                    $extension = $request->file('hasilPengesahan')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('hasilPengesahan')->move('hasil_pengesahan/', $newFileName);
                    $posts->hasilPengesahan = $newFileName;
                    $posts->hasilPengesahan_uploaded_at = $currentTimestamp;
                }
                break;
            case 'hasilRubrik':
                if ($request->hasFile('hasilRubrik')) {
                    $jenisDokumen = 'Hasil Kertas Kerja';
                    $extension = $request->file('hasilRubrik')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('hasilRubrik')->move('hasil_rubrik/', $newFileName);
                    $posts->hasilRubrik = $newFileName;
                    $posts->hasilRubrik_uploaded_at = $currentTimestamp;
                }
                break;
        }
        $posts->save();

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    //submit laporan akhir
    public function submit_akhir(Request $request, $id)
    {
        $request->validate([
            'laporan_akhir' => 'required|mimes:pdf',
        ]);

        $posts = Post::findOrFail($id);

        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;

        if ($request->hasFile('laporan_akhir')) {
            $jenisDokumen = 'Laporan Akhir';
            $extension = $request->file('laporan_akhir')->getClientOriginalExtension();

            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            $request->file('laporan_akhir')->move('hasil_akhir/', $newFileName);
            $posts->laporan_akhir = $newFileName;
            $posts->save();
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    //koreksi ketua
    public function koreksi_ketua(Request $request, $id)
    {
        $request->validate([
            'file_type' => 'required|in:koreksiReviu,koreksiBerita,koreksiPengesahan,koreksiRubrik',
            'koreksiReviu' => 'nullable|mimes:doc,docx|max:10240',
            'koreksiBerita' => 'nullable|mimes:doc,docx|max:10240',
            'koreksiPengesahan' => 'nullable|mimes:doc,docx|max:10240',
            'koreksiRubrik' => 'nullable|mimes:xls,xlsx|max:10240',
        ]);

        $posts = Post::findOrFail($id);
        $fileType = $request->input('file_type');
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;

        switch ($fileType) {
            case 'koreksiReviu':
                if ($request->hasFile('koreksiReviu')) {
                    $jenisDokumen = 'Koreksi Dokumen Reviu';
                    $extension = $request->file('koreksiReviu')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('koreksiReviu')->move('koreksi_reviu/', $newFileName);
                    $posts->koreksiReviu = $newFileName;
                }
                break;
            case 'koreksiBerita':
                if ($request->hasFile('koreksiBerita')) {
                    $jenisDokumen = 'Koreksi Berita Acara';
                    $extension = $request->file('koreksiBerita')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('koreksiBerita')->move('koreksi_berita/', $newFileName);
                    $posts->koreksiBerita = $newFileName;
                }
                break;
            case 'koreksiPengesahan':
                if ($request->hasFile('koreksiPengesahan')) {
                    $jenisDokumen = 'Koreksi Lembar Pengesahan';
                    $extension = $request->file('koreksiPengesahan')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('koreksiPengesahan')->move('koreksi_pengesahan/', $newFileName);
                    $posts->koreksiPengesahan = $newFileName;
                }
                break;
            case 'koreksiRubrik':
                if ($request->hasFile('koreksiRubrik')) {
                    $jenisDokumen = 'Koreksi Kertas Kerja';
                    $extension = $request->file('koreksiRubrik')->getClientOriginalExtension();

                    $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
                    $request->file('koreksiRubrik')->move('koreksi_rubrik/', $newFileName);
                    $posts->koreksiRubrik = $newFileName;
                }
                break;
        }

        $posts->save();

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    //tampil data
    public function tampilData($id)
    {
        $active = 2;
        $post = Post::find($id);
        $pic = User::where('id_level', '!=', 5)->get();
        // $anggota = User::select('id', 'name', 'id_unit_kerja')
        //         ->where('id_level', 5)
        //         ->groupBy('id_unit_kerja', 'id', 'name')
        //         ->get();
        $unitKerja = UnitKerja::all();
        $jenisKegiatan = JenisKegiatan::all();
        // Ambil ID anggota yang terkait dengan post ini
        $selectedAnggota = Sertifikat::where('id_post', $id)
            ->pluck('id_user')
            ->toArray();

        // Tambahkan selected anggota ke post object
        $post->anggota = $selectedAnggota;
        // dd($post);
        return view('posts.tampilEdit', compact('post', 'unitKerja', 'jenisKegiatan', 'pic', 'active'));
    }

    //detail tugas
    public function detailTugas($id)
    {
        $active = 2;
        $posts = Post::find($id);
        $jenisKegiatan = JenisKegiatan::where('id', $posts->jenis)->first();
        if ($posts->jenis == 1) {
            $sertifikat = Sertifikat::with('user')->where('id_post', $posts->id)->get();
            $templateE = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Dokumen Tindak Lanjut')->first();
            // dd($sertifikat);
            return view('posts.detailPelatihanSertifikasi', compact('active', 'posts', 'sertifikat', 'jenisKegiatan', 'templateE'))->with([]);
        } else {
            $comments = Comment::where('post_id', $posts->id)->get();
            $templateA = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Dokumen Reviu')->first();
            $templateB = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Berita Acara')->first();
            $templateC = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Lembar Pengesahan')->first();
            $templateD = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Kertas Kerja')->first();
            $templateE = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Dokumen Tindak Lanjut')->first();
            return view('posts.detailTugas', compact('active', 'posts', 'comments', 'templateA', 'templateB', 'templateC', 'templateD', 'templateE', 'jenisKegiatan'))->with([
                'user' => Auth::user(),
            ]);
        }
    }

    //detail tugas ketua
    public function detailTugasKetua($id)
    {
        $active = 2;
        $posts = Post::find($id);
        $jenisKegiatan = JenisKegiatan::where('id', $posts->jenis)->first();
        $comments = Comment::where('post_id', $posts->id)->get();
        $templateA = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Dokumen Reviu')->first();
        $templateB = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Berita Acara')->first();
        $templateC = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Lembar Pengesahan')->first();
        $templateD = TemplateDokumen::where('id_jenis', $posts->jenis)->where('judul', 'Template Kertas Kerja')->first();
        return view('posts.detailTugasKetua', compact('active', 'posts', 'comments', 'templateA', 'templateB', 'templateC', 'templateD', 'jenisKegiatan'))->with([
            'user' => Auth::user(),
        ]);
    }

    public function uploadSuratTugas(Request $request, $id)
    {
        $request->validate([
            'surat_tugas' => 'required|mimes:doc,docx,pdf|max:10240',
        ]);

        $posts = Post::findOrFail($id);

        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;

        if ($request->hasFile('surat_tugas')) {
            $jenisDokumen = 'Surat Tugas';
            $extension = $request->file('surat_tugas')->getClientOriginalExtension();

            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            $request->file('surat_tugas')->move('surat_tugas/', $newFileName);
            $posts->suratTugas = $newFileName;
            $posts->save();
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    //approval
    public function approve($id, $type)
    {
        $post = Post::find($id);
        if (Auth::user()->id_level == 1 || Auth::user()->id_level == 3) {
            $currentTimestamp = now();
            switch ($type) {
                case 'reviu':
                    $post->approvalReviu = 'approved';
                    $post->approvalReviu_at = $currentTimestamp;
                    break;
                case 'berita':
                    $post->approvalBerita = 'approved';
                    $post->approvalBerita_at = $currentTimestamp;
                    break;
                case 'pengesahan':
                    $post->approvalPengesahan = 'approved';
                    $post->approvalPengesahan_at = $currentTimestamp;
                    break;
                case 'rubrik':
                    $post->approvalRubrik = 'approved';
                    $post->approvalRubrik_at = $currentTimestamp;
                    break;
                default:
                    return redirect()->route('detailTugasKetua', $id)->with('error', 'Tipe approval tidak valid');
            }

            $post->save();
            return redirect()->route('detailTugasKetua', $id)->with('success', 'Dokumen berhasil di-approve');
        }

        return redirect()->route('detailTugasKetua', $id)->with('error', 'Anda tidak memiliki hak akses untuk approve dokumen ini');
    }

    public function approvePIC($id)
    {
        $post = Post::find($id);
        $post->approvalReviuPIC = 'approved';
        $post->save();
        return redirect()->route('detailTugas', $id)->with('success', 'Dokumen berhasil di-approve');
    }

    // Disapproval
    public function disapprove($id, $type)
    {
        $post = Post::find($id);
        if (Auth::user()->id_level == 1 || Auth::user()->id_level == 3) {
            switch ($type) {
                case 'reviu':
                    $post->approvalReviu = 'rejected';
                    break;
                case 'berita':
                    $post->approvalBerita = 'rejected';
                    break;
                case 'pengesahan':
                    $post->approvalPengesahan = 'rejected';
                    break;
                case 'rubrik':
                    $post->approvalRubrik = 'rejected';
                    break;
                default:
                    return redirect()->route('detailTugasKetua', $id)->with('error', 'Tipe approval tidak valid');
            }

            $post->save();
            return redirect()->route('detailTugasKetua', $id)->with('success', 'Dokumen berhasil ditolak');
        }

        return redirect()->route('detailTugasKetua', $id)->with('error', 'Anda tidak memiliki hak akses untuk menolak dokumen ini');
    }

    public function disapprovePIC(Request $request, $id, $type)
    {
        // Validasi request
        $request->validate([
            'koreksiReviuPIC' => 'nullable|mimes:doc,docx|max:10240', // Tambahkan max file size 10MB
            'commentPIC' => 'required|string',
        ]);

        $post = Post::findOrFail($id); // Gunakan findOrFail untuk handling jika post tidak ditemukan
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;

        // Upload file jika ada
        if ($request->hasFile('koreksiReviuPIC')) { // Sesuaikan dengan nama di form
            $jenisDokumen = 'Koreksi_Dokumen_Reviu_PIC';
            $extension = $request->file('koreksiReviuPIC')->getClientOriginalExtension();
            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;

            $request->file('koreksiReviuPIC')->move('koreksi_reviu_PIC/', $newFileName);
            $post->koreksiReviuPIC = $newFileName;
        }

        // Update post
        // $post->commenter = $pengunggah;
        $post->approvalReviuPIC = 'rejected';
        // $post->commentReviu = $request->commentPIC; // Tambahkan comment ke database jika ada field-nya
        $post->save();

        $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->input('commentPIC'),
            'type' => $type,
        ]);

        return redirect()->route('detailTugas', $id)->with('success', 'Dokumen berhasil di-disapprove');
    }

    //Comment Ketua
    public function showCommentForm($id, $type)
    {
        $active = 2;
        $post = Post::findOrFail($id);
        return view('comments.create', compact('active', 'post', 'type'));
    }

    public function postComment(Request $request, $id, $type)
    {
        $post = Post::findOrFail($id);

        // Validasi input komentar
        $request->validate([
            'comment' => 'required|string',
        ]);

        // Simpan komentar ke dalam database
        $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->input('comment'),
            'type' => $type,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil disimpan.');
    }

    //Edit Data
    public function updateData(Request $request, $id)
    {
        // get post
        $post = Post::find($id);


        //validate form
        if ($request->jenis == 1) {
            $this->validate($request, [
                'waktu'     => 'required|min:1',
                'unitkerja' => 'required',
                'anggota' => 'required|array',
                'tempat'     => 'required|min:1',
                'jenis'     => 'required|min:1',
                'judul'     => 'required|min:1',
                'deskripsi'     => 'required|min:1',
                'bidang'     => 'required|min:1',
                'status_task'   => 'pending',
            ]);
        } else {
            $this->validate($request, [
                'waktu'     => 'required|min:1',
                'unitkerja' => 'required',
                // 'anggota'   => 'required',
                'tempat'     => 'required|min:1',
                'jenis'     => 'required|min:1',
                'judul'     => 'required|min:1',
                'deskripsi'     => 'required|min:1',
                'bidang'     => 'required|min:1',
                'tanggungjawab' => 'required',
                'status_task'   => 'pending',
            ], [
                // 'anggota.required' => 'Anggota field is required.',
                'tanggungjawab.required' => 'Tanggungjawab field is required.'
            ]);
        }

        //update post
        $post->waktu = $request->waktu;
        $post->id_unit_kerja = $request->unitkerja;
        // $post->anggota = $request->anggota;
        $post->tempat = $request->tempat;
        $post->jenis = $request->jenis;
        $post->judul = $request->judul;
        $post->deskripsi = $request->deskripsi;
        $post->bidang = $request->bidang;
        $post->tanggungjawab = $request->tanggungjawab;

        Sertifikat::where('id_post', $id)->delete();
        if ($request->jenis == 1) {
            foreach ($request->anggota as $anggota) {
                Sertifikat::create([
                    'id_post' => $post->id,
                    'id_user' => $anggota,
                ]);
            }
        }
        // $currentDate = date('dmY_His');
        // $pengunggah = auth()->user()->name;

        // if ($request->hasFile('dokumen')) {
        //     $jenisDokumen = 'Dokumen Reviu';
        //     $extension = $request->file('dokumen')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;

        //     $request->file('dokumen')->move('dokumenrev/', $newFileName);
        //     $post->dokumen = $newFileName;
        // }

        // if ($request->hasFile('templateA')) {
        //     $jenisDokumen = 'Template Berita';
        //     $extension = $request->file('templateA')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('templateA')->move('template_berita/', $newFileName);
        //     $post->templateA = $newFileName;
        // }

        // if ($request->hasFile('templateB')) {
        //     $jenisDokumen = 'Template Pengesahan';
        //     $extension = $request->file('templateB')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('templateB')->move('template_pengesahan/', $newFileName);
        //     $post->templateB = $newFileName;
        // }

        // if ($request->hasFile('rubrik')) {
        //     $jenisDokumen = 'Template Rubrik';
        //     $extension = $request->file('rubrik')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('rubrik')->move('template_rubrik/', $newFileName);
        //     $post->rubrik = $newFileName;
        // }

        // save post
        $post->save();

        return redirect()->route('posts.index')->with('success', 'Data Berhasil Diupdate!');
    }

    //Hapus Data
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Hapus file terkait jika ada
        if ($post->dokumen) {
            Storage::delete('dokumenrev/' . $post->dokumen);
        }
        if ($post->templateA) {
            Storage::delete('template_berita/' . $post->templateA);
        }
        if ($post->templateB) {
            Storage::delete('template_pengesahan/' . $post->templateB);
        }
        if ($post->rubrik) {
            Storage::delete('template_rubrik/' . $post->rubrik);
        }

        // Hapus record dari database
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    //Searching
    public function show(Request $request)
    {
        $active = 2;
        $search = $request->input('search');
        $posts = Post::where('judul', 'LIKE', '%' . $search . '%')
            ->orWhere('waktu', 'LIKE', '%' . $search . '%') // Sesuaikan dengan format pencarian
            ->paginate(10);

        return view('posts.reviewLaporan', compact('active', 'posts'));
    }

    public function search(Request $request)
    {
        $active = 2;
        $search = $request->input('search');
        $posts = Post::where('judul', 'like', '%' . $search . '%')
            ->orWhere('waktu', 'like', '%' . $search . '%') // Sesuaikan dengan format pencarian
            ->paginate(10);

        return view('posts.index', compact('active', 'posts'));
    }
    // Print Detail Tugas yang sudah dikonversi
    public function printDetailTugas($id)
    {
        $post = Post::findOrFail($id);

        // Periksa jika semua persetujuan telah disetujui
        if (
            $post->approvalReviu == 'approved' &&
            $post->approvalBerita == 'approved' &&
            $post->approvalPengesahan == 'approved' &&
            $post->approvalRubrik == 'approved'
        ) {
            // Gabungkan dokumen-dokumen
            $filePath = $this->mergeDocuments($id);

            // Tambahkan tanda tangan
            $finalFilePath = $this->insertSignature($filePath, $post->tanggungjawab);

            // Unduh dokumen final
            // return response()->download($finalFilePath, 'Dokumen_' . $post->id . '.docx');
            return response()->download($finalFilePath, basename($finalFilePath));
        } else {
            // Jika tidak semua dokumen telah disetujui, kembalikan ke halaman sebelumnya dengan pesan kesalahan
            return redirect()->back()->with('error', 'Tidak dapat membuat dokumen karena belum semua dokumen disetujui.');
        }
    }

    private function insertSignature($filePath, $penanggungjawabId)
    {
        $penanggungjawab = User::where('name', $penanggungjawabId)->firstOrFail();

        $signaturePath = public_path('tanda_tangans/' . $penanggungjawab->tanda_tangan);

        if (!file_exists($signaturePath)) {
            throw new \Exception('File tanda tangan tidak ditemukan: ' . $signaturePath);
        }

        // Buka dokumen yang ada
        $phpWord = IOFactory::load($filePath);

        // Dapatkan halaman terakhir
        $sections = $phpWord->getSections();
        $lastSection = end($sections);

        // Hitung ukuran halaman
        $pageWidth = $lastSection->getStyle()->getPageSizeW() - $lastSection->getStyle()->getMarginRight() - $lastSection->getStyle()->getMarginLeft();
        $pageHeight = $lastSection->getStyle()->getPageSizeH() - $lastSection->getStyle()->getMarginBottom();

        // Tambahkan teks dan tanda tangan menggunakan addTextRun
        $textrun = $lastSection->addTextRun(['alignment' => Jc::RIGHT]);
        $textrun->addText('Penanggung Jawab,', ['bold' => true]);
        $textrun->addTextBreak();
        try {
            $textrun->addImage($signaturePath, ['width' => 100, 'height' => 50]);
        } catch (InvalidImageException $e) {
            // Do nothing if the image is invalid
        }
        $textrun->addTextBreak();
        $textrun->addText($penanggungjawab->name, ['bold' => true]);

        // Posisikan tanda tangan di kanan bawah
        $lastParagraph = $lastSection->addTextRun(['alignment' => Jc::RIGHT]);
        $lastParagraph->addText('', [], ['position' => 'absolute', 'marginLeft' => $pageWidth - 150, 'marginTop' => $pageHeight - 150]);

        // Simpan dokumen yang telah dimodifikasi
        $finalFilePath = storage_path('app/temp/dokumen_final_' . uniqid() . '.docx');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($finalFilePath);

        return $finalFilePath;
    }

    // Konversi dan merge dokumen
    public function mergeDocuments($id)
    {
        $post = Post::findOrFail($id);

        // Tentukan lokasi dokumen yang akan digabung
        $pathDokumenReviu = ('hasil_reviu/' . $post->hasilReviu);
        $pathDokumenBerita = ('hasil_berita/' . $post->hasilBerita);
        $pathDokumenPengesahan = ('hasil_pengesahan/' . $post->hasilPengesahan);

        // Buat objek PhpWord baru untuk dokumen yang digabung
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Fungsi untuk menambahkan konten dari dokumen ke section
        $this->addContentFromDocx($phpWord, $pathDokumenReviu);
        $this->addContentFromDocx($phpWord, $pathDokumenBerita);
        $this->addContentFromDocx($phpWord, $pathDokumenPengesahan);

        // Simpan dokumen yang digabungkan ke file sementara
        $tempFile = storage_path('app/public/temp_document.docx');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return $tempFile; // Kembalikan path file dokumen yang digabung
    }

    // Fungsi untuk menambahkan konten dari file DOCX ke objek PhpWord
    private function addContentFromDocx($phpWord, $filePath)
    {
        $source = \PhpOffice\PhpWord\IOFactory::load($filePath);

        foreach ($source->getSections() as $section) {
            $newSection = $phpWord->addSection();
            foreach ($section->getElements() as $element) {
                $this->copyElement($newSection, $element);
            }
        }
    }

    // Fungsi untuk menyalin elemen ke section baru
    private function copyElement($newSection, $element)
    {
        $type = get_class($element);

        switch ($type) {
            case 'PhpOffice\PhpWord\Element\TextRun':
                $textRun = $newSection->addTextRun($element->getParagraphStyle());
                foreach ($element->getElements() as $childElement) {
                    if (method_exists($childElement, 'getText')) {
                        $textRun->addText($childElement->getText(), $childElement->getFontStyle(), $childElement->getParagraphStyle());
                    }
                }
                break;
            case 'PhpOffice\PhpWord\Element\Text':
                $newSection->addText($element->getText(), $element->getFontStyle(), $element->getParagraphStyle());
                break;
            case 'PhpOffice\PhpWord\Element\Title':
                $newSection->addTitle($element->getText(), $element->getDepth());
                break;
            case 'PhpOffice\PhpWord\Element\Image':
                $newSection->addImage($element->getSource(), $element->getStyle());
                break;
            case 'PhpOffice\PhpWord\Element\Link':
                $newSection->addLink($element->getSource(), $element->getText(), $element->getFontStyle(), $element->getParagraphStyle());
                break;
            case 'PhpOffice\PhpWord\Element\Table':
                $newTable = $newSection->addTable($element->getStyle());
                foreach ($element->getRows() as $row) {
                    $tableRow = $newTable->addRow();
                    foreach ($row->getCells() as $cell) {
                        $tableCell = $tableRow->addCell();
                        foreach ($cell->getElements() as $cellElement) {
                            $this->copyElement($tableCell, $cellElement);
                        }
                    }
                }
                break;
            default:
                // Handle other element types as needed
                break;
        }
    }

    public function dokumenTindakLanjut()
    {
        $active = 2;
        $posts = Post::whereNotNull('dokumen_tindak_lanjut')
            ->latest()
            ->paginate(10);

        // Tambahkan nama kegiatan untuk setiap post
        foreach ($posts as $post) {
            $namaKegiatan = JenisKegiatan::find($post->jenis)->jenis ?? '-';
            $post->jenis_kegiatan = $namaKegiatan;
        }

        return view('posts.dokumen_tindakLanjut', compact('active', 'posts'));
    }

    public function tambahTindakLanjut($id)
    {
        $active = 2;
        $posts = Post::find($id);
        return view('posts.tambahTindakLanjut', compact('active', 'posts'));
    }

    public function storeTindakLanjut(Request $request, $id)
    {
        $request->validate([
            'dokumen_tindak_lanjut' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $posts = Post::findOrFail($id);
        $currentTimestamp = now();
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;

        if ($request->hasFile('dokumen_tindak_lanjut')) {
            $jenisDokumen = 'Dokumen Tindak Lanjut';
            $extension = $request->file('dokumen_tindak_lanjut')->getClientOriginalExtension();

            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            $request->file('dokumen_tindak_lanjut')->move('dokumen_tindaklanjut/', $newFileName);
            $posts->judul_tindak_lanjut = 'Tindak Lanjut ' . $posts->judul;
            $posts->dokumen_tindak_lanjut = $newFileName;
            $posts->tindakLanjut_at = $currentTimestamp;
            $posts->save();
        }
        return redirect()->route('dokumenTindakLanjut')->with('success', 'Dokumen Tindak Lanjut berhasil ditambahkan');
    }

    public function uploadSertifikat(Request $request, $id)
    {
        $request->validate([
            'sertifikat' => 'required|file|mimes:pdf,jpg,jpeg,png,svg|max:10240',
        ]);

        $sertifikat = Sertifikat::find($id);
        // $currentTimestamp = now();
        $currentDate = date('dmY_His');
        $pengunggah = auth()->user()->name;

        if ($request->hasFile('sertifikat')) {
            $jenisDokumen = 'Sertifikat';
            $extension = $request->file('sertifikat')->getClientOriginalExtension();

            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            $request->file('sertifikat')->move('sertifikat/', $newFileName);
            $sertifikat->sertifikat = $newFileName;
            $sertifikat->save();
        }

        return redirect()->back()->with('success', 'Sertifikat berhasil diunggah.');
    }

    // public function rtm()
    // {
    //     $active = 2;
    //     $posts = Post::whereNotNull('rekomendasi')
    //         ->latest()
    //         ->paginate(10);
    //     $pic = Post::where('status_task', 'approved')->distinct('tanggungjawab')->get();
    //     return view('posts.RTM', compact('active', 'posts', 'pic'));
    // }

    public function rtm(Request $request)
    {
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

        $posts = $query->whereNotNull('dokumen_tindak_lanjut')->latest()->where('status_task', 'approved')->paginate(10);
        $pic = Post::where('status_task', 'approved')->distinct('tanggungjawab')->get();

        dd($posts);

        return view('posts.RTM', compact('active', 'posts', 'pic'));
    }

    public function searchRTM(Request $request)
    {
        $active = 4;
        $search = $request->input('search');
        $selectedYear = $request->input('year', date('Y')); // Ambil tahun dari inputan filter atau default ke tahun saat ini
        $user = Auth::user();

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
        $post = $query->whereNotNull('dokumen_tindak_lanjut')->latest()->paginate(10);

        // Ambil tahun yang tersedia
        $years = Post::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // dd($post);

        return view('post.RTM', compact('active', 'post', 'years', 'selectedYear'));
    }

    public function createRTM()
    {
        $active = 4;
        $unitKerja = UnitKerja::all();
        if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2 || auth()->user()->id_level == 3 || auth()->user()->id_level == 6) {
            $posts = Post::with('rtm')->whereNotNull('dokumen_tindak_lanjut')->get();
        } else {
            $posts = Post::with('rtm')->whereNotNull('dokumen_tindak_lanjut')->where('tanggungjawab', auth()->user()->name)->get();
        }

        return view('posts.createRTM', compact('active', 'posts', 'unitKerja'));
    }

    public function showRTM($id)
    {
        $post = Post::findOrFail($id);
        return response()->json([
            'temuan' => $post->temuan,
            'rekomendasi' => $post->rekomendasi,
            'rencanaTinJut' => $post->rencanaTinJut,
            'rencanaWaktuTinJut' => $post->rencanaWaktuTinJut,
            'status_rtm' => $post->status_rtm,
        ]);
    }

    // public function storeRTM(Request $request)
    // {
    //     $request->validate([
    //         'temuan' => 'sometimes|required|string',
    //         'rekomendasi' => 'sometimes|required|string',
    //         'rencanaTinJut' => 'sometimes|required|string',
    //         'rencanaWaktuTinJut' => 'sometimes|required|date',
    //         'status_rtm' => 'sometimes|required|string',
    //     ]);

    //     $post = Post::find($request->judul);

    //     if ($request->has('temuan')) {
    //         $post->temuan = $request->temuan;
    //     }
    //     if ($request->has('rekomendasi')) {
    //         $post->rekomendasi = $request->rekomendasi;
    //     }
    //     if ($request->has('rencanaTinJut')) {
    //         $post->rencanaTinJut = $request->rencanaTinJut;
    //     }
    //     if ($request->has('rencanaWaktuTinJut')) {
    //         $post->rencanaWaktuTinJut = $request->rencanaWaktuTinJut;
    //     }
    //     if ($request->has('status_rtm')) {
    //         $post->status_rtm = $request->status_rtm;
    //     }

    //     $post->save();
    //     return redirect()->route('rtm');
    // }

    public function storeRTM(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|exists:posts,id',
            'rtm.*.id' => 'required|exists:rtm,id',
            'rtm.*.temuan' => 'required',
            'rtm.*.rekomendasi' => 'required',
            'rtm.*.rencanaTinJut' => 'nullable',  // ubah sometimes jadi nullable
            'rtm.*.rencanaWaktuTinJut' => 'nullable|date', // ubah sometimes jadi nullable
            'rtm.*.pic' => 'nullable|array',  // ubah sometimes jadi nullable
            'rtm.*.status_rtm' => 'required',
        ]);

        foreach ($validatedData['rtm'] as $rtmData) {
            // Siapkan data untuk update
            $updateData = [
                // Hanya masukkan field jika ada nilainya
                'rencanaTinJut' => $rtmData['rencanaTinJut'] ?? null,
                'rencanaWaktuTinJut' => $rtmData['rencanaWaktuTinJut'] ?? null,
                'status_rtm' => $rtmData['status_rtm'] ?? null,
            ];

            // Update atau create RTM
            $rtm = RTM::updateOrCreate(
                ['id' => $rtmData['id']],
                $updateData
            );

            // Update PIC hanya jika ada data PIC
            if (!empty($rtmData['pic'])) {
                PIC_RTM::where('id_rtm', $rtm->id)->delete();
                foreach ($rtmData['pic'] as $pic) {
                    PIC_RTM::create([
                        'id_rtm' => $rtm->id,
                        'id_unit_kerja' => $pic
                    ]);
                }
            }
        }

        return redirect()->route('rtm');
    }

    public function exportRTMToWord(Request $request)
    {
        $query = Post::query();

        if ($request->has('search')) {
            $query->where('judul', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $posts = $query->whereNotNull('dokumen_tindak_lanjut')->latest()->get();

        $phpWord = new PhpWord();

        // Set default font
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        // Create section with landscape orientation dan margin yang lebih kecil
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginLeft' => 400,
            'marginRight' => 400,
            'marginTop' => 400,
            'marginBottom' => 400,
        ]);

        // Header text tanpa indent
        $section->addText('POLITEKNIK NEGERI MALANG', ['bold' => true, 'size' => 11], ['alignment' => 'left', 'spaceAfter' => 0]);
        $section->addText('LEMBAR MONITORING REKOMENDASI SPI TAHUN ' . ($request->year ?? date('Y')), ['bold' => true, 'size' => 11], ['alignment' => 'left', 'spaceAfter' => 100]);

        // Table style
        $tableStyle = [
            'borderSize' => 1,
            'borderColor' => '000000',
            'width' => 100,
            'unit' => 'pct',
            'cellMargin' => 50,
        ];

        $table = $section->addTable($tableStyle);

        // Header row dengan ukuran kolom yang disesuaikan
        $table->addRow(400);

        // Width disesuaikan dengan proporsi gambar
        $table->addCell(700, ['bgColor' => 'FFFFFF', 'valign' => 'center'])
            ->addText('NO', ['bold' => true], ['alignment' => 'center']);

        $table->addCell(2500, ['bgColor' => 'FFFFFF', 'valign' => 'center'])
            ->addText('KEGIATAN', ['bold' => true], ['alignment' => 'center']);

        $table->addCell(5550, ['bgColor' => 'FFFFFF', 'valign' => 'center'])
            ->addText('REKOMENDASI', ['bold' => true], ['alignment' => 'center']);

        $table->addCell(3200, ['bgColor' => 'FFFFFF', 'valign' => 'center'])
            ->addText('RENCANA TINDAK LANJUT', ['bold' => true], ['alignment' => 'center']);

        $table->addCell(2200, ['bgColor' => 'FFFFFF', 'valign' => 'center'])
            ->addText('RENCANA WAKTU TINDAK LANJUT', ['bold' => true], ['alignment' => 'center']);

        $table->addCell(1850, ['bgColor' => 'FFFFFF', 'valign' => 'center'])
            ->addText('STATUS', ['bold' => true], ['alignment' => 'center']);

        // Data rows
        $no = 1;
        foreach ($posts as $post) {
            $table->addRow();
            $table->addCell(700)->addText($no++, [], ['alignment' => 'center']);
            $table->addCell(2500)->addText($post->judul ?? '', [], ['alignment' => 'left']);

            // Cell rekomendasi dengan format nomor
            $rekCell = $table->addCell(5000);
            if ($post->rekomendasi) {
                $rekomendasi = explode("\n", $post->rekomendasi);
                foreach ($rekomendasi as $index => $rek) {
                    if (trim($rek)) {
                        $rekCell->addText(($index + 1) . '. ' . trim($rek), [], [
                            'alignment' => 'left',
                            'spacing' => 120,
                            'spaceAfter' => 0  // Mengurangi spasi antar paragraf
                        ]);
                    }
                }
            }

            $table->addCell(3000)->addText($post->rencanaTinJut ?? '', [], ['alignment' => 'left']);
            $table->addCell(2000)->addText(
                $post->rencanaWaktuTinJut ?
                    \Carbon\Carbon::parse($post->rencanaWaktuTinJut)->format('d F Y') :
                    '',
                [],
                ['alignment' => 'center']
            );
            $table->addCell(1500)->addText($post->status_rtm ?? '', [], ['alignment' => 'center']);
        }

        $filename = 'Monitoring_Rekomendasi_SPI_' . ($request->year ?? date('Y')) . '.docx';

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        $objWriter->save('php://output');
        exit;
    }
}
