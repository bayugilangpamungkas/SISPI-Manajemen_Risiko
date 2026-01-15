@extends('layout.app')
@section('title', 'Detail Unit Kerja - ' . $unitKerja)

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('manajemen-risiko.data', ['tahun' => $tahun]) }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Detail Unit Kerja: {{ $unitKerja }}</h1>
                        <small class="text-muted">Daftar kegiatan dan risiko untuk unit kerja {{ $unitKerja }} - Tahun
                            {{ $tahun }}</small>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- STATISTICS CARDS --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Kegiatan</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['total_kegiatan'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Risiko</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['total_risiko'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Sudah Ditampilkan</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['total_tampil'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Skor Risiko</h4>
                                </div>
                                <div class="card-body">
                                    {{ $statistics['total_skor_unit'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FILTER SECTION --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-header bg-light border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="fas fa-filter"></i> Filter Tahun
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="GET"
                                    action="{{ route('manajemen-risiko.detail-unit', ['unitKerja' => $unitKerja]) }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="font-weight-bold small">TAHUN</label>
                                            <select name="tahun" class="form-control" onchange="this.form.submit()">
                                                @foreach (range(date('Y'), date('Y') - 5) as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $tahun == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-9 d-flex align-items-end">
                                            <a href="{{ route('manajemen-risiko.data', ['tahun' => $tahun]) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Kembali ke Data Manajemen Risiko
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE SECTION --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-header bg-light border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="fas fa-list"></i> Daftar Kegiatan dengan Risiko
                                </h6>
                            </div>
                            <div class="card-body">
                                @if (count($kegiatans->items()) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-primary text-white">
                                                <tr class="text-center">
                                                    <th width="5%">No</th>
                                                    <th width="10%">ID Kegiatan</th>
                                                    <th width="25%">Nama Kegiatan</th>
                                                    <th width="10%">Jumlah Risiko</th>
                                                    <th width="12%">Sudah Ditampilkan</th>
                                                    <th width="12%">Total Skor Risiko</th>
                                                    <th width="26%">Preview Risiko</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = ($kegiatans->currentPage() - 1) * $kegiatans->perPage() + 1; @endphp
                                                @foreach ($kegiatans as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $no++ }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-secondary" style="font-size: 13px;">
                                                                {{ $item['kegiatan']->id_kegiatan }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $item['kegiatan']->judul }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-warning badge-lg"
                                                                style="font-size: 16px; padding: 8px 12px;">
                                                                {{ $item['jumlah_risiko'] }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-success badge-lg"
                                                                style="font-size: 16px; padding: 8px 12px;">
                                                                {{ $item['sudah_tampil'] }} / {{ $item['jumlah_risiko'] }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-danger badge-lg"
                                                                style="font-size: 18px; padding: 10px 15px;">
                                                                {{ $item['total_skor_risiko'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($item['petas']->count() > 0)
                                                                <ul class="list-unstyled mb-0" style="font-size: 12px;">
                                                                    @foreach ($item['petas']->take(3) as $peta)
                                                                        <li class="mb-2">
                                                                            <span
                                                                                class="badge badge-{{ $peta->tingkat_risiko == 'Extreme'
                                                                                    ? 'danger'
                                                                                    : ($peta->tingkat_risiko == 'High'
                                                                                        ? 'warning'
                                                                                        : ($peta->tingkat_risiko == 'Moderate'
                                                                                            ? 'info'
                                                                                            : 'secondary')) }}">
                                                                                {{ $peta->tingkat_risiko }}
                                                                            </span>
                                                                            {{ \Illuminate\Support\Str::limit($peta->judul, 40) }}
                                                                            @if ($peta->tampil_manajemen_risiko == 1)
                                                                                <i class="fas fa-check-circle text-success"
                                                                                    title="Sudah ditampilkan"></i>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                    @if ($item['petas']->count() > 3)
                                                                        <li class="text-muted">
                                                                            <small><i class="fas fa-ellipsis-h"></i> dan
                                                                                {{ $item['petas']->count() - 3 }} risiko
                                                                                lainnya</small>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            @else
                                                                <span class="text-muted"><i class="fas fa-info-circle"></i>
                                                                    Tidak ada risiko</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Pagination --}}
                                    <div class="mt-3">
                                        {{ $kegiatans->appends(['tahun' => $tahun])->links('pagination::bootstrap-4') }}
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        Tidak ada kegiatan dengan risiko untuk unit kerja
                                        <strong>{{ $unitKerja }}</strong> pada tahun {{ $tahun }}.
                                    </div>
                                @endif
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
        .badge-lg {
            font-size: 14px;
            padding: 6px 10px;
        }

        .table thead th {
            vertical-align: middle;
        }
    </style>
@endpush
