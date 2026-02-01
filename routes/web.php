<?php

use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuditeeController;
use App\Http\Controllers\AuditorController;
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
use App\Http\Controllers\WelcomeBeritaAcaraController;
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

Route::get('/welcome/berita-acara', WelcomeBeritaAcaraController::class)->name('welcome.berita-acara');

/*
|--------------------------------------------------------------------------
| PETA RISIKO ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    /* ======================
     |  Peta Risiko - View
     ====================== */
    Route::get('/petas/search', [PetaController::class, 'searchPetaRisiko'])
        ->name('petaRisiko.search');

    Route::get('/petas/tabel', [PetaController::class, 'tabelMatrik'])
        ->name('petas.tabel');

    Route::get('/petas/tabel/{unitKerja}', [PetaController::class, 'tabelUnitKerja'])
        ->name('petas.tabelUnitKerja');

    Route::get('/petas/detail/{jenis}', [PetaController::class, 'detailByJenis'])
        ->name('petaRisikoDetail');

    Route::get('/petas/{id}/detail', [PetaController::class, 'detailPR'])
        ->name('petas.detailPR');
    /* ======================
     |  Peta Risiko - Tugas & Anggota
     ====================== */
    Route::get('/petas/tugas/{jenis}', [PetaController::class, 'tugas'])
        ->name('petas.tugas');

    Route::post('/petas/tambahtugas/{jenis}', [PetaController::class, 'tambahtugas'])
        ->name('petas.tambahtugas');

    Route::post('/tambah-tugas-ketua/{jenis}', [PetaController::class, 'tambahTugasKetua'])
        ->name('tambahTugasKetua');

    Route::get('/tambah-anggota/{jenis}', [PetaController::class, 'tambahAnggota'])
        ->name('tambahAnggota');

    Route::post('/store-anggota/{jenis}', [PetaController::class, 'storeAnggota'])
        ->name('storeAnggota');
    /* ======================
     |  Peta Risiko - Dokumen & Update
     ====================== */
    Route::post('/petas/upload/{jenis}', [PetaController::class, 'uploadDokumenByJenis'])
        ->name('petas.uploadDokumen');

    Route::patch('/petas/update-data/{jenis}', [PetaController::class, 'updateData'])
        ->name('petas.updateData');
    /* ======================
     |  Approval & Comment
     ====================== */
    Route::post('/petas/{id}/approve', [PetaController::class, 'approve'])
        ->name('petas.approve');

    Route::post('/petas/{id}/disapprove', [PetaController::class, 'disapprove'])
        ->name('petas.disapprove');

    Route::post('/detailPR/{id}/comment', [PetaController::class, 'postComment'])
        ->name('postComment');
    /* ======================
     |  Export, Import, Penelaah
     ====================== */
    Route::get('/peta/export-excel', [PetaController::class, 'exportExcelPR'])
        ->name('peta.export-excel');
    Route::get('/peta/export-excel/jenis', [PetaController::class, 'exportExcelPRJenis'])
        ->name('peta.export-excel-jenis');
    Route::post('/peta/import', [PetaController::class, 'import'])
        ->name('peta.import');
    Route::get('/peta/penelaah', [PetaController::class, 'penelaahPeta'])
        ->name('peta.penelaah');
    Route::put('/peta/penelaah', [PetaController::class, 'updatePenelaahPeta'])
        ->name('peta.update-penelaah');
    /* ======================
     |  Resource
     ====================== */
    Route::resource('/petas', PetaController::class);
    Route::resource('/imported-excel', ImportedExcelController::class);
});

