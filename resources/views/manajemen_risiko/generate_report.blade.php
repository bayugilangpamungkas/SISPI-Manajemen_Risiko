@extends('layout.app')
@section('title', 'Generate Laporan Risiko')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ route('manajemen-risiko.index') }}" class="mr-3">
                    <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                </a>
                <h1>Generate Laporan Risiko</h1>
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

                {{-- Summary Card --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm bg-primary text-white">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="text-white mb-2">
                                            <i class="fas fa-building"></i> {{ $unitKerja }}
                                        </h4>
                                        <p class="mb-0">Laporan Peta Risiko Tahun {{ $tahun }}</p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <h2 class="text-white mb-0">{{ $petas->count() }}</h2>
                                        <p class="mb-0">Total Risiko</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    @php
                        $extreme = $petas
                            ->filter(function ($p) {
                                return $p->skor_kemungkinan * $p->skor_dampak >= 20;
                            })
                            ->count();
                        $high = $petas
                            ->filter(function ($p) {
                                $skor = $p->skor_kemungkinan * $p->skor_dampak;
                                return $skor >= 15 && $skor < 20;
                            })
                            ->count();
                        $moderate = $petas
                            ->filter(function ($p) {
                                $skor = $p->skor_kemungkinan * $p->skor_dampak;
                                return $skor >= 10 && $skor < 15;
                            })
                            ->count();
                        $low = $petas
                            ->filter(function ($p) {
                                return $p->skor_kemungkinan * $p->skor_dampak < 10;
                            })
                            ->count();
                    @endphp

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                                </div>
                                <h3 class="font-weight-bold text-danger">{{ $extreme }}</h3>
                                <p class="text-muted mb-0">Risiko Extreme</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-exclamation-circle fa-3x text-warning"></i>
                                </div>
                                <h3 class="font-weight-bold text-warning">{{ $high }}</h3>
                                <p class="text-muted mb-0">Risiko High</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-info-circle fa-3x text-info"></i>
                                </div>
                                <h3 class="font-weight-bold text-info">{{ $moderate }}</h3>
                                <p class="text-muted mb-0">Risiko Moderate</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-check-circle fa-3x text-success"></i>
                                </div>
                                <h3 class="font-weight-bold text-success">{{ $low }}</h3>
                                <p class="text-muted mb-0">Risiko Low</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-alt"></i> Aksi Laporan
                                        </h5>
                                        <small class="text-muted">Cetak atau ekspor laporan risiko</small>
                                    </div>
                                    <div>
                                        <button onclick="window.print()" class="btn btn-primary mr-2">
                                            <i class="fas fa-print"></i> Cetak Laporan
                                        </button>
                                        <a href="{{ route('manajemen-risiko.export') }}?unit_kerja={{ $unitKerja }}&tahun={{ $tahun }}"
                                            class="btn btn-success">
                                            <i class="fas fa-file-excel"></i> Export Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h4><i class="fas fa-table"></i> Daftar Risiko {{ $unitKerja }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr class="text-center">
                                                <th width="3%">No</th>
                                                <th width="10%">Kode Registrasi</th>
                                                <th width="7%">Kategori</th>
                                                <th width="15%">Judul Risiko</th>
                                                <th width="15%">Pernyataan Risiko</th>
                                                <th width="10%">Auditor</th>
                                                <th width="5%">Kemungkinan</th>
                                                <th width="5%">Dampak</th>
                                                <th width="5%">Skor</th>
                                                <th width="10%">Tingkat</th>
                                                <th width="15%">Metode Pengendalian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($petas as $index => $peta)
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
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary">{{ $peta->kode_regist }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-light">{{ $peta->kategori }}</span>
                                                    </td>
                                                    <td>{{ $peta->judul }}</td>
                                                    <td>{{ Str::limit($peta->pernyataan, 50) }}</td>
                                                    <td class="text-center">
                                                        @if ($peta->auditor)
                                                            <small>
                                                                <i class="fas fa-user"></i>
                                                                {{ $peta->auditor->name }}
                                                            </small>
                                                        @else
                                                            <small class="text-muted">-</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ $peta->skor_kemungkinan }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ $peta->skor_dampak }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong class="text-primary">{{ $skorTotal }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                                    </td>
                                                    <td>{{ Str::limit($peta->metode, 40) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upload Laporan Section --}}
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h4 class="text-white">
                                    <i class="fas fa-upload"></i> Upload Laporan
                                </h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Upload 2 file laporan: 1 untuk Unit Kerja dan 1 untuk SPI (Format: PDF, DOC, DOCX | Max:
                                    5MB)
                                </p>

                                <div class="row">
                                    @foreach ($petas as $peta)
                                        <div class="col-md-12 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold mb-3">
                                                        {{ $peta->judul }}
                                                        <small class="text-muted">({{ $peta->kode_regist }})</small>
                                                    </h6>

                                                    <form
                                                        action="{{ route('manajemen-risiko.upload-report', $peta->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label class="font-weight-bold">
                                                                        <i class="fas fa-file-pdf text-danger"></i> Laporan
                                                                        untuk Unit Kerja
                                                                    </label>
                                                                    @if ($peta->laporan_unit)
                                                                        <div class="alert alert-success py-2">
                                                                            <i class="fas fa-check-circle"></i>
                                                                            File sudah diupload:
                                                                            <a href="{{ asset('storage/laporan_unit/' . $peta->laporan_unit) }}"
                                                                                target="_blank" class="text-primary">
                                                                                {{ $peta->laporan_unit }}
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" name="laporan_unit"
                                                                        class="form-control-file"
                                                                        accept=".pdf,.doc,.docx">
                                                                    <small class="text-muted">Upload ulang untuk mengganti
                                                                        file</small>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label class="font-weight-bold">
                                                                        <i class="fas fa-file-pdf text-primary"></i>
                                                                        Laporan untuk SPI
                                                                    </label>
                                                                    @if ($peta->laporan_spi)
                                                                        <div class="alert alert-success py-2">
                                                                            <i class="fas fa-check-circle"></i>
                                                                            File sudah diupload:
                                                                            <a href="{{ asset('storage/laporan_spi/' . $peta->laporan_spi) }}"
                                                                                target="_blank" class="text-primary">
                                                                                {{ $peta->laporan_spi }}
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" name="laporan_spi"
                                                                        class="form-control-file"
                                                                        accept=".pdf,.doc,.docx">
                                                                    <small class="text-muted">Upload ulang untuk mengganti
                                                                        file</small>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-2 d-flex align-items-center">
                                                                <button type="submit" class="btn btn-primary btn-block">
                                                                    <i class="fas fa-upload"></i> Upload
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .main-sidebar,
            .navbar,
            .section-header a,
            .btn,
            form,
            .no-print {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .section-body {
                padding: 20px !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endpush

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
