@extends('layout.app')
@section('title', 'Isi Data Monitoring Risiko')

@section('main')
    @php
        $user = Auth::user();
        $unitKerjaUser = $user->unit_kerja->nama_unit_kerja ?? null;
    @endphp

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ route('manajemen-risiko.auditee.index') }}" class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <div>
                    <h1>Isi Data Monitoring Risiko</h1>
                    <small class="text-muted">Unit Kerja: {{ $unitKerjaUser }}</small>
                </div>
            </div>

            <div class="section-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Alert jika risiko ditolak --}}
                @if ($peta->koreksiPr == 'rejected')
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h5><i class="fas fa-exclamation-triangle"></i> Risiko Ditolak oleh Auditor</h5>
                        <p class="mb-2">Data risiko ini perlu diperbaiki sesuai catatan dari Auditor.</p>

                        @if ($peta->comment_prs->where('jenis', 'analisis')->last())
                            <hr>
                            <strong>Catatan Auditor:</strong>
                            <div class="bg-light p-3 rounded mt-2">
                                <p class="mb-0">{{ $peta->comment_prs->where('jenis', 'analisis')->last()->comment }}</p>
                                <small class="text-muted">
                                    <i class="far fa-clock"></i>
                                    {{ $peta->comment_prs->where('jenis', 'analisis')->last()->created_at->diffForHumans() }}
                                </small>
                            </div>
                        @endif
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <form action="{{ route('manajemen-risiko.auditee.update', $peta->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card border-0 shadow rounded">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="text-white"><i class="fas fa-info-circle"></i> Informasi Risiko</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">UNIT KERJA</label>
                                        <input type="text" class="form-control" value="{{ $peta->jenis }}" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">KODE REGISTRASI</label>
                                        <input type="text" class="form-control" value="{{ $peta->kode_regist }}"
                                            disabled>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">JUDUL RISIKO</label>
                                        <input type="text" class="form-control" value="{{ $peta->judul }}" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">KATEGORI</label>
                                        <input type="text" class="form-control" value="{{ $peta->kategori }}" disabled>
                                    </div>

                                    @if ($peta->kegiatan)
                                        <div class="form-group">
                                            <label class="font-weight-bold">KEGIATAN TERKAIT</label>
                                            <input type="text" class="form-control" value="{{ $peta->kegiatan->judul }}"
                                                disabled>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card border-0 shadow rounded">
                                <div class="card-header bg-success text-white">
                                    <h4 class="text-white"><i class="fas fa-edit"></i> Data Monitoring Risiko</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Petunjuk:</strong> Lengkapi data monitoring risiko di bawah ini dengan
                                        detail dan akurat. Data ini akan direview oleh Auditor.
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">PERNYATAAN RISIKO <span
                                                class="text-danger">*</span></label>
                                        <textarea name="pernyataan" class="form-control @error('pernyataan') is-invalid @enderror" rows="4"
                                            placeholder="Tuliskan pernyataan risiko secara jelas dan detail..." required>{{ old('pernyataan', $peta->pernyataan) }}</textarea>
                                        <small class="form-text text-muted">
                                            Contoh: "Risiko keterlambatan pengadaan barang yang dapat menghambat pelaksanaan
                                            kegiatan..."
                                        </small>
                                        @error('pernyataan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">URAIAN DAMPAK RISIKO <span
                                                class="text-danger">*</span></label>
                                        <textarea name="uraian" class="form-control @error('uraian') is-invalid @enderror" rows="4"
                                            placeholder="Uraikan dampak yang mungkin terjadi jika risiko ini terealisasi..." required>{{ old('uraian', $peta->uraian) }}</textarea>
                                        <small class="form-text text-muted">
                                            Jelaskan dampak terhadap waktu, biaya, kualitas, atau aspek lainnya.
                                        </small>
                                        @error('uraian')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">METODE PENGENDALIAN RISIKO <span
                                                class="text-danger">*</span></label>
                                        <textarea name="metode" class="form-control @error('metode') is-invalid @enderror" rows="4"
                                            placeholder="Jelaskan metode atau strategi untuk mengendalikan risiko ini..." required>{{ old('metode', $peta->metode) }}</textarea>
                                        <small class="form-text text-muted">
                                            Contoh: "Melakukan pemantauan berkala, menyiapkan vendor alternatif, atau
                                            mitigasi lainnya."
                                        </small>
                                        @error('metode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>

                                    <h6 class="font-weight-bold mb-3"><i class="fas fa-chart-bar"></i> Penilaian Risiko</h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">SKOR KEMUNGKINAN <span
                                                        class="text-danger">*</span></label>
                                                <select name="skor_kemungkinan"
                                                    class="form-control @error('skor_kemungkinan') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Skor --</option>
                                                    <option value="1"
                                                        {{ old('skor_kemungkinan', $peta->skor_kemungkinan) == 1 ? 'selected' : '' }}>
                                                        1 - Sangat Jarang (0-20%)
                                                    </option>
                                                    <option value="2"
                                                        {{ old('skor_kemungkinan', $peta->skor_kemungkinan) == 2 ? 'selected' : '' }}>
                                                        2 - Jarang (21-40%)
                                                    </option>
                                                    <option value="3"
                                                        {{ old('skor_kemungkinan', $peta->skor_kemungkinan) == 3 ? 'selected' : '' }}>
                                                        3 - Mungkin (41-60%)
                                                    </option>
                                                    <option value="4"
                                                        {{ old('skor_kemungkinan', $peta->skor_kemungkinan) == 4 ? 'selected' : '' }}>
                                                        4 - Sering (61-80%)
                                                    </option>
                                                    <option value="5"
                                                        {{ old('skor_kemungkinan', $peta->skor_kemungkinan) == 5 ? 'selected' : '' }}>
                                                        5 - Sangat Sering (81-100%)
                                                    </option>
                                                </select>
                                                @error('skor_kemungkinan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">SKOR DAMPAK <span
                                                        class="text-danger">*</span></label>
                                                <select name="skor_dampak"
                                                    class="form-control @error('skor_dampak') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Skor --</option>
                                                    <option value="1"
                                                        {{ old('skor_dampak', $peta->skor_dampak) == 1 ? 'selected' : '' }}>
                                                        1 - Sangat Rendah
                                                    </option>
                                                    <option value="2"
                                                        {{ old('skor_dampak', $peta->skor_dampak) == 2 ? 'selected' : '' }}>
                                                        2 - Rendah
                                                    </option>
                                                    <option value="3"
                                                        {{ old('skor_dampak', $peta->skor_dampak) == 3 ? 'selected' : '' }}>
                                                        3 - Sedang
                                                    </option>
                                                    <option value="4"
                                                        {{ old('skor_dampak', $peta->skor_dampak) == 4 ? 'selected' : '' }}>
                                                        4 - Tinggi
                                                    </option>
                                                    <option value="5"
                                                        {{ old('skor_dampak', $peta->skor_dampak) == 5 ? 'selected' : '' }}>
                                                        5 - Sangat Tinggi
                                                    </option>
                                                </select>
                                                @error('skor_dampak')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-calculator"></i>
                                        <strong>Skor Total:</strong> Akan dihitung otomatis (Kemungkinan × Dampak)
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mb-4">
                                <a href="{{ route('manajemen-risiko.auditee.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">
                        {{-- Info Current Risk Score --}}
                        @if ($peta->skor_kemungkinan && $peta->skor_dampak)
                            <div class="card border-0 shadow rounded">
                                <div class="card-header bg-info text-white">
                                    <h4 class="text-white"><i class="fas fa-chart-line"></i> Skor Risiko Saat Ini</h4>
                                </div>
                                <div class="card-body text-center">
                                    <h1 class="display-3 font-weight-bold">
                                        {{ $peta->skor_kemungkinan * $peta->skor_dampak }}
                                    </h1>
                                    <p class="text-muted">
                                        {{ $peta->skor_kemungkinan }} × {{ $peta->skor_dampak }}
                                    </p>
                                    <hr>
                                    @php
                                        $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;
                                        if ($skorTotal >= 20) {
                                            $badgeClass = 'badge-danger';
                                            $badgeText = 'Extreme';
                                        } elseif ($skorTotal >= 15) {
                                            $badgeClass = 'badge-warning';
                                            $badgeText = 'High';
                                        } elseif ($skorTotal >= 10) {
                                            $badgeClass = 'badge-info';
                                            $badgeText = 'Moderate';
                                        } else {
                                            $badgeClass = 'badge-success';
                                            $badgeText = 'Low';
                                        }
                                    @endphp
                                    <h4>
                                        <span class="badge {{ $badgeClass }}" style="font-size: 16px;">
                                            {{ $badgeText }}
                                        </span>
                                    </h4>
                                </div>
                            </div>
                        @endif

                        {{-- Help Card --}}
                        <div class="card border-0 shadow rounded">
                            <div class="card-header bg-warning text-white">
                                <h4 class="text-white"><i class="fas fa-question-circle"></i> Bantuan</h4>
                            </div>
                            <div class="card-body">
                                <h6 class="font-weight-bold">Panduan Pengisian:</h6>
                                <ol class="pl-3 mb-3">
                                    <li class="mb-2">Baca dengan teliti <strong>catatan dari Auditor</strong> jika ada
                                    </li>
                                    <li class="mb-2">Isi semua field yang bertanda <span class="text-danger">*</span>
                                    </li>
                                    <li class="mb-2">Berikan penjelasan yang <strong>detail dan akurat</strong></li>
                                    <li class="mb-2">Pilih skor sesuai kondisi <strong>aktual</strong></li>
                                    <li class="mb-2">Setelah menyimpan, <strong>submit</strong> ke Auditor</li>
                                </ol>

                                <hr>

                                <h6 class="font-weight-bold">Kategori Tingkat Risiko:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <span class="badge badge-success">Low (1-9)</span>
                                        <small class="d-block text-muted">Risiko dapat diterima</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge badge-info">Moderate (10-14)</span>
                                        <small class="d-block text-muted">Perlu pemantauan</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge badge-warning">High (15-19)</span>
                                        <small class="d-block text-muted">Perlu tindakan segera</small>
                                    </li>
                                    <li class="mb-2">
                                        <span class="badge badge-danger">Extreme (20-25)</span>
                                        <small class="d-block text-muted">Sangat prioritas</small>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Riwayat Komentar --}}
                        @if ($peta->comment_prs->count() > 0)
                            <div class="card border-0 shadow rounded">
                                <div class="card-header">
                                    <h4><i class="fas fa-comments"></i> Riwayat Komentar</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($peta->comment_prs->take(3) as $comment)
                                        <div class="alert alert-light border mb-2">
                                            <small class="font-weight-bold text-primary d-block">
                                                {{ $comment->user->name ?? 'Unknown' }}
                                            </small>
                                            <small class="text-muted d-block mb-1">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </small>
                                            <p class="mb-0 small">{{ Str::limit($comment->comment, 100) }}</p>
                                        </div>
                                    @endforeach

                                    @if ($peta->comment_prs->count() > 3)
                                        <a href="{{ route('manajemen-risiko.auditee.show', $peta->id) }}"
                                            class="btn btn-sm btn-link">
                                            Lihat semua komentar →
                                        </a>
                                    @endif
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
            // Real-time risk score calculator
            $('select[name="skor_kemungkinan"], select[name="skor_dampak"]').on('change', function() {
                var kemungkinan = parseInt($('select[name="skor_kemungkinan"]').val()) || 0;
                var dampak = parseInt($('select[name="skor_dampak"]').val()) || 0;
                var total = kemungkinan * dampak;

                if (total > 0) {
                    var badgeClass, badgeText;

                    if (total >= 20) {
                        badgeClass = 'badge-danger';
                        badgeText = 'Extreme';
                    } else if (total >= 15) {
                        badgeClass = 'badge-warning';
                        badgeText = 'High';
                    } else if (total >= 10) {
                        badgeClass = 'badge-info';
                        badgeText = 'Moderate';
                    } else {
                        badgeClass = 'badge-success';
                        badgeText = 'Low';
                    }

                    // Show preview if card exists
                    if ($('.card-body .display-3').length) {
                        $('.card-body .display-3').text(total);
                        $('.card-body .text-muted').text(kemungkinan + ' × ' + dampak);
                        $('.card-body .badge').removeClass(
                                'badge-success badge-info badge-warning badge-danger').addClass(badgeClass)
                            .text(badgeText);
                    }
                }
            });

            // Form validation feedback
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]').html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
            });
        });
    </script>
@endpush
