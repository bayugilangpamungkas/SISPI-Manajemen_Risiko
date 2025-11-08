<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Post;
use App\Models\JenisKegiatan;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Shared\Validate;

class DokumenController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    // public function index()
    // {
    //     $active = 8;
    //     //get dokumens
    //     $dokumens = Dokumen::latest()->paginate(5);

    //     //render view with dokumens
    //     return view('dokumens.dokumen', compact('active', 'dokumens'))->with([
    //         'user' => Auth::user(),
    //     ]);
    // }

    public function index()
    {
        $active = 8;
        //get dokumens
        $dokumens = Post::where('id_unit_kerja', Auth::user()->id_unit_kerja)
                ->where('hasilReviu', '!=', null) // Contoh kondisi, jika ingin filter dokumen dengan `hasilReviu` yang tidak null
                ->orWhere('dokumen_tindak_lanjut', '!=', null)
                ->latest()
                ->paginate(5);
        // dd($dokumens);
        $jenisKegiatan = JenisKegiatan::all()->keyBy('id');

        //render view with dokumens
        return view('dokumens.dokumen', compact('active', 'dokumens', 'jenisKegiatan'))->with([
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        $post = Post::where('id_unit_kerja', Auth::user()->id_unit_kerja)->where('status_task', 'approved')->where('jenis', 2)->get();
        return view('dokumens.tambahDokumen')->with([
            'user' => Auth::user(),
            'post' => $post,
            'active' => 8,
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

        //validate form
        $this->validate($request, [
            'judul'     => 'required|min:1',
            'dokumen' => 'mimes:doc,docx,pdf',
        ]);

        //create dokumen
        // Dokumen::create([
        //     'judul'     => $request->judul,
        //     'jenis'     => $request->jenis,
        //     'dokumen'   => $request->dokumen,
        // ]);

        $dokumens = Post::find($request->judul);
        $currentDate = date('dmY_His');
        $currentTimestamp = now();
        $pengunggah = auth()->user()->name;
        // if ($request->hasFile('dokumen')) {
        //     $jenisDokumen = 'Dokumen Auditee';
        //     $extension = $request->file('dokumen')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('dokumen')->move('dokumen_auditee/', $newFileName);
        //     $dokumens->dokumen = $newFileName;
        //     $dokumens->save();
        // }
        // dd($request->dokumen);
        if ($request->hasFile('dokumen')) {
            $jenisDokumen = 'Hasil Dokumen Reviu';
            $extension = $request->file('dokumen')->getClientOriginalExtension();

            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;

            $request->file('dokumen')->move('hasil_reviu/', $newFileName);
            $dokumens->hasilReviu = $newFileName;
            $dokumens->hasilReviu_uploaded_at = $currentTimestamp;
        }

        $dokumens->save();

        //redirect to index
        return redirect()->route('dokumens.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    //tampil data
    public function tampilDataDokumen($id)
    {
        $active = 8;
        $dokumens = Post::find($id);
        $comments = Comment::with('user')->where('post_id', $dokumens->id)->get();
        //dd($dokumens);
        return view('dokumens.tampilEditDokumen', compact('active', 'dokumens', 'comments'));
    }

    //Edit Data
    public function updateDataDokumen(Request $request, $id)
    {
        // $dokumens = Dokumen::find($id);

        //validate form
        $this->validate($request,[
            'dokumen' => 'mimes:doc,docx,pdf',
        ]);

        //update dokumens
        $dokumens = Post::find($id);
        $currentDate = date('dmY_His');
        $currentTimestamp = now();
        $pengunggah = auth()->user()->name;
        // if ($request->hasFile('dokumen')) {
        //     $jenisDokumen = 'Dokumen Auditee';
        //     $extension = $request->file('dokumen')->getClientOriginalExtension();

        //     $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
        //     $request->file('dokumen')->move(public_path('dokumen_auditee'), $newFileName);
        //     $dokumens->dokumen = $newFileName;
        // }
        if ($request->hasFile('dokumen')) {
            $jenisDokumen = 'Hasil Dokumen Reviu';
            $extension = $request->file('dokumen')->getClientOriginalExtension();

            $newFileName = $currentDate . '_' . $pengunggah . '_' . $jenisDokumen . '.' . $extension;
            // dd($newFileName);

            $request->file('dokumen')->move('hasil_reviu/', $newFileName);
            $dokumens->hasilReviu = $newFileName;
            $dokumens->hasilReviu_uploaded_at = $currentTimestamp;
        }
        $dokumens->approvalReviuPIC = null;
        $dokumens->approvalReviu = null;
        $dokumens->commenter = null;
        $dokumens->commentReviu = null;
        $dokumens->koreksiReviu = null;
        $dokumens->koreksiReviuPIC = null;
        $dokumens->save();

        return redirect()->route('dokumens.index')->with('success', 'Data Berhasil Diupdate!');
    }

    //Hapus Delete Dokumen
    public function destroy($id)
    {
        $dokumens = Post::findOrFail($id);
        $dokumens->hasilReviu = null;
        $dokumens->hasilReviu_uploaded_at = null;
        $dokumens->approvalReviu = null;
        $dokumens->approvalReviu_at = null;
        $dokumens->save();

        return redirect()->route('dokumens.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
    //Searching
    public function show(Request $request)
    {
        $active = 8;
        if ($request->has('search')) {
            $dokumens = Dokumen::where('judul', 'LIKE', '%' . $request->search . '%')->get();
        } else {
            $dokumens = Dokumen::all();
        }

        return view('dokumens.Dokumen', ['dokumens' => $dokumens, 'active' => $active]);
    }
    public function search(Request $request)
    {
        $active = 8;
        $search = $request->input('search');
        $dokumens = Dokumen::where('judul', 'like', '%' . $search . '%')
            ->orWhere('jenis', 'like', '%' . $search . '%')
            ->paginate(10);

        return view('dokumens.dokumen', ['dokumens' => $dokumens, 'active' => $active]);
    }

    public function download($id)
    {
        $dokumens = Dokumen::findOrFail($id);
        $filePath = public_path('dokumen/' . $dokumens->dokumen);

        // Verifikasi bahwa file ada sebelum mencoba mengunduh
        if (Dokumen::exists($filePath)) {
            return response()->download($filePath, $dokumens->dokumen);
        } else {
            // Redirect atau tampilkan pesan kesalahan jika file tidak ditemukan
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }
    }
}