/*
|--------------------------------------------------------------------------
| WELCOME / PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/welcome/berita-acara', WelcomeBeritaAcaraController::class)
    ->name('welcome.berita-acara');



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
$welcomePage = function () {
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
            'display' => $formatValue($unitKerjaCount),
            'label' => 'Unit Kerja',
        ],
        [
            'display' => $formatValue($beritaAcaraCount),
            'label' => 'Berita Acara',
        ],
    ];

    return view('welcome', compact('beritaAcaras', 'moreMinutesExist', 'welcomeStats'));
};

Route::get('/welcome', $welcomePage)->name('welcome');
Route::get('/', $welcomePage);

Route::get('/welcome/berita-acara', function (Request $request) {
    $search = $request->input('search');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $minutesQuery = BeritaAcara::with(['documents', 'images'])
        ->when($search, function ($query, $term) {
            $query->where(function ($inner) use ($term) {
                $inner->where('title', 'like', "%{$term}%")
                    ->orWhere('summary', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        })
        ->when($startDate, function ($query, $from) {
            $query->whereDate('meeting_date', '>=', $from);
        })
        ->when($endDate, function ($query, $to) {
            $query->whereDate('meeting_date', '<=', $to);
        })
        ->orderByDesc('meeting_date')
        ->orderByDesc('created_at');

    $minutes = $minutesQuery
        ->paginate(9)
        ->appends($request->query());

    return view('welcome-berita-acara', [
        'minutes' => $minutes,
        'filters' => [
            'search' => $search,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ],
    ]);
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

/*
|--------------------------------------------------------------------------
| MANAJEMEN RISIKO ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    /* ======================
     |  Manajemen Risiko - Admin Only
     ====================== */
    // Data Manajemen Risiko - Halaman seleksi clustering
    Route::get('/manajemen-risiko/data', [App\Http\Controllers\ManajemenRisikoController::class, 'dataManajemenRisiko'])
        ->name('manajemen-risiko.data');
    // Update tampil manajemen risiko (centang data)
    Route::post('/manajemen-risiko/data/update-tampil', [App\Http\Controllers\ManajemenRisikoController::class, 'updateTampilManajemenRisiko'])
        ->name('manajemen-risiko.update-tampil');
    // Sembunyikan data dari manajemen risiko
    Route::post('/manajemen-risiko/data/hide-tampil', [App\Http\Controllers\ManajemenRisikoController::class, 'hideTampilManajemenRisiko'])
        ->name('manajemen-risiko.hide-tampil');
    // Detail Unit Kerja - Rincian Kegiatan per Unit
    Route::get('/manajemen-risiko/detail-unit/{unitKerja}', [App\Http\Controllers\ManajemenRisikoController::class, 'detailUnitKerja'])
        ->name('manajemen-risiko.detail-unit');
    // Tampilkan kegiatan terpilih di Manajemen Risiko
    Route::post('/manajemen-risiko/tampilkan-kegiatan', [App\Http\Controllers\ManajemenRisikoController::class, 'tampilkanKegiatan'])
        ->name('manajemen-risiko.tampilkan-kegiatan')
        ->middleware(['auth']);
    // Detail Risiko per Kegiatan (AJAX untuk Modal)
    Route::get('/manajemen-risiko/detail-unit/{unitKerja}/kegiatan/{kegiatanId}', [App\Http\Controllers\ManajemenRisikoController::class, 'detailRisikoKegiatan'])
        ->name('manajemen-risiko.detail-risiko-kegiatan');
    // Download template PDF
    Route::get('/manajemen-risiko/template/pdf', [App\Http\Controllers\ManajemenRisikoController::class, 'downloadTemplatePDF'])
        ->name('manajemen-risiko.template.pdf');
    // Download template Excel
    Route::get('/manajemen-risiko/template/excel', [App\Http\Controllers\ManajemenRisikoController::class, 'downloadTemplateExcel'])
        ->name('manajemen-risiko.template.excel');

    /* ======================
     |  Manajemen Risiko - Auditee
     ====================== */
    // Route Manajemen Risiko - Auditee (Unit Kerja)
    Route::get('/auditee/manajemen-risiko', [App\Http\Controllers\AuditeeController::class, 'auditeeIndex'])->name('manajemen-risiko.auditee.index')->middleware('auth');
    Route::post('/auditee/manajemen-risiko/upload', [App\Http\Controllers\AuditeeController::class, 'auditeeUpload'])->name('manajemen-risiko.auditee.upload')->middleware('auth');
    Route::get('/auditee/manajemen-risiko/download-template', [App\Http\Controllers\AuditeeController::class, 'auditeeDownloadTemplate'])->name('manajemen-risiko.auditee.download-template')->middleware('auth');
    Route::get('/auditee/manajemen-risiko/export/excel', [App\Http\Controllers\AuditeeController::class, 'auditeeExport'])->name('manajemen-risiko.auditee.export')->middleware('auth');
    Route::get('/auditee/manajemen-risiko/{id}/edit', [App\Http\Controllers\AuditeeController::class, 'auditeeEdit'])->name('manajemen-risiko.auditee.edit')->middleware('auth');
    Route::put('/auditee/manajemen-risiko/{id}/update', [App\Http\Controllers\AuditeeController::class, 'auditeeUpdate'])->name('manajemen-risiko.auditee.update')->middleware('auth');
    Route::post('/auditee/manajemen-risiko/{id}/submit', [App\Http\Controllers\AuditeeController::class, 'auditeeSubmit'])->name('manajemen-risiko.auditee.submit')->middleware('auth');
    // Detail Auditee dengan Form Tindak Lanjut
    Route::get('/auditee/manajemen-risiko/{id}/detail', [App\Http\Controllers\AuditeeController::class, 'auditeeShowDetail'])->name('manajemen-risiko.auditee.show-detail')->middleware('auth');
    // Submit Response Auditee
    Route::put('/auditee/manajemen-risiko/{id}/submit-response', [App\Http\Controllers\AuditeeController::class, 'auditeeSubmitResponse'])->name('manajemen-risiko.auditee.submit-response')->middleware('auth');
    Route::get('/auditee/manajemen-risiko/{id}', [App\Http\Controllers\AuditeeController::class, 'auditeeShow'])->name('manajemen-risiko.auditee.show')->middleware('auth');

    /* ======================
|  Manajemen Risiko - Auditor
====================== */
    // Route Manajemen Risiko - Auditor (Ketua, Anggota, Sekretaris)
    Route::get('/auditor/manajemen-risiko', [App\Http\Controllers\AuditorController::class, 'auditorIndex'])->name('manajemen-risiko.auditor.index')->middleware('auth');
    Route::get('/auditor/manajemen-risiko/generate/report', [App\Http\Controllers\AuditorController::class, 'auditorGenerateReport'])->name('manajemen-risiko.auditor.generate-report')->middleware('auth');
    Route::get('/auditor/manajemen-risiko/export/excel', [App\Http\Controllers\AuditorController::class, 'auditorExport'])->name('manajemen-risiko.auditor.export')->middleware('auth');
    // Detail Auditor dengan Template Form
    Route::get('/auditor/manajemen-risiko/{id}/detail', [App\Http\Controllers\AuditorController::class, 'auditorShowDetail'])->name('manajemen-risiko.auditor.show-detail')->middleware('auth');
    // Update audit form dan simpan ke database
    Route::put('/auditor/manajemen-risiko/{id}/update-template', [App\Http\Controllers\AuditorController::class, 'auditorUpdateTemplate'])->name('manajemen-risiko.auditor.update-template')->middleware('auth');
    // Route Pendukung Lainnya
    Route::post('/auditor/manajemen-risiko/{id}/send-template', [App\Http\Controllers\AuditorController::class, 'auditorSendTemplate'])->name('manajemen-risiko.auditor.send-template')->middleware('auth');
    Route::post('/auditor/manajemen-risiko/{id}/approve', [App\Http\Controllers\AuditorController::class, 'auditorApprove'])->name('manajemen-risiko.auditor.approve')->middleware('auth');
    Route::post('/auditor/manajemen-risiko/{id}/reject', [App\Http\Controllers\AuditorController::class, 'auditorReject'])->name('manajemen-risiko.auditor.reject')->middleware('auth');
    Route::post('/auditor/manajemen-risiko/{id}/upload-report', [App\Http\Controllers\AuditorController::class, 'auditorUploadReport'])->name('manajemen-risiko.auditor.upload-report')->middleware('auth');
    Route::get('/auditor/manajemen-risiko/{id}', [App\Http\Controllers\AuditorController::class, 'auditorShow'])->name('manajemen-risiko.auditor.show')->middleware('auth');

    /* ======================
     |  Manajemen Risiko - Admin
     ====================== */
    // Route Manajemen Risiko - Admin (Super Admin, Admin)
    Route::get('/manajemen-risiko', [App\Http\Controllers\ManajemenRisikoController::class, 'index'])->name('manajemen-risiko.index')->middleware('auth');
    Route::get('/manajemen-risiko/generate/report', [App\Http\Controllers\ManajemenRisikoController::class, 'generateReport'])->name('manajemen-risiko.generate-report')->middleware('auth');
    Route::get('/manajemen-risiko/export/excel', [App\Http\Controllers\ManajemenRisikoController::class, 'export'])->name('manajemen-risiko.export')->middleware('auth');
    Route::post('/manajemen-risiko/{id}/comment', [App\Http\Controllers\ManajemenRisikoController::class, 'comment'])->name('manajemen-risiko.comment')->middleware('auth');
    Route::put('/manajemen-risiko/{id}/update-status', [App\Http\Controllers\ManajemenRisikoController::class, 'updateStatus'])->name('manajemen-risiko.update-status')->middleware('auth');
    Route::post('/manajemen-risiko/{id}/assign-auditor', [App\Http\Controllers\ManajemenRisikoController::class, 'assignAuditor'])->name('manajemen-risiko.assign-auditor')->middleware('auth');
    Route::post('/manajemen-risiko/{id}/upload-report', [App\Http\Controllers\ManajemenRisikoController::class, 'uploadReport'])->name('manajemen-risiko.upload-report')->middleware('auth');

    // Hasil Audit Routes (Admin)
    Route::get('/manajemen-risiko/hasil-audit', [App\Http\Controllers\ManajemenRisikoController::class, 'hasilAuditIndex'])->name('manajemen-risiko.hasil-audit.index')->middleware('auth');
    Route::get('/manajemen-risiko/hasil-audit/{id}', [App\Http\Controllers\ManajemenRisikoController::class, 'hasilAuditShow'])->name('manajemen-risiko.hasil-audit.show')->middleware('auth');
    Route::get('/manajemen-risiko/hasil-audit/{id}/print', [App\Http\Controllers\ManajemenRisikoController::class, 'hasilAuditPrint'])->name('manajemen-risiko.hasil-audit.print')->middleware('auth');
    Route::post('/manajemen-risiko/hasil-audit/{id}/upload-scan', [App\Http\Controllers\ManajemenRisikoController::class, 'uploadScanHasilAudit'])->name('manajemen-risiko.hasil-audit.upload-scan')->middleware('auth');

    Route::get('/manajemen-risiko/{id}', [App\Http\Controllers\ManajemenRisikoController::class, 'show'])->name('manajemen-risiko.show')->middleware('auth');
});
