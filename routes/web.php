<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuditeController;
use App\Http\Controllers\BobotController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\JenisKegiatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KelolaController;
use App\Http\Controllers\KesimpulanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MRController;
use App\Http\Controllers\PengumpulanController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TemplateDokumenController;
use App\Http\Controllers\TindakLanjutController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\RTMController;
use App\Http\Controllers\ImportedExcelController;
use App\Http\Controllers\BeritaAcaraController;
use App\Models\BeritaAcara;
use App\Models\Post;
use App\Models\UnitKerja;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route Login
//Route::get('login',     [LoginController::class, 'index'])->name('login');
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::post('login/proses', 'proses');
    Route::get('logout', 'logout')->name('logout');
    Route::get('register', 'create')->name('register');
    Route::post('register', 'store')->name('register.store');
    Route::get('manualbook', 'manualbook')->name('manualbook');
});


//Route Admin Menu Setting
Route::resource('/admin/panel', MenuController::class)->middleware(['auth', 'cekUserLogin']);
Route::post('/admin/panel/head-menu', [MenuController::class, 'storeHead'])->middleware(['auth', 'cekUserLogin']);
Route::put('/admin/panel/{id}/head-menu', [MenuController::class, 'editHead'])->middleware(['auth', 'cekUserLogin']);
Route::delete('/admin/panel/{id}/head-menu', [MenuController::class, 'removeHead'])->middleware(['auth', 'cekUserLogin']);
Route::post('/admin/panel/{id}/menu', [MenuController::class, 'addMenu'])->middleware(['auth', 'cekUserLogin']);
Route::delete('/admin/panel/{id}/menu', [MenuController::class, 'removeMenu'])->middleware(['auth', 'cekUserLogin']);

//Route Unit Kerja
Route::resource('/unit-kerja',   UnitKerjaController::class)->middleware(['auth', 'cekUserLogin']);

//Route Kegiatan Controller
Route::post('/kegiatan/import', [KegiatanController::class, 'import'])->name('kegiatan.import')->middleware('auth');
Route::delete('/kegiatan/delete-by-year', [KegiatanController::class, 'deleteByYear'])->name('kegiatan.deleteByYear')->middleware('auth');
Route::resource('/kegiatan', KegiatanController::class)->middleware('auth');

//Route CRUD Post Controller
Route::resource('/posts', PostController::class)->middleware(['auth', 'approved']);
Route::get('/laporanAkhir',    [PostController::class, 'laporanAkhir'])->name('laporanAkhir')
    ->middleware('auth');
Route::get('/reviewKetua',    [PostController::class, 'reviewKetua'])->name('reviewKetua')
    ->middleware('auth');
Route::resource('/tindak-lanjut', TindakLanjutController::class)->middleware('auth');
Route::put('/tindak-lanjut', [TindakLanjutController::class, 'storeRekomendasi'])->name('tindak-lanjut.storeRekomendasi')
    ->middleware('auth');
Route::get('/tindak-lanjut/search', [TindakLanjutController::class, 'search'])->name('tindak-lanjut.search')
    ->middleware('auth');
Route::post('/tindak-lanjut/export', [TindakLanjutController::class, 'exportExcel'])->name('tindak-lanjut.export-excel')
    ->middleware('auth');
// Route untuk approve dan disapprove tugas
Route::post('/posts/{id}/approve_task', [PostController::class, 'approve_task'])->name('posts.approve_task')
    ->middleware('auth', 'cekUserLogin');
Route::post('/posts/{id}/disapprove_task', [PostController::class, 'disapprove_task'])->name('posts.disapprove_task')
    ->middleware('auth', 'cekUserLogin');
Route::get('/tampilData/{id}', [PostController::class, 'tampilData'])->name('tampilData')
    ->middleware('auth');
Route::patch('/updateData/{id}', [PostController::class, 'updateData'])->name('updateData')
    ->middleware('auth');
Route::get('/detailTugas/{id}',      [PostController::class, 'detailTugas'])->name('detailTugas')
    ->middleware('auth');
Route::get('/detailTugasKetua/{id}',   [PostController::class, 'detailTugasKetua'])->name('detailTugasKetua')
    ->middleware('auth', 'cekUserLogin');
