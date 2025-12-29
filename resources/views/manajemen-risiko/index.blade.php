@extends('layout.app')
@section('title', 'Manajemen Risiko')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Risiko</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Manajemen Risiko</div>
                </div>
            </div>

            <div class="section-body">
                {{-- Filter Tahun
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Filter Data</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('manajemen-risiko.index') }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tahun</label>
                                                <select name="tahun" class="form-control" onchange="this.form.submit()">
                                                    @foreach ($years as $year)
                                                        <option value="{{ $year }}"
                                                            {{ $tahun == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- Statistik Cards --}}
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="far fa-newspaper"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Risiko</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalRisiko }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Risiko Extreme</h4>
                                </div>
                                <div class="card-body">
                                    {{ $risikoExtreme }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Risiko High</h4>
                                </div>
                                <div class="card-body">
                                    {{ $risikoHigh }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Unit Kerja</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalUnitKerja }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Clustering berdasarkan Unit Kerja --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Clustering Risiko Berdasarkan Unit Kerja</h4>
                                <div class="card-header-action">
                                    <a href="#" class="btn btn-primary">Export PDF</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Unit Kerja</th>
                                                <th>Total Risiko</th>
                                                <th>Rata-rata Skor</th>
                                                <th>Skor Tertinggi</th>
                                                <th>Extreme</th>
                                                <th>High</th>
                                                <th>Middle</th>
                                                <th>Low</th>
                                                <th>Very Low</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($clusteringByUnit as $index => $cluster)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $cluster->jenis }}</strong></td>
                                                    <td>{{ $cluster->total_risiko }}</td>
                                                    <td>{{ number_format($cluster->rata_rata_skor, 2) }}</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-danger">{{ $cluster->skor_tertinggi }}</span>
                                                    </td>
                                                    <td>{{ $cluster->extreme }}</td>
                                                    <td>{{ $cluster->high }}</td>
                                                    <td>{{ $cluster->middle }}</td>
                                                    <td>{{ $cluster->low }}</td>
                                                    <td>{{ $cluster->very_low }}</td>
                                                    <td>
                                                        <a href="{{ route('manajemen-risiko.detail', $cluster->jenis) }}?tahun={{ $tahun }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Clustering berdasarkan Tingkat Risiko --}}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Clustering Berdasarkan Tingkat Risiko</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tingkat Risiko</th>
                                                <th>Total</th>
                                                <th>Rata-rata Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clusteringByRisk as $risk)
                                                <tr>
                                                    <td>
                                                        @if ($risk->tingkat_risiko == 'EXTREME')
                                                            <span
                                                                class="badge badge-danger">{{ $risk->tingkat_risiko }}</span>
                                                        @elseif($risk->tingkat_risiko == 'HIGH')
                                                            <span
                                                                class="badge badge-warning">{{ $risk->tingkat_risiko }}</span>
                                                        @elseif($risk->tingkat_risiko == 'MIDDLE')
                                                            <span
                                                                class="badge badge-info">{{ $risk->tingkat_risiko }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-success">{{ $risk->tingkat_risiko }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $risk->total }}</td>
                                                    <td>{{ number_format($risk->rata_rata_skor, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Clustering Berdasarkan Kategori Risiko</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kategori Risiko</th>
                                                <th>Total</th>
                                                <th>Rata-rata Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clusteringByCategory as $category)
                                                <tr>
                                                    <td>{{ $category->kategori }}</td>
                                                    <td>{{ $category->total }}</td>
                                                    <td>{{ number_format($category->rata_rata_skor, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top 10 Risiko Tertinggi --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Top 10 Risiko Tertinggi</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ranking</th>
                                                <th>Kode Registrasi</th>
                                                <th>Judul</th>
                                                <th>Unit Kerja</th>
                                                <th>Skor Total</th>
                                                <th>Tingkat Risiko</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($topRisks as $index => $risk)
                                                <tr>
                                                    <td>
                                                        @if ($index == 0)
                                                            <span class="badge badge-danger">{{ $index + 1 }}</span>
                                                        @elseif($index == 1)
                                                            <span class="badge badge-warning">{{ $index + 1 }}</span>
                                                        @elseif($index == 2)
                                                            <span class="badge badge-info">{{ $index + 1 }}</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ $index + 1 }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $risk->kode_regist }}</td>
                                                    <td>{{ $risk->judul }}</td>
                                                    <td>{{ $risk->jenis }}</td>
                                                    <td><strong>{{ $risk->skor_total }}</strong></td>
                                                    <td>
                                                        @if ($risk->tingkat_risiko == 'EXTREME')
                                                            <span
                                                                class="badge badge-danger">{{ $risk->tingkat_risiko }}</span>
                                                        @elseif($risk->tingkat_risiko == 'HIGH')
                                                            <span
                                                                class="badge badge-warning">{{ $risk->tingkat_risiko }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-info">{{ $risk->tingkat_risiko }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
