@extends('layout.app')
@section('title', 'Detail Risiko')

@section('main')
    @php
        $user = Auth::user();
        $isAuditor = in_array($user->Level->name ?? '', ['Ketua', 'Anggota', 'Sekretaris']);
        $isAdmin = in_array($user->Level->name ?? '', ['Super Admin', 'Admin']);
        $isAuditee = !$isAdmin && !$isAuditor;
    @endphp

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ $isAuditee ? route('manajemen-risiko.auditee.index') : ($isAuditor ? route('manajemen-risiko.auditor.index') : route('manajemen-risiko.index')) }}"
                    class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <h1>LEMBAR MONITORING DAN EVALUASI MANAJEMEN RISIKO UNIT</h1>
            </div>

            <div class="section-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Form Monitoring --}}
                <div class="card border-0 shadow rounded">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-white"><i class="fas fa-file-alt"></i> Lembar Monitoring dan Evaluasi</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manajemen-risiko.auditor.show', $peta->id) }}" method="POST">
                            @csrf

                            {{-- Header Information --}}
                            <div class="form-row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">UNIT</label>
                                        <input type="text" class="form-control-plaintext"
                                            value="{{ $peta->jenis ?? '-' }}" readonly>
                                        <small class="form-text text-muted">SATUAN PENGGAWAS INTERNAL</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">PEMONEV</label>
                                        <input type="text" class="form-control-plaintext"
                                            value="{{ $user->name ?? '-' }}" readonly>
                                        <small class="form-text text-muted">POLITEKNIK NEGERI MALANG</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Information --}}
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th width="20%">KODE RISIKO</th>
                                    <td>
                                        <input type="text" class="form-control-plaintext"
                                            value="{{ $peta->kode_regist ?? '-' }}" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tahun Anggaran Kegiatan</th>
                                    <td>
                                        <input type="text" class="form-control-plaintext" value="{{ date('Y') }}"
                                            readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th>KEGIATAN</th>
                                    <td>
                                        <input type="text" class="form-control-plaintext"
                                            value="{{ $peta->kegiatan->judul ?? $peta->judul }}" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th>PERNYATAAN RISIKO</th>
                                    <td>
                                        <textarea class="form-control-plaintext" rows="3" readonly>{{ $peta->pernyataan ?? '-' }}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>LEVEL RISIKO</th>
                                    <td>
                                        @php
                                            $skorTotal = $peta->skor_kemungkinan * $peta->skor_dampak;
                                            if ($skorTotal >= 20) {
                                                $levelText = 'HIGH';
                                                $badgeClass = 'badge-danger';
                                            } elseif ($skorTotal >= 15) {
                                                $levelText = 'HIGH';
                                                $badgeClass = 'badge-danger';
                                            } elseif ($skorTotal >= 10) {
                                                $levelText = 'MODERATE';
                                                $badgeClass = 'badge-warning';
                                            } else {
                                                $levelText = 'LOW';
                                                $badgeClass = 'badge-success';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} p-2" style="font-size: 16px;">
                                            {{ $levelText }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>RISIKO RESIDUAL</th>
                                    <td>
                                        @php
                                            if ($skorTotal >= 20) {
                                                $residualText = 'Extreme';
                                                $residualClass = 'badge-danger';
                                            } elseif ($skorTotal >= 15) {
                                                $residualText = 'High';
                                                $residualClass = 'badge-warning';
                                            } elseif ($skorTotal >= 10) {
                                                $residualText = 'Moderate';
                                                $residualClass = 'badge-info';
                                            } else {
                                                $residualText = 'Low';
                                                $residualClass = 'badge-success';
                                            }
                                        @endphp
                                        <span class="badge {{ $residualClass }} p-2" style="font-size: 16px;">
                                            {{ $residualText }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Pengendalian --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">PENGENDALIAN</label>
                                <small class="form-text text-muted mb-2">
                                    Untuk mengatasi risiko yang mungkin terjadi, maka cara mengendalikan dampaknya adalah:
                                </small>
                                <textarea name="pengendalian" class="form-control" rows="4"
                                    placeholder="Contoh: mengikuti sosialisasi online atau menggunakan paus untuk mengadakan sosialisasi tentang kurikulum merdeka belajar."
                                    required>{{ old('pengendalian') }}</textarea>
                            </div>

                            {{-- Mitigasi Risiko --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">MITIGASI RISIKO</label>
                                <input type="text" name="mitigasi" class="form-control"
                                    placeholder="Contoh: Menemani Risiko" value="{{ old('mitigasi') }}" required>
                            </div>

                            {{-- Komentar --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">KOMENTAR</label>
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label>1. Sentralisasi Repositori Bukti</label>
                                            <textarea name="komentar_1" class="form-control" rows="2"
                                                placeholder="Buat penyimpanan digital terpusat dan berindeks untuk semua bukti pendukung." required>{{ old('komentar_1') }}</textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>2. Finalisasi LKPS & Cross-Check</label>
                                            <textarea name="komentar_2" class="form-control" rows="2"
                                                placeholder="Perepet penyelesaian draft Laporan Kinerja Program Studi (LKPS) dan lakukan verifikasi silang (cross-check) bahwa semua klaim dilaporkan didukung bukti yang tersedia."
                                                required>{{ old('komentar_2') }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>3. Koordinasi Khusus Keuangan</label>
                                            <textarea name="komentar_3" class="form-control" rows="2"
                                                placeholder="Lakukan koordinasi intensif dengan Pihak Keuangan untuk mempercepat pencarian data ke gajian yang harusnya akan menjadi bukti kritis akreditas."
                                                required>{{ old('komentar_3') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Konfirmasi --}}
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Status Konfirmasi</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Auditee</label>
                                            <select name="status_konfirmasi_auditee" class="form-control">
                                                <option value="">- Pilih -</option>
                                                <option value="disetujui">Disetujui</option>
                                                <option value="ditolak">Ditolak</option>
                                                <option value="perlu_revisi">Perlu Revisi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Auditor</label>
                                            <select name="status_konfirmasi_auditor" class="form-control">
                                                <option value="">- Pilih -</option>
                                                <option value="disetujui">Disetujui</option>
                                                <option value="ditolak">Ditolak</option>
                                                <option value="perlu_revisi">Perlu Revisi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tanda Tangan --}}
                            <div class="row mt-5">
                                <div class="col-md-6 text-center">
                                    <p class="mb-1">Auditee</p>
                                    <div class="signature-placeholder"
                                        style="height: 100px; border-bottom: 2px solid #333; margin-bottom: 10px;"></div>
                                    <p class="mb-0">
                                        <strong>{{ $peta->user->name ?? '-' }}</strong><br>
                                        <small>{{ $peta->user->nip ?? 'NIP: -' }}</small>
                                    </p>
                                </div>
                                <div class="col-md-6 text-center">
                                    <p class="mb-1">Pemonev (Auditor)</p>
                                    <div class="signature-placeholder"
                                        style="height: 100px; border-bottom: 2px solid #333; margin-bottom: 10px;"></div>
                                    <p class="mb-0">
                                        <strong>{{ $user->name }}</strong><br>
                                        <small>NIP: {{ $user->nip ?? '-' }}</small>
                                    </p>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <p class="text-muted">
                                    Malang, {{ date('d/m/Y') }}
                                </p>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-5 pt-4 border-top">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('manajemen-risiko.auditor.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-danger mr-2" data-toggle="modal"
                                            data-target="#rejectModal">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Simpan & Setujui
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
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
@endsection

@push('styles')
    <style>
        .form-control-plaintext {
            border: none !important;
            background: transparent !important;
            padding-left: 0 !important;
            font-weight: 500;
        }

        .signature-placeholder {
            width: 80%;
            margin: 0 auto;
        }

        table.table-bordered th {
            background-color: #f8f9fa;
            vertical-align: middle;
        }
    </style>
@endpush

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
