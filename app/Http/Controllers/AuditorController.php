<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peta;
use App\Models\CommentPr;
use App\Models\User;
use App\Models\HasilAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AuditorController extends Controller
{

    /**
     * ========================================
     * AUDITOR METHODS (Ketua, Anggota, Sekretaris)
     * ========================================
     */
    /**
     * Dashboard Auditor - Menampilkan risiko yang ditugaskan ke auditor yang login
     */
    public function auditorIndex(Request $request)
    {
        $active = 21;
        $user = Auth::user();

        // Get filter parameters
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');
        $statusReview = $request->input('status_review', 'all');

        // Get available years
        $years = Peta::selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($years->isEmpty()) {
            $currentYear = date('Y');
            $years = collect(range($currentYear - 4, $currentYear))->reverse()->values();
        }

        // Get available unit kerja (hanya dari risiko yang ditugaskan ke auditor ini)
        $unitKerjas = Peta::where('auditor_id', $user->id)
            ->select('jenis as nama_unit_kerja')
            ->distinct()
            ->orderBy('jenis')
            ->get();

        // Get auditors list (for view compatibility)
        $auditors = collect();

        // Build query - hanya tampilkan risiko yang ditugaskan ke auditor ini
        $query = Peta::with(['comment_prs.user', 'auditor', 'kegiatan'])
            ->where('auditor_id', $user->id)
            ->whereYear('created_at', $tahun);

        // Filter by unit kerja
        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        // Filter by status review
        if ($statusReview != 'all') {
            if ($statusReview == 'reviewed') {
                $query->where('status_telaah', 1);
            } else {
                $query->where(function ($q) {
                    $q->where('status_telaah', 0)->orWhereNull('status_telaah');
                });
            }
        }

        // Filter by cluster (risk level)
        switch ($cluster) {
            case 'high':
                $query->whereIn('tingkat_risiko', ['Extreme', 'High']);
                break;
            case 'middle':
                $query->where('tingkat_risiko', 'Moderate');
                break;
            case 'low':
                $query->whereIn('tingkat_risiko', ['Low']);
                break;
        }

        // Get paginated data, sorted by risk score (highest first)
        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->paginate(15);

        // Calculate statistics untuk auditor ini
        $statistics = [
            'total' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)->count(),
            'high_risk' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->whereIn('tingkat_risiko', ['Extreme', 'High'])->count(),
            'middle_risk' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Moderate')->count(),
            'low_risk' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where('tingkat_risiko', 'Low')->count(),
            'reviewed' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where('status_telaah', 1)->count(),
            'pending' => Peta::where('auditor_id', $user->id)
                ->whereYear('created_at', $tahun)
                ->where(function ($q) {
                    $q->where('status_telaah', 0)->orWhereNull('status_telaah');
                })->count(),
            'assigned_auditor' => 0,
        ];

        // Count notifikasi (risiko baru yang belum direview)
        $notificationCount = Peta::where('auditor_id', $user->id)
            ->where(function ($q) {
                $q->where('status_telaah', 0)->orWhereNull('status_telaah');
            })
            ->count();

        $auditorFilter = 'all';

        return view('manajemen_risiko.index', compact(
            'active',
            'petas',
            'statistics',
            'cluster',
            'tahun',
            'unitKerja',
            'statusReview',
            'years',
            'unitKerjas',
            'notificationCount',
            'auditors',
            'auditorFilter'
        ));
    }


    /**
     * Detail risiko untuk Auditor - Input Pertanyaan & Review Jawaban
     * WORKFLOW: 
     * - Jika status = menunggu_audit: Auditor input hasil audit (pengendalian, mitigasi, komentar, status)
     * - Jika status = final: Read-only
     */
    public function auditorShowDetail($id)
    {
        $active = 21;
        $user = Auth::user();

        // Pastikan auditor hanya bisa melihat risiko yang ditugaskan ke mereka
        $peta = Peta::with(['comment_prs.user', 'kegiatan', 'auditor'])
            ->where('auditor_id', $user->id)
            ->findOrFail($id);

        // ✅ CEK STATUS AUDIT
        $statusAudit = $peta->status_audit;

        // Get hasil audit if exists
        $hasilAudit = HasilAudit::where('peta_id', $peta->id)
            ->where('auditor_id', $user->id)
            ->where('tahun_anggaran', date('Y'))
            ->first();

        // Decode template_data (daftar pertanyaan) dan auditee_response (jawaban)
        $questions = $peta->questions; // Menggunakan accessor dari Model
        $responses = $peta->responses; // Menggunakan accessor dari Model

        // ✅ PERBAIKAN: Tentukan mode view berdasarkan NEW WORKFLOW
        $viewMode = 'read_only'; // Default

        // ✅ NEW: Cek apakah Auditor bisa input hasil audit (sesuai revisi dosen)
        if ($peta->auditorCanInputAudit()) {
            // Mode input hasil audit: pengendalian, mitigasi, komentar, status konfirmasi
            $viewMode = 'input_questions'; // Nama tetap sama, tapi konten berbeda (lihat view)
        } elseif ($peta->isAuditFinal()) {
            $viewMode = 'final'; // Semua read-only
        }

        // Hitung Skor Risiko
        $skorTotal = ($peta->skor_kemungkinan ?? 0) * ($peta->skor_dampak ?? 0);

        // ✅ GUNAKAN VIEW YANG SUDAH ADA: manajemen_risiko.show
        return view('manajemen_risiko.show', compact(
            'active',
            'peta',
            'user',
            'hasilAudit',
            'questions',
            'responses',
            'statusAudit',
            'viewMode',
            'skorTotal'
        ));
    }

    /**
     * Update template audit oleh Auditor
     * WORKFLOW:
     * - Input Pertanyaan: Simpan ke template_data (JSON)
     * - Review Jawaban: Simpan penilaian ke status_konfirmasi_auditor & hasil_audit
     */
    public function auditorUpdateTemplate(Request $request, $id)
    {
        $user = Auth::user();

        // Pastikan auditor hanya update data miliknya
        $peta = Peta::where('auditor_id', $user->id)->with('kegiatan')->findOrFail($id);

        // ✅ CEK: Tentukan mode action (input pertanyaan atau review jawaban)
        $action = $request->input('action'); // 'input_questions', 'review_answers', atau 'confirm_revision'

        if ($action === 'input_audit_result') {
            // ✅ MODE BARU: AUDITOR INPUT HASIL AUDIT LANGSUNG (SESUAI REQUIREMENT DOSEN)

            // Validasi input
            $request->validate([
                'pengendalian' => 'required|string|max:5000',
                'mitigasi' => 'required|in:Accept Risk,Share Risk,Transfer Risk',
                'komentar_auditor' => 'required|string|max:5000',
                'status_konfirmasi_auditor' => 'required|in:Completed,Not Completed',
            ]);

            // Calculate score and level
            $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

            if ($skorTotal >= 20) {
                $levelText = 'EXTREME';
            } elseif ($skorTotal >= 15) {
                $levelText = 'HIGH';
            } elseif ($skorTotal >= 10) {
                $levelText = 'MODERATE';
            } else {
                $levelText = 'LOW';
            }

            // Calculate residual risk
            if ($skorTotal >= 20) {
                $residualText = 'Extreme';
            } elseif ($skorTotal >= 15) {
                $residualText = 'High';
            } elseif ($skorTotal >= 10) {
                $residualText = 'Moderate';
            } else {
                $residualText = 'Low';
            }

            // ✅ UPDATE DATA PETA dengan hasil audit
            $peta->update([
                'pengendalian' => $request->pengendalian,
                'mitigasi' => $request->mitigasi,
                'status_konfirmasi_auditor' => $request->status_konfirmasi_auditor,
            ]);

            // ✅ SIMPAN/UPDATE KE TABEL HASIL_AUDIT
            $hasilAudit = HasilAudit::updateOrCreate(
                [
                    'peta_id' => $peta->id,
                    'auditor_id' => $user->id,
                    'tahun_anggaran' => date('Y'),
                ],
                [
                    'pengendalian' => $request->pengendalian,
                    'mitigasi' => $request->mitigasi,
                    'komentar_1' => $request->komentar_auditor,
                    'komentar_2' => '-', // ✅ PERBAIKAN: Set default value untuk komentar_2
                    'komentar_3' => '-',
                    'komentar_4' => '-', // ✅ PERBAIKAN: Set default value untuk komentar_3
                    'unit_kerja' => $peta->jenis,
                    'kode_risiko' => $peta->kode_regist,
                    'kegiatan' => $peta->kegiatan->judul ?? $peta->judul,
                    'level_risiko' => $levelText,
                    'risiko_residual' => $residualText,
                    'skor_total' => $skorTotal,
                    'nama_pemonev' => $user->name,
                    'nip_pemonev' => $user->nip ?? '-',
                ]
            );

            // ✅ LOG ACTIVITY
            $statusLabel = $request->status_konfirmasi_auditor == 'Completed' ? 'Audit Selesai' : 'Audit Belum Selesai (Perlu Tindak Lanjut Auditee)';

            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => "Auditor telah menginput hasil audit. Status: {$statusLabel}. Mitigasi: {$request->mitigasi}.",
            ]);

            return redirect()
                ->route('manajemen-risiko.auditor.show-detail', $peta->id)
                ->with('success', 'Hasil audit berhasil disimpan! Status Konfirmasi: ' . $statusLabel);
        } elseif ($action === 'input_questions') {
            // ✅ MODE 1: AUDITOR INPUT PERTANYAAN

            // Validasi: Hanya bisa input jika status memungkinkan
            if (!$peta->auditorCanInputQuestions()) {
                return redirect()->back()->with('error', 'Anda tidak dapat menginput pertanyaan pada status ini!');
            }

            $request->validate([
                'questions' => 'required|array|min:1',
                'questions.*.question' => 'required|string',
            ]);

            // Format pertanyaan ke JSON
            $questionsData = [];
            foreach ($request->questions as $index => $item) {
                $questionsData[] = [
                    'no' => $index + 1,
                    'question' => $item['question'],
                    'created_at' => now()->toDateTimeString(),
                ];
            }

            // Simpan ke template_data
            $peta->update([
                'template_data' => json_encode($questionsData),
                'template_sent_at' => now(),
            ]);

            // Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => 'Auditor telah menginput ' . count($questionsData) . ' pertanyaan audit wawancara.',
            ]);

            return redirect()
                ->route('manajemen-risiko.auditor.show-detail', $peta->id)
                ->with('success', 'Daftar pertanyaan audit berhasil disimpan! Auditee dapat menjawab pertanyaan.');
        } elseif ($action === 'review_answers') {
            // ✅ MODE 2: AUDITOR REVIEW JAWABAN AUDITEE

            // Validasi: Hanya bisa review jika auditee sudah jawab
            if (!$peta->auditorCanReview()) {
                return redirect()->back()->with('error', 'Belum ada jawaban dari Auditee untuk direview!');
            }

            $request->validate([
                'penilaian' => 'required|array',
                'penilaian.*.status' => 'required|in:memadai,kurang_memadai,tidak_memadai',
                'penilaian.*.komentar' => 'nullable|string',
                'penilaian.*.rekomendasi' => 'nullable|string',
                'penilaian.*.skor' => 'nullable|integer|min:0|max:100',
                'pengendalian' => 'nullable|string',
                'mitigasi' => 'nullable|string',
            ]);

            // Calculate score and level
            $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

            if ($skorTotal >= 15) {
                $levelText = 'HIGH';
            } elseif ($skorTotal >= 10) {
                $levelText = 'MODERATE';
            } else {
                $levelText = 'LOW';
            }

            // Calculate residual risk
            if ($skorTotal >= 20) {
                $residualText = 'Extreme';
            } elseif ($skorTotal >= 15) {
                $residualText = 'High';
            } elseif ($skorTotal >= 10) {
                $residualText = 'Moderate';
            } else {
                $residualText = 'Low';
            }

            // Simpan hasil review ke tabel hasil_audit
            $hasilAudit = HasilAudit::updateOrCreate(
                [
                    'peta_id' => $peta->id,
                    'auditor_id' => $user->id,
                    'tahun_anggaran' => date('Y'),
                ],
                [
                    'penilaian_data' => json_encode($request->penilaian), // Simpan penilaian per pertanyaan
                    'pengendalian' => $request->input('pengendalian', '-'),
                    'mitigasi' => $request->input('mitigasi', '-'),
                    'komentar_1' => $request->input('komentar_1', '-'),
                    'komentar_2' => $request->input('komentar_2', '-'),
                    'komentar_3' => $request->input('komentar_3', '-'),
                    'unit_kerja' => $peta->jenis,
                    'kode_risiko' => $peta->kode_regist,
                    'kegiatan' => $peta->kegiatan->judul ?? $peta->judul,
                    'level_risiko' => $levelText,
                    'risiko_residual' => $residualText,
                    'skor_total' => $skorTotal,
                    'nama_pemonev' => $user->name,
                    'nip_pemonev' => $user->nip,
                ]
            );

            // ✅ Update status konfirmasi auditor menjadi 'reviewed'
            $peta->update([
                'status_konfirmasi_auditor' => 'reviewed',
            ]);

            // Log activity
            $totalPertanyaan = count($request->penilaian);
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => "Auditor telah menyelesaikan review terhadap {$totalPertanyaan} jawaban audit wawancara.",
            ]);

            return redirect()
                ->route('manajemen-risiko.auditor.show-detail', $peta->id)
                ->with('success', 'Review audit berhasil disimpan! Menunggu konfirmasi dari Auditee.');
        } elseif ($action === 'confirm_revision') {
            // ✅ MODE 3: AUDITOR KONFIRMASI REVISI AUDITEE

            // Validasi: Hanya bisa konfirmasi jika status = menunggu_konfirmasi_auditor
            if (!$peta->auditorCanConfirmRevision()) {
                return redirect()->back()->with('error', 'Tidak dapat mengkonfirmasi revisi pada status ini!');
            }

            $request->validate([
                'catatan_konfirmasi' => 'nullable|string|max:1000',
            ]);

            // Update status konfirmasi auditor menjadi 'reviewed'
            $peta->update([
                'status_konfirmasi_auditor' => 'reviewed',
            ]);

            // Log activity
            CommentPr::create([
                'peta_id' => $peta->id,
                'user_id' => $user->id,
                'jenis' => 'analisis',
                'comment' => 'Auditor telah mengkonfirmasi hasil revisi Auditee. ' . ($request->catatan_konfirmasi ? 'Catatan: ' . $request->catatan_konfirmasi : ''),
            ]);

            return redirect()
                ->route('manajemen-risiko.auditor.show-detail', $peta->id)
                ->with('success', 'Revisi telah dikonfirmasi! Menunggu konfirmasi dari Auditee.');
        } elseif ($action === 'approve_follow_up') {
            // ✅ MODE BARU 4: AUDITOR APPROVE HASIL TINDAK LANJUT AUDITEE
            // (SETELAH AUDITOR SET STATUS = NOT COMPLETED & AUDITEE SUDAH SUBMIT TINDAK LANJUT)

            // Validasi: Hanya bisa approve jika Auditee sudah submit tindak lanjut
            if (!$peta->auditorCanReviewFollowUp()) {
                return redirect()->back()->with('error', 'Belum ada tindak lanjut dari Auditee untuk direview!');
            }

            $request->validate([
                'keputusan_auditor' => 'required|in:approve,reject',
                'catatan_auditor' => 'nullable|string|max:2000',
            ]);

            if ($request->keputusan_auditor === 'approve') {
                // ✅ APPROVE: Set status Auditor menjadi Completed (Audit selesai)
                $peta->update([
                    'status_konfirmasi_auditor' => 'Completed', // ✅ UBAH STATUS AUDITOR KE COMPLETED
                    // status_konfirmasi_auditee tetap (Completed/Not Completed)
                ]);

                // Log activity
                CommentPr::create([
                    'peta_id' => $peta->id,
                    'user_id' => $user->id,
                    'jenis' => 'analisis',
                    'comment' => 'Auditor telah menyetujui (APPROVE) hasil tindak lanjut dari Auditee. Status Audit: SELESAI.' .
                        ($request->catatan_auditor ? ' Catatan: ' . $request->catatan_auditor : ''),
                ]);

                return redirect()
                    ->route('manajemen-risiko.auditor.show-detail', $peta->id)
                    ->with('success', '✅ Tindak lanjut DISETUJUI! Status audit menjadi SELESAI. Auditee dapat melakukan konfirmasi akhir.');
            } else {
                // ❌ REJECT: Minta Auditee untuk revisi lagi
                // Reset status_konfirmasi_auditee agar Auditee bisa submit ulang
                $peta->update([
                    'status_konfirmasi_auditee' => null, // Reset status Auditee
                    // status_konfirmasi_auditor tetap "Not Completed"
                ]);

                // Simpan catatan penolakan ke catatan_revisi
                $rejectionData = [
                    'catatan_penolakan' => $request->catatan_auditor,
                    'rejected_at' => now()->toDateTimeString(),
                    'rejected_by' => $user->name,
                ];

                $peta->update([
                    'catatan_revisi' => json_encode($rejectionData),
                ]);

                // Log activity
                CommentPr::create([
                    'peta_id' => $peta->id,
                    'user_id' => $user->id,
                    'jenis' => 'analisis',
                    'comment' => 'Auditor MENOLAK hasil tindak lanjut. Auditee diminta melakukan revisi ulang. Catatan: ' . $request->catatan_auditor,
                ]);

                return redirect()
                    ->route('manajemen-risiko.auditor.show-detail', $peta->id)
                    ->with('warning', '⚠️ Tindak lanjut DITOLAK! Auditee akan diminta untuk melakukan perbaikan kembali.');
            }
        } else {
            return redirect()->back()->with('error', 'Action tidak valid!');
        }
    }

    /**
     * Kirim Revisi ke Auditee
     */
    public function auditorSendRevision(Request $request, $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string|max:2000',
            'items_revisi' => 'required|array|min:1',
            'items_revisi.*.pertanyaan_no' => 'required|integer',
            'items_revisi.*.catatan' => 'required|string',
        ]);

        $user = Auth::user();
        $peta = Peta::where('auditor_id', $user->id)->findOrFail($id);

        // Validasi: Hanya bisa kirim revisi jika auditee sudah jawab
        if (!$peta->auditorCanSendRevision()) {
            return redirect()->back()->with('error', 'Tidak dapat mengirim revisi pada status ini!');
        }

        // Format data revisi ke JSON
        $revisiData = [
            'catatan_umum' => $request->catatan_revisi,
            'items' => $request->items_revisi,
            'sent_at' => now()->toDateTimeString(),
            'sent_by' => $user->name,
        ];

        // Update status menjadi need_revision
        $peta->update([
            'status_konfirmasi_auditor' => 'need_revision',
            'catatan_revisi' => json_encode($revisiData),
        ]);

        // Log activity
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'jenis' => 'analisis',
            'comment' => 'Auditor meminta revisi kepada Auditee. Total ' . count($request->items_revisi) . ' item perlu diperbaiki.',
        ]);

        return redirect()
            ->route('manajemen-risiko.auditor.show-detail', $peta->id)
            ->with('success', 'Permintaan revisi berhasil dikirim ke Auditee!');
    }

    /**
     * Approve risiko - Jika data sesuai
     */
    public function auditorApprove(Request $request, $id)
    {
        $user = Auth::user();

        $peta = Peta::where('auditor_id', $user->id)->findOrFail($id);

        $peta->update([
            'status_telaah' => 1,
            'waktu_telaah_spi' => now(),
        ]);

        // Tambahkan komentar approval
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'comment' => $request->input('comment', 'Data risiko telah direview dan disetujui.'),
            'jenis' => 'analisis',
        ]);

        return redirect()->route('manajemen-risiko.auditor.index')
            ->with('success', 'Risiko berhasil disetujui dan akan dikirim ke Admin!');
    }



    /**
     * Reject risiko - Jika data tidak sesuai, kirim kembali ke auditee
     */
    public function auditorReject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        $peta = Peta::where('auditor_id', $user->id)->findOrFail($id);

        $peta->update([
            'status_telaah' => 0,
            'koreksiPr' => 'rejected',
            'koreksiPr_at' => now(),
        ]);

        // Tambahkan komentar rejection
        CommentPr::create([
            'peta_id' => $peta->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'jenis' => 'analisis',
        ]);

        return redirect()->route('manajemen-risiko.auditor.index')
            ->with('success', 'Risiko dikembalikan ke Auditee untuk revisi!');
    }



    /**
     * Generate laporan review untuk Admin
     */
    public function auditorGenerateReport(Request $request)
    {
        $user = Auth::user();
        $unitKerja = $request->input('unit_kerja');
        $tahun = $request->input('tahun', date('Y'));

        if (!$unitKerja || $unitKerja == 'all') {
            return redirect()->back()->with('error', 'Silakan pilih unit kerja terlebih dahulu!');
        }

        $petas = Peta::with(['comment_prs', 'auditor', 'kegiatan'])
            ->where('auditor_id', $user->id)
            ->where('jenis', $unitKerja)
            ->whereYear('created_at', $tahun)
            ->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')
            ->get();

        if ($petas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data risiko untuk unit kerja ini!');
        }

        return view('manajemen_risiko.generate_report', compact('petas', 'unitKerja', 'tahun', 'user'));
    }



    /**
     * Export Excel untuk Auditor
     */
    public function auditorExport(Request $request)
    {
        $user = Auth::user();
        $cluster = $request->input('cluster', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $unitKerja = $request->input('unit_kerja', 'all');

        // Build query - hanya risiko yang ditugaskan ke auditor ini
        $query = Peta::with(['comment_prs'])
            ->where('auditor_id', $user->id)
            ->whereYear('created_at', $tahun);

        if ($unitKerja != 'all') {
            $query->where('jenis', $unitKerja);
        }

        switch ($cluster) {
            case 'high':
                $query->whereIn('tingkat_risiko', ['EXTREME', 'HIGH']);
                break;
            case 'middle':
                $query->where('tingkat_risiko', 'MIDDLE');
                break;
            case 'low':
                $query->whereIn('tingkat_risiko', ['LOW', 'VERY LOW']);
                break;
        }

        $petas = $query->orderByRaw('(skor_kemungkinan * skor_dampak) DESC')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("SISPI - " . $user->name)
            ->setTitle("Review Risiko - {$tahun}")
            ->setSubject("Laporan Review Risiko oleh Auditor");

        // Header
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'LAPORAN REVIEW RISIKO TAHUN ' . $tahun . ' - ' . strtoupper($user->name));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Column headers
        $headers = ['No', 'Unit Kerja', 'Kategori', 'Judul', 'Kode Registrasi', 'Kemungkinan', 'Dampak', 'Skor Total', 'Tingkat Risiko', 'Status Review', 'Jumlah Komentar', 'Tanggal Review'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '3', $header);
            $column++;
        }

        // Style header
        $sheet->getStyle('A3:L3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data rows
        $row = 4;
        $no = 1;
        foreach ($petas as $peta) {
            $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $peta->jenis);
            $sheet->setCellValue('C' . $row, $peta->kategori);
            $sheet->setCellValue('D' . $row, $peta->judul);
            $sheet->setCellValue('E' . $row, $peta->kode_regist);
            $sheet->setCellValue('F' . $row, $peta->skor_kemungkinan);
            $sheet->setCellValue('G' . $row, $peta->skor_dampak);
            $sheet->setCellValue('H' . $row, $skorTotal);
            $sheet->setCellValue('I' . $row, $peta->tingkat_risiko);
            $sheet->setCellValue('J' . $row, $peta->status_telaah ? 'Direview' : 'Pending');
            $sheet->setCellValue('K' . $row, $peta->comment_prs->count());
            $sheet->setCellValue('L' . $row, $peta->waktu_telaah_spi ? date('d-m-Y', strtotime($peta->waktu_telaah_spi)) : '-');

            // Apply borders
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'Review_Risiko_' . str_replace(' ', '_', $user->name) . '_' . $tahun . '_' . date('Ymd_His') . '.xlsx';

        // Save file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }


    /**
     * Upload lampiran file pendukung oleh Auditor
     */
    public function auditorUploadLampiran(Request $request, $id)
    {
        $request->validate([
            'file_pendukung' => 'required|mimes:pdf,xls,xlsx|max:10240', // Max 10MB
        ]);

        $peta = Peta::findOrFail($id);
        $user = Auth::user();

        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');

            // Buat nama file yang rapi
            $namaFile = 'Lampiran_Revisi_' . $peta->kode_regist . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Simpan file ke storage (folder public/lampiran_auditor)
            $path = $file->storeAs('public/lampiran_auditor', $namaFile);

            // Update hasil audit dengan file lampiran
            HasilAudit::where('peta_id', $peta->id)
                ->where('auditor_id', $user->id)
                ->where('tahun_anggaran', date('Y'))
                ->update([
                    'file_lampiran' => $namaFile,
                ]);

            // dd($request->all());

            return redirect()->back()->with('success', 'File revisi berhasil diupload dan dikirim ke Auditee.');
        }

        return redirect()->back()->with('error', 'Gagal mengupload file.');
    }



    /**
     * Export PDF untuk Auditor
     */
    public function auditorExportPdf($id)
    {
        $peta = Peta::with(['kegiatan', 'auditor', 'comment_prs'])->findOrFail($id);
        $user = Auth::user();

        // Get hasil audit if exists
        $hasilAudit = HasilAudit::where('peta_id', $peta->id)
            ->where('auditor_id', $user->id)
            ->where('tahun_anggaran', date('Y'))
            ->first();

        // Calculate score and level
        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;

        // LEVEL
        if ($skorTotal >= 20) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 15) {
            $levelText = 'HIGH';
            $badgeClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $levelText = 'MODERATE';
            $badgeClass = 'bg-warning text-dark';
        } else {
            $levelText = 'LOW';
            $badgeClass = 'bg-success text-white';
        }

        // RESIDUAL
        if ($skorTotal >= 20) {
            $residualText = 'Extreme';
            $residualClass = 'bg-danger text-white';
        } elseif ($skorTotal >= 15) {
            $residualText = 'High';
            $residualClass = 'bg-warning text-dark';
        } elseif ($skorTotal >= 10) {
            $residualText = 'Moderate';
            $residualClass = 'bg-info text-dark';
        } else {
            $residualText = 'Low';
            $residualClass = 'bg-success text-white';
        }

        // Data yang akan dilempar ke view PDF
        $data = [
            'peta' => $peta,
            'hasilAudit' => $hasilAudit,
            'user' => $user,
            'skorTotal' => $skorTotal,
            'levelText' => $levelText,
            'badgeClass' => $badgeClass,
            'residualText' => $residualText,
            'residualClass' => $residualClass,
            'tanggal' => date('d F Y')
        ];

        // Menggunakan library DomPDF
        $pdf = Pdf::loadView('manajemen_risiko.export_audit_pdf', $data);

        // Set landscape jika tabel terlalu lebar
        $pdf->setPaper('a4', 'landscape');

        $filename = 'Audit_' . $peta->kode_regist . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }



    public function auditorShow($id)
    {
        abort(404);
    }

    public function auditorSendTemplate(Request $request, $id)
    {
        abort(404);
    }

    public function auditorUploadReport(Request $request, $id)
    {
        abort(404);
    }
}
