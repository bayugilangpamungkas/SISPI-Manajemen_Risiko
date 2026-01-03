@extends('layout.app')
@section('title', 'Detail Risiko')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ route('manajemen-risiko.index') }}" class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <h1>Detail Risiko</h1>
            </div>

            <div class="section-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        {{-- Informasi Risiko --}}
                        <div class="card border-0 shadow rounded">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle"></i> Informasi Risiko</h4>
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
                                        <td>{{ $peta->judul }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td><span class="badge badge-secondary">{{ $peta->kategori }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Anggaran</th>
                                        <td>Rp {{ number_format($peta->anggaran, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pernyataan Risiko</th>
                                        <td>{{ $peta->pernyataan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Uraian Dampak</th>
                                        <td>{{ $peta->uraian }}</td>
                                    </tr>
                                    <tr>
                                        <th>Metode Pengendalian</th>
                                        <td>{{ $peta->metode }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Komentar --}}
                        <div class="card border-0 shadow rounded">
                            <div class="card-header">
                                <h4><i class="fas fa-comments"></i> Komentar & Catatan</h4>
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

                                <h6 class="font-weight-bold mb-3">Tambah Komentar Baru</h6>
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
                                        <textarea name="comment" class="form-control" rows="4" placeholder="Masukkan komentar Anda..." required></textarea>
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
                            <div class="card-header bg-primary text-white">
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
                                <h4><i class="fas fa-tasks"></i> Status</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="font-weight-bold d-block">Status Telaah:</label>
                                    @if ($peta->status_telaah)
                                        <span class="badge badge-success" style="font-size: 14px;">
                                            <i class="fas fa-check-circle"></i> Sudah Ditelaah
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
                                    @else
                                        <span class="badge badge-warning" style="font-size: 14px;">
                                            <i class="fas fa-clock"></i> Belum Ditelaah
                                        </span>
                                        <form action="{{ route('manajemen-risiko.update-status', $peta->id) }}"
                                            method="POST" class="mt-3"
                                            onsubmit="return confirm('Tandai risiko ini sebagai sudah ditelaah?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status_telaah" value="1">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-check"></i> Tandai Selesai Ditelaah
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <hr>

                                <div>
                                    <label class="font-weight-bold d-block">Jumlah Komentar:</label>
                                    <h4>
                                        <span class="badge badge-info">
                                            <i class="fas fa-comment"></i> {{ $peta->comment_prs->count() }} Komentar
                                        </span>
                                    </h4>
                                </div>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-hide success alerts
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        });
    </script>
@endpush
