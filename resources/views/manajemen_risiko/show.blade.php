@extends('layout.app')
@section('title', 'Detail Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditee = !$isAdmin && !$isAuditor; // Role Auditee (Unit Kerja)
    @endphp

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.index') : ($isAuditor ? route('manajemen-risiko.auditor.index') : route('manajemen-risiko.index')) }}"
                    class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <h1></h1>
                @if ($isAuditor)
                    Detail Review Risiko
                @elseif($isAuditee)
                    Detail Risiko
                @else
                    Detail Risiko
                @endif
                </h1>
                {{-- Badge Clustering Result --}}
                @php
                    $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;
                    if ($skorTotal >= 20) {
                        $clusterBadgeClass = 'badge-danger';
                        $clusterText = 'HIGH RISK';
                        $clusterIcon = 'fa-exclamation-triangle';
                    } elseif ($skorTotal >= 15) {
                        $clusterBadgeClass = 'badge-warning';
                        $clusterText = 'MODERATE RISK';
                        $clusterIcon = 'fa-exclamation-circle';
                    } else {
                        $clusterBadgeClass = 'badge-success';
                        $clusterText = 'LOW RISK';
                        $clusterIcon = 'fa-check-circle';
                    }
                @endphp
                <span class="badge {{ $clusterBadgeClass }} ml-3" style="font-size: 16px; padding: 10px 20px;">
                    <i class="fas {{ $clusterIcon }}"></i> {{ $clusterText }}
                </span>
            </div>

            {{-- Info Alert for Auditee --}}
            @if ($isAuditee && empty($peta->pernyataan))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-info-circle"></i> <strong>Data Risiko dari Upload Excel</strong></h5>
                    <p class="mb-2">Risiko ini telah di-<strong>clustering otomatis</strong> dari file Excel yang Anda
                        upload dengan hasil: <span class="badge {{ $clusterBadgeClass }}">{{ $clusterText }}</span></p>
                    <hr>
                    <p class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <strong>Silakan lengkapi data monitoring</strong> (Pernyataan Risiko, Uraian Dampak, Metode
                        Pengendalian) sebelum submit ke Auditor.
                    </p>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="section-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        {{-- Informasi Risiko --}}
                        <div class="card border-0 shadow rounded">
                            <div
                                class="card-header {{ $isAuditor ? 'bg-primary' : ($isAuditee ? 'bg-success' : 'bg-info') }} text-white">
                                <h4 class="text-white"><i class="fas fa-info-circle"></i> Informasi Risiko</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Unit Kerja</th>
                                        <td><strong>{{ $peta->jenis }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Kode Registrasi</th>
                                        <td><span class="badge badge-light">{{ $peta->kode_regist }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Judul Risiko</th>
                                        <td><strong>{{ $peta->judul }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td><span class="badge badge-secondary">{{ $peta->kategori }}</span></td>
                                    </tr>
                                    @if ($peta->kegiatan)
                                        <tr>
                                            <th>Kegiatan Terkait</th>
                                            <td><strong>{{ $peta->kegiatan->judul }}</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Anggaran</th>
                                        <td>Rp {{ number_format($peta->anggaran, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pernyataan Risiko</th>
                                        <td>{{ $peta->pernyataan ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Uraian Dampak</th>
                                        <td>{{ $peta->uraian ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Metode Pengendalian</th>
                                        <td>{{ $peta->metode ?: '-' }}</td>
                                    </tr>
                                </table>

                                {{-- Action buttons for Auditee --}}
                                @if ($isAuditee)
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        @if ($peta->koreksiPr == 'rejected' || empty($peta->pernyataan))
                                            <a href="{{ route('manajemen-risiko.auditee.edit', $peta->id) }}"
                                                class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                                {{ empty($peta->pernyataan) ? 'Isi Data Monitoring' : 'Perbaiki Data' }}
                                            </a>
                                        @endif

                                        @if (!empty($peta->pernyataan) && !$peta->status_telaah && $peta->koreksiPr != 'submitted')
                                            <form action="{{ route('manajemen-risiko.auditee.submit', $peta->id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Submit data risiko ini ke Auditor untuk direview?')">
                                                @csrf
                                                <button type="submit" class="btn btn-info">
                                                    <i class="fas fa-paper-plane"></i> Submit ke Auditor
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Komentar & Review --}}
                        <div class="card border-0 shadow rounded">
                            <div class="card-header">
                                <h4><i class="fas fa-comments"></i>
                                    {{ $isAuditor ? 'Riwayat Review & Komentar' : 'Komentar & Catatan' }}</h4>
                            </div>
                            <div class="card-body">
                                @if ($peta->comment_prs->count() > 0)
                                    <div class="mb-4">
                                        @foreach ($peta->comment_prs as $comment)
                                            <div class="alert alert-light border mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div>
                                                        <strong class="text-primary">
                                                            <i class="fas fa-user-circle"></i>
                                                            {{ $comment->user->name ?? 'Unknown' }}
                                                        </strong>
                                                        <span class="badge badge-info ml-2">
                                                            {{ ucfirst($comment->jenis) }}
                                                        </span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock"></i>
                                                        {{ $comment->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                <p class="mb-0">{{ $comment->comment }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Belum ada komentar untuk risiko ini.
                                    </div>
                                @endif

                                <hr>

                                <h6 class="font-weight-bold mb-3">
                                    {{ $isAuditor ? 'Tambah Komentar Review' : 'Tambah Komentar Baru' }}</h6>
                                <form action="{{ route('manajemen-risiko.comment', $peta->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label class="font-weight-bold">JENIS KOMENTAR</label>
                                        <select name="jenis" class="form-control" required>
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="keuangan">Aspek Keuangan</option>
                                            <option value="analisis">Analisis Risiko</option>
                                            <option value="mitigasi">Strategi Mitigasi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">KOMENTAR</label>
                                        <textarea name="comment" class="form-control" rows="4"
                                            placeholder="{{ $isAuditor ? 'Masukkan komentar review Anda...' : 'Masukkan komentar Anda...' }}" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Kirim Komentar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        {{-- Skor Risiko --}}
                        <div class="card border-0 shadow rounded">
                            <div
                                class="card-header {{ $isAuditor ? 'bg-danger' : ($isAuditee ? 'bg-info' : 'bg-primary') }} text-white">
                                <h4 class="text-white"><i class="fas fa-chart-bar"></i> Skor Risiko</h4>
                            </div>
                            <div class="card-body text-center">
                                <h1 class="display-4 font-weight-bold">
                                    {{ $peta->skor_kemungkinan * $peta->skor_dampak }}
                                </h1>
                                <p class="text-muted">
                                    {{ $peta->skor_kemungkinan }} (Kemungkinan) ×
                                    {{ $peta->skor_dampak }} (Dampak)
                                </p>
                                <hr>
                                @php
                                    $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;
                                    if ($skorTotal >= 20) {
                                        $badgeClass = 'badge-danger';
                                        $badgeText = 'Extreme';
                                        $badgeIcon = 'fa-exclamation-triangle';
                                    } elseif ($skorTotal >= 15) {
                                        $badgeClass = 'badge-warning';
                                        $badgeText = 'High';
                                        $badgeIcon = 'fa-exclamation-circle';
                                    } elseif ($skorTotal >= 10) {
                                        $badgeClass = 'badge-info';
                                        $badgeText = 'Moderate';
                                        $badgeIcon = 'fa-info-circle';
                                    } else {
                                        $badgeClass = 'badge-success';
                                        $badgeText = 'Low';
                                        $badgeIcon = 'fa-check-circle';
                                    }
                                @endphp
                                <h4>
                                    <span class="badge {{ $badgeClass }}" style="font-size: 18px;">
                                        <i class="fas {{ $badgeIcon }}"></i> {{ $badgeText }}
                                    </span>
                                </h4>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="card border-0 shadow rounded">
                            <div class="card-header">
                                <h4><i class="fas {{ $isAuditor ? 'fa-clipboard-check' : 'fa-tasks' }}"></i>
                                    {{ $isAuditor ? 'Status Review' : 'Status' }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="font-weight-bold d-block">Status:</label>
                                    @if ($peta->status_telaah)
                                        <span class="badge badge-success" style="font-size: 14px;">
                                            <i class="fas fa-check-circle"></i>
                                            {{ $isAuditor ? 'Sudah Direview' : 'Sudah Ditelaah' }}
                                        </span>
                                        @if ($peta->waktu_telaah_spi)
                                            <p class="text-muted mt-2 mb-0">
                                                <small>
                                                    <i class="far fa-clock"></i>
                                                    @if (is_string($peta->waktu_telaah_spi))
                                                        {{ \Carbon\Carbon::parse($peta->waktu_telaah_spi)->format('d M Y H:i') }}
                                                    @else
                                                        {{ $peta->waktu_telaah_spi->format('d M Y H:i') }}
                                                    @endif
                                                </small>
                                            </p>
                                        @endif
                                    @elseif($peta->koreksiPr == 'rejected')
                                        <span class="badge badge-danger" style="font-size: 14px;">
                                            <i class="fas fa-times-circle"></i> Ditolak - Perlu Revisi
                                        </span>
                                    @elseif($peta->koreksiPr == 'submitted')
                                        <span class="badge badge-info" style="font-size: 14px;">
                                            <i class="fas fa-paper-plane"></i> Menunggu Review
                                        </span>
                                    @else
                                        <span class="badge badge-warning" style="font-size: 14px;">
                                            <i class="fas fa-clock"></i>
                                            {{ $isAuditor ? 'Menunggu Review' : 'Belum Ditelaah' }}
                                        </span>
                                    @endif
                                </div>

                                @if ($isAuditor && !$peta->status_telaah)
                                    {{-- Auditor Actions --}}
                                    <hr>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success btn-block mb-2" data-toggle="modal"
                                            data-target="#approveModal">
                                            <i class="fas fa-check"></i> Setujui Risiko
                                        </button>
                                        <button class="btn btn-danger btn-block" data-toggle="modal"
                                            data-target="#rejectModal">
                                            <i class="fas fa-times"></i> Tolak & Kembalikan
                                        </button>
                                    </div>
                                @elseif($isAdmin && !$peta->status_telaah)
                                    {{-- Admin Actions --}}
                                    <hr>
                                    <form action="{{ route('manajemen-risiko.update-status', $peta->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Tandai risiko ini sebagai sudah ditelaah?')">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status_telaah" value="1">
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-check"></i> Tandai Selesai Ditelaah
                                        </button>
                                    </form>
                                @endif

                                <hr>

                                <div>
                                    <label class="font-weight-bold d-block">Jumlah Komentar:</label>
                                    <h4>
                                        <span class="badge badge-info">
                                            <i class="fas fa-comment"></i> {{ $peta->comment_prs->count() }} Komentar
                                        </span>
                                    </h4>
                                </div>

                                @if ($isAuditee)
                                    <hr>
                                    <div>
                                        <label class="font-weight-bold d-block">Auditor:</label>
                                        @if ($peta->auditor)
                                            <p class="mb-0">
                                                <i class="fas fa-user"></i> {{ $peta->auditor->name }}
                                                <br>
                                                <small
                                                    class="text-muted">{{ $peta->auditor->Level->name ?? 'N/A' }}</small>
                                            </p>
                                        @else
                                            <small class="text-muted">Belum ditugaskan</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Kegiatan Terkait --}}
                        @if ($peta->kegiatan)
                            <div class="card border-0 shadow rounded">
                                <div class="card-header">
                                    <h4><i class="fas fa-tasks"></i> Kegiatan Terkait</h4>
                                </div>
                                <div class="card-body">
                                    <p><strong>{{ $peta->kegiatan->judul ?? '-' }}</strong></p>
                                    <small class="text-muted">
                                        {{ $peta->kegiatan->deskripsi ?? 'Tidak ada deskripsi' }}
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- MODALS for Auditor --}}
    @if ($isAuditor)
        {{-- Modal Approve --}}
        <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle"></i> Setujui Risiko
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('manajemen-risiko.auditor.approve', $peta->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p><strong>Apakah Anda yakin data risiko ini sudah sesuai dan siap dikirim ke Admin?</strong>
                            </p>
                            <hr>
                            <p><strong>Unit:</strong> {{ $peta->jenis }}</p>
                            <p><strong>Judul:</strong> {{ $peta->judul }}</p>
                            <p><strong>Skor:</strong> {{ $peta->skor_kemungkinan * $peta->skor_dampak }}</p>
                            <div class="form-group mt-3">
                                <label class="font-weight-bold">Catatan Review (Opsional)</label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Tambahkan catatan hasil review Anda..."></textarea>
                            </div>
                            <div class="alert alert-success">
                                <i class="fas fa-info-circle"></i>
                                Data akan diteruskan ke Admin SPI untuk diproses lebih lanjut.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Ya, Setujui & Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Reject --}}
        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle"></i> Tolak & Kembalikan ke Auditee
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('manajemen-risiko.auditor.reject', $peta->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p><strong>Data risiko akan dikembalikan ke Auditee untuk dilakukan perbaikan</strong></p>
                            <hr>
                            <p><strong>Unit:</strong> {{ $peta->jenis }}</p>
                            <p><strong>Judul:</strong> {{ $peta->judul }}</p>
                            <div class="form-group mt-3">
                                <label class="font-weight-bold">Alasan Penolakan & Perbaikan yang Diperlukan <span
                                        class="text-danger">*</span></label>
                                <textarea name="comment" class="form-control" rows="5"
                                    placeholder="Jelaskan secara detail:&#10;1. Alasan penolakan&#10;2. Perbaikan yang harus dilakukan&#10;3. Aspek yang perlu ditinjau ulang"
                                    required></textarea>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perhatian:</strong> Auditee akan menerima notifikasi dan wajib melakukan revisi
                                sesuai catatan Anda.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Ya, Tolak & Kembalikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        });
    </script>
@endpush