Route::post('/posts/{id}/approve/{type}',   [PostController::class, 'approve'])->name('posts.approve')
    ->middleware('auth', 'cekUserLogin');
Route::post('/posts/{id}/disapprove/{type}', [PostController::class, 'disapprove'])->name('posts.disapprove')
    ->middleware('auth', 'cekUserLogin');
Route::post('/posts/{id}/approvePIC',   [PostController::class, 'approvePIC'])->name('posts.approvePIC')
    ->middleware('auth');
Route::post('/posts/{id}/disapprovePIC/{type}', [PostController::class, 'disapprovePIC'])->name('posts.disapprovePIC')
    ->middleware('auth');
Route::get('/detailTugas/print/{id}',  [PostController::class, 'printDetailTugas'])->name('printDetailTugas')
    ->middleware('auth');
Route::delete('/posts/{id}',   [PostController::class, 'destroy'])->name('destroy')->middleware('auth');
Route::post('/detailTugas/{id}/submit', [PostController::class, 'submit'])->middleware('auth');
Route::post('/detailTugasKetua/{id}/koreksi_ketua', [PostController::class, 'koreksi_ketua'])->middleware('auth');
Route::post('/detailTugas/{id}/submit_akhir', [PostController::class, 'submit_akhir'])->middleware('auth');
Route::post('/uploadSuratTugas/{id}', [PostController::class, 'uploadSuratTugas'])->name('uploadSuratTugas')
    ->middleware('auth');
Route::post('/uploadSertifikat/{id}', [PostController::class, 'uploadSertifikat'])->name('uploadSertifikat')
    ->middleware('auth');
Route::get('/reviewLaporan/print',   [PostController::class, 'print'])->middleware('auth');
//  Route::get('/detailTugas/print/{id}',   [PostController::class,'print_id'])->middleware('auth');
Route::get('/reviewLaporan/printpdf',   [PostController::class, 'printpdf'])->middleware('auth');
// Menampilkan form komentar
Route::get('/posts/{id}/comment/{type}', [PostController::class, 'showCommentForm'])->name('posts.comment');
// Menyimpan komentar
Route::post('/posts/{id}/comment/{type}', [PostController::class, 'postComment'])->name('posts.comment.store');
Route::get('/tambahTindakLanjut/{id}', [PostController::class, 'tambahTindakLanjut'])->name('tambahTindakLanjut')
    ->middleware('auth');
Route::post('/tambahTindakLanjut/store/{id}', [PostController::class, 'storeTindakLanjut'])->name('storeTindakLanjut')
    ->middleware('auth');
Route::get('/dokumen-tindak-lanjut', [PostController::class, 'dokumenTindakLanjut'])->name('dokumenTindakLanjut')
    ->middleware('auth');
Route::get('/rtm', [RTMController::class, 'index'])->name('rtm')
    ->middleware('auth');
Route::get('/rtm/search', [PostController::class, 'searchRTM'])->name('rtm.search')
    ->middleware('auth');
Route::get('/rtm/create', [PostController::class, 'createRTM'])->name('rtm.create')
    ->middleware('auth');
Route::get('/rtm/show/{id}', [PostController::class, 'showRTM'])->name('rtm.show')
    ->middleware('auth');
Route::get('/get-rtm/{postId}', [TindakLanjutController::class, 'getRTM']);
Route::put('/store-rtm', [PostController::class, 'storeRTM'])->name('rtm.store')
    ->middleware('auth');
Route::get('/rtm/export', [PostController::class, 'exportRTMToWord'])->name('rtm.export')
    ->middleware('auth');
Route::get('/rtm/export-excel', [RTMController::class, 'exportExcel'])->name('rtm.export-excel')
    ->middleware('auth');

//Route CRUD Peta Controller
Route::resource('/petas', PetaController::class)->middleware(['auth', 'approved']);
Route::post('/uploadDokumen/{id}', [PetaController::class, 'uploadDokumen'])->name('uploadDokumen')
    ->middleware('auth');
