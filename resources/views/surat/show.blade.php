@extends('layout.app')
@section('title', 'Detail Surat')

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- ========== HEADER SECTION ========== --}}
            <div class="section-header mb-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('surat.index') }}" class="btn btn-light btn-sm mr-3 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700; color: #2c3e50;">
                                Detail Surat
                            </h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                Informasi lengkap surat {{ $surat->nomor_surat }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- ========== ACTION BUTTONS ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-info-circle text-primary mr-2"></i>Aksi Surat
                            </h6>
                            <div>
                                @if ($surat->file_pdf)
                                    <a href="{{ route('surat.download-pdf', $surat->id) }}" class="btn btn-success btn-lg"
                                        target="_blank">
                                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                    </a>
                                    <a href="{{ route('surat.print', $surat->id) }}" class="btn btn-primary btn-lg"
                                        target="_blank">
                                        <i class="fas fa-print mr-2"></i> Cetak
                                    </a>
                                @endif
                                @if ($surat->status == 'Draft')
                                    <a href="{{ route('surat.edit', $surat->id) }}" class="btn btn-warning btn-lg">
                                        <i class="fas fa-edit mr-2"></i> Edit Surat
                                    </a>
                                @endif
                                <a href="{{ route('surat.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== INFORMASI SURAT ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-envelope mr-2"></i>Informasi Surat
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="info-group mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-hashtag mr-1 text-primary"></i> NOMOR SURAT
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        <span class="badge badge-secondary p-2" style="font-size: 1rem;">
                                            {{ $surat->nomor_surat }}
                                        </span>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-file-alt mr-1 text-primary"></i> JENIS SURAT
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        <span class="badge badge-info p-2">{{ $surat->jenis_surat }}</span>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-calendar-alt mr-1 text-primary"></i> TANGGAL SURAT
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        <strong>{{ $surat->tanggal_surat->format('d F Y') }}</strong>
                                    </div>
                                </div>

                                <div class="info-group">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-users mr-1 text-primary"></i> TUJUAN SURAT
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        {{ $surat->tujuan_surat }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-group mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-clipboard-check mr-1 text-primary"></i> STATUS
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        @if ($surat->status == 'Draft')
                                            <span class="badge badge-warning p-2">
                                                <i class="fas fa-edit mr-1"></i> Draft
                                            </span>
                                        @else
                                            <span class="badge badge-success p-2">
                                                <i class="fas fa-check-circle mr-1"></i> Final
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-link mr-1 text-primary"></i> TIPE REFERENSI
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        <span class="badge badge-secondary p-2">{{ $surat->tipe_referensi }}</span>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-user mr-1 text-primary"></i> DIBUAT OLEH
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        {{ $surat->creator->name ?? '-' }}
                                    </div>
                                </div>

                                <div class="info-group">
                                    <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                        <i class="fas fa-clock mr-1 text-primary"></i> DIBUAT PADA
                                    </label>
                                    <div class="info-value p-3 bg-light rounded">
                                        {{ $surat->created_at->format('d F Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== PERIHAL & ISI SURAT ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-primary">
                            <i class="fas fa-file-alt mr-2"></i>Perihal & Isi Surat
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                <i class="fas fa-bookmark mr-1 text-primary"></i> PERIHAL
                            </label>
                            <div class="p-3 bg-light rounded border" style="line-height: 1.6;">
                                {{ $surat->perihal }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark mb-2" style="font-size: 0.875rem;">
                                <i class="fas fa-align-left mr-1 text-primary"></i> ISI SURAT
                            </label>
                            <div class="p-4 bg-light rounded border isi-surat-preview"
                                style="font-family: 'Times New Roman', Times, serif; font-size: 14px; line-height: 1.9; text-align: justify; min-height: 120px;">
                                {!! $surat->isi_surat !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== INFORMASI REFERENSI ========== --}}
                @if ($surat->tipe_referensi != 'Tanpa Referensi')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-success text-white py-3">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-link mr-2"></i>Informasi Referensi
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $kodeKegiatan = '-';
                                $peta = $surat->petaRisiko;

                                if ($peta && $peta->kegiatan) {
                                    $keg = $peta->kegiatan;
                                    if (!empty($keg->kode_regist)) {
                                        $kodeKegiatan = $keg->kode_regist;
                                    } elseif (!empty($keg->id_kegiatan)) {
                                        $kodeKegiatan = $keg->id_kegiatan;
                                    } elseif (!empty($keg->kode)) {
                                        $kodeKegiatan = $keg->kode;
                                    } else {
                                        $kodeKegiatan =
                                            'KEG-' . date('Y') . '-' . str_pad($keg->id, 3, '0', STR_PAD_LEFT);
                                    }
                                } elseif ($peta && $peta->id_kegiatan) {
                                    $kegiatanManual = \App\Models\Kegiatan::find($peta->id_kegiatan);
                                    if ($kegiatanManual) {
                                        $kodeKegiatan =
                                            $kegiatanManual->kode_regist ??
                                            'KEG-' .
                                                date('Y') .
                                                '-' .
                                                str_pad($kegiatanManual->id, 3, '0', STR_PAD_LEFT);
                                    }
                                }
                            @endphp

                            <div class="alert alert-success border-0 mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-link fa-2x mr-3"></i>
                                    <div>
                                        <strong style="font-size: 1rem;">{{ $surat->tipe_referensi }}:</strong>
                                        <div class="mt-2">
                                            <span class="badge badge-light text-dark p-2" style="font-size: 0.95rem;">
                                                {{ $kodeKegiatan }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .card {
            border-radius: 0.5rem;
        }

        .info-group {
            transition: all 0.3s ease;
        }

        .info-value {
            font-size: 0.95rem;
            color: #2c3e50;
            min-height: 50px;
            display: flex;
            align-items: center;
        }

        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
        }
    </style>
@endpush