Route::get('/peta-risiko/detail/{jenis}', [PetaController::class, 'detailByJenis'])->name('petaRisikoDetail')
    ->middleware('auth');
Route::post('/upload-dokumen/{jenis}', [PetaController::class, 'uploadDokumenByJenis'])->name('uploadDokumenByJenis')
    ->middleware('auth');
Route::post('/updateDataByJenis/{jenis}', [PetaController::class, 'updateData'])->name('updateDataByJenis')
    ->middleware('auth');
Route::get('/peta-risiko/tugas/{jenis}', [PetaController::class, 'tugas'])->name('petas.tugas')
    ->middleware('auth');
Route::post('/peta-risiko/tambahtugas/{jenis}', [PetaController::class, 'tambahtugas'])->name('petas.tambahtugas')
    ->middleware('auth');

// Route::get('/petas/{id}/tugas', [PetaController::class, 'tugas'])->name('petas.tugas')
//     ->middleware('auth');
Route::get('/petas-tabel', [PetaController::class, 'tabelMatrik'])->name('petas.tabel')
    ->middleware('auth');
Route::get('/petas/tabel-unit-kerja/{unitKerja}', [PetaController::class, 'tabelUnitKerja'])->name('petas.tabelUnitKerja')
    ->middleware('auth');
// Route::post('/petas/{id}/tambahtugas', [PetaController::class, 'tambahtugas'])->name('petas.tambahtugas')
//     ->middleware('auth');
Route::get('/detailPR/{id}', [PetaController::class, 'detailPR'])->name('detailPR')
    ->middleware('auth');
// Route::get('/tampilData/{id}', [PetaController::class, 'tampilData'])->name('tampilData')
//     ->middleware('auth');
// Route::post('/updateData/{id}', [PetaController::class, 'updateData'])->name('updateData')
//     ->middleware('auth');
Route::delete('/petas/{id}',   [PetaController::class, 'destroy'])->name('destroy')->middleware('auth');
Route::post('/petas/{id}/approve',   [PetaController::class, 'approve'])->name('petas.approve')
    ->middleware('auth');
Route::post('/petas/{id}/disapprove', [PetaController::class, 'disapprove'])->name('petas.disapprove')
    ->middleware('auth');
Route::get('/detailPR/{id}', [PetaController::class, 'detailPR'])->name('detailPR')
    ->middleware('auth');
Route::post('/detailPR/{id}/comment', [PetaController::class, 'postComment'])->name('postComment')
    ->middleware('auth');
Route::post('/tambah-tugas-ketua/{jenis}', [PetaController::class, 'tambahTugasKetua'])->name('tambahTugasKetua')->middleware('auth');
Route::get('/tambah-anggota/{jenis}', [PetaController::class, 'tambahAnggota'])->name('tambahAnggota')->middleware('auth');
Route::post('/store-anggota/{jenis}', [PetaController::class, 'storeAnggota'])->name('storeAnggota')->middleware('auth');
Route::get('/peta/export-excel', [PetaController::class, 'exportExcelPR'])->name('peta.export-excel')
    ->middleware('auth');
Route::get('/peta/export-excel/jenis', [PetaController::class, 'exportExcelPRJenis'])->name('peta.export-excel-jenis')
    ->middleware('auth');
Route::post('/peta/import', [PetaController::class, 'import'])->name('peta.import')->middleware('auth');
Route::get('/peta/penelaah', [PetaController::class, 'penelaahPeta'])->name('peta.penelaah')->middleware('auth');
Route::put('/peta/penelaah', [PetaController::class, 'updatePenelaahPeta'])->name('peta.update-penelaah');
Route::resource('/imported-excel', ImportedExcelController::class)->middleware('auth');


//Route CRUD User
Route::resource('/users', UserController::class)->middleware('auth');
Route::post('/users/tambah', [UserController::class, 'storeFromAdmin'])->name('users.tambah')
    ->middleware('auth');
Route::get('/tampilDataUser/{id}', [UserController::class, 'tampilDataUser'])->name('tampilDataUser')
    ->middleware('auth');
Route::post('/updateDataUser/{id}', [UserController::class, 'updateDataUser'])->name('updateDataUser')
    ->middleware('auth');
Route::delete('/users/{id}',   [UserController::class, 'destroy'])->name('users.destroy')->middleware('auth');
//Route Profile
Route::get('/profileDataUser/{id}', [UserController::class, 'profileDataUser'])->name('profileDataUser')
    ->middleware('auth');
Route::post('/profile/{id}/update', [UserController::class, 'updateProfile'])->name('profile.update')
    ->middleware('auth');
Route::post('/users/{id}/approve', [UserController::class, 'approveUser'])->name('users.approve')
    ->middleware('auth');
Route::post('/users/{id}/disapprove', [UserController::class, 'disapproveUser'])->name('users.disapprove')
    ->middleware('auth');

//Route CRUD Dokumen
Route::resource('/dokumens', DokumenController::class)->middleware(['auth', 'approved']);
Route::get('/tampilDataDokumen/{id}', [DokumenController::class, 'tampilDataDokumen'])->name('tampilDataDokumen')
    ->middleware('auth');
Route::post('/updateDataDokumen/{id}', [DokumenController::class, 'updateDataDokumen'])->name('updateDataDokumen')
    ->middleware('auth');
Route::get('dokumen/download/{id}', [DokumenController::class, 'download'])->name('download.dokumen')
    ->middleware('auth');
Route::delete('/dokumens/{id}',   [DokumenController::class, 'destroy'])->name('dokumens.destroy')->middleware('auth');

// Route Berita Acara Minutes Management
Route::resource('/berita-acara', BeritaAcaraController::class)->middleware(['auth', 'approved']);
Route::delete('/berita-acara-documents/{document}', [BeritaAcaraController::class, 'destroyDocument'])
    ->name('berita-acara.documents.destroy')
    ->middleware(['auth', 'approved']);
Route::delete('/berita-acara-images/{image}', [BeritaAcaraController::class, 'destroyImage'])
    ->name('berita-acara.images.destroy')
    ->middleware(['auth', 'approved']);


//Route View
Route::get('/dashboard',               [ProjectController::class, 'dashboard'])->middleware(['auth', 'approved']);
Route::get('/template',               [ProjectController::class, 'template']);
Route::get('/',                        [ProjectController::class, 'dashboard'])->middleware(['auth', 'approved']);

//Route Search
Route::get('/reviewLaporan/search',     [ProjectController::class, 'search'])->middleware('auth');
Route::get('/reviewLaporanKetua/searchKetua', [ProjectController::class, 'searchKetua'])->middleware('auth');
Route::get('/laporanAkhir/searchAkhir',     [ProjectController::class, 'searchAkhir'])->middleware('auth');
Route::get('/petaRisiko/search',     [PetaController::class, 'searchPetaRisiko'])->middleware('auth')->name('petaRisiko.search');
Route::get('/tindakLanjut/search',     [ProjectController::class, 'searchTindakLanjut'])->middleware('auth');
Route::get('/userView/search',     [UserController::class, 'search'])->middleware('auth');
Route::get('/dokumen/search',           [DokumenController::class, 'search'])->middleware('auth');

//Route Verifikasi Email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//Route Email Verification Handler
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

//Route setelah verif
Route::get('/dashboard', [ProjectController::class, 'dashboard'])->middleware(['auth', 'verified', 'approved']);

//Route kirim ulang verifikasi email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Email verifikasi telah dikirim ulang!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


//Feedback
Route::get('/feedback', [ProjectController::class, 'feedback'])->middleware('auth');
Route::get('/feedback_web', [ProjectController::class, 'feedback_web']);

//template
Route::get('/welcome', function () {
    $beritaAcaras = BeritaAcara::with(['documents', 'images'])
        ->orderByDesc('meeting_date')
        ->orderByDesc('created_at')
        ->take(3)
        ->get();

    $beritaAcaraCount = BeritaAcara::count();
    $moreMinutesExist = $beritaAcaraCount > $beritaAcaras->count();

    $totalAudits = Post::count();
    $completedAudits = Post::where('status_task', 'approved')->count();
    $unitKerjaCount = UnitKerja::count();

    $completionRate = $totalAudits > 0
        ? round(($completedAudits / $totalAudits) * 100)
        : null;

    $formatValue = static function (int $value): string {
        if ($value >= 1000) {
            return number_format($value);
        }

        return (string) $value;
    };

    $welcomeStats = [
        [
            'display' => $formatValue($totalAudits),
            'label' => 'Total Audit',
        ],
        [
            'display' => $formatValue($completedAudits),
            'label' => 'Audit Selesai',
        ],
        [
            'display' => $completionRate !== null ? $completionRate . '%' : '0%',
            'label' => 'Tingkat Penyelesaian',
        ],
        [
            'display' => $formatValue($beritaAcaraCount),
            'label' => 'Berita Acara',
        ],
    ];

    return view('welcome', compact('beritaAcaras', 'moreMinutesExist', 'welcomeStats'));
});

Route::get('/welcome/berita-acara', function () {
    $minutes = BeritaAcara::with(['documents', 'images'])
        ->orderByDesc('meeting_date')
        ->orderByDesc('created_at')
        ->paginate(9);

    return view('welcome-berita-acara', compact('minutes'));
})->name('welcome.berita-acara');

Route::middleware(['auth'])->group(function () {
    //MR Anggota
    Route::get('/entitas', [KelolaController::class, 'index'])->name('entitas.index');
    Route::put('/entitas/{id}', [KelolaController::class, 'update'])->name('entitas.update');
    Route::post('/entitas/store', [KelolaController::class, 'jawabanStore'])->name('jawaban.store');
    Route::post('/simpulan/update', [KelolaController::class, 'simpulanUpdate'])->name('simpulan.update');
    Route::get('/export-excel/{topic_id}', [KelolaController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/{sub_element_id}', [KelolaController::class, 'exportSubElemenExcel'])->name('exportSubElemen.excel');

    //MR Blade
    Route::resource('/MR', MRController::class, ['parameters' => ['MR' => 'id']])->middleware('auth');
    Route::put('/MR/sub-elemen/{id}', [MRController::class, 'subElemenUpdate'])->name('subElemen.update');
    Route::post('MR/sub-elemen', [MRController::class, 'subElemenStore'])
        ->name('subElemen.store');
    Route::post('/MR/uraians', [MRController::class, 'uraianStore'])->name('uraian.store');
    Route::post('/MR/topics', [MRController::class, 'topicStore'])->name('topic.store');
    Route::delete('/MR/topic/{id}', [MRController::class, 'topicDestroy'])->name('topic.destroy');
    Route::put('/MR/topic/{id}', [MRController::class, 'topicUpdate'])->name('topic.update');
    Route::put('/uraian/{id}', [MRController::class, 'uraianUpdate'])->name('uraian.update');
    Route::delete('/MR/uraian/{id}', [MRController::class, 'uraianDestroy'])->name('uraian.destroy');
    Route::delete('/MR/sub-elemen/{id}', [MRController::class, 'subElemenDestroy'])->name('subElemen.destroy');
    Route::get('/api/sub-elements/{elementId}', [MRController::class, 'getSubElements']);

    //Bobot Blade
    Route::get('/bobot', [BobotController::class, 'index'])->name('bobot.index');
    Route::put('/bobot/update', [BobotController::class, 'update'])->name('bobot.update');

    //Validasi Blade
    Route::resource('/verif', ValidasiController::class);
    Route::patch('/verify/jawaban/{id}', [ValidasiController::class, 'verify'])->name('jawaban.verify');

    //Kesimpulan Blade
    Route::get('/kesimpulan', [KesimpulanController::class, 'index'])->name('kesimpulan.index');
});

// Route CRUD Jenis Template
Route::resource('/jenis-template', JenisKegiatanController::class)->middleware('auth');
Route::get('/jenis-template/search', [JenisKegiatanController::class, 'search'])->middleware('auth')->name('jenisTemplate.search');


// Route CRUD Template Dokumen
Route::resource('/template-dokumen', TemplateDokumenController::class)->middleware('auth');
Route::get('/template-dokumen/search', [TemplateDokumenController::class, 'search'])->middleware('auth')->name('templateDokumen.search');
