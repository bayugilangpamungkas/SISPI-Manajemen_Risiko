@extends('layout.app')
@section('title', 'Peta Risiko')
@section('main')

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('peta.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Peta Risiko</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih file Excel</label>
                            <input type="file" name="file" class="form-control" required accept=".xls, .xlsx">
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <a href="{{ route('kegiatan.template') }}" class="btn btn-success">Download Template</a> --}}
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left"
                        style="font-size: 1.3rem"></i></a>
                <h1>Peta Risiko</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Dokumen</span>
                </h4>
                <!-- Rekapitulasi -->
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Rekapitulasi:</strong>
                            <ul>
                                <li>Jumlah Dokumen <span class="badge badge-success">Disetujui</span> : {{ $approvedCount }}
                                </li>
                                <li>Jumlah Dokumen <span class="badge badge-danger">Ditolak</span> : {{ $rejectedCount }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                {{-- search bar --}}
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <form action="{{ route('petaRisiko.search') }}" method="GET">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control float-right"
                                    placeholder="Search: Masukkan Judul atau Tahun">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Filter Jenis -->
                {{-- @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2 || auth()->user()->id_level == 3 || auth()->user()->id_level == 4 || auth()->user()->id_level == 6)
            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('petas.index') }}" method="GET">
                        <div class="input-group">
                            <select name="jenis" class="form-control">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($unitKerjas as $unitKerja)
                                    <option value="{{ $unitKerja->nama_unit_kerja }}"
                                        {{ request('jenis') == $unitKerja->nama_unit_kerja ? 'selected' : '' }}>
                                        {{ $unitKerja->nama_unit_kerja }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif --}}
                {{-- </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        {{-- @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                            <a href="{{ route('petas.create') }}" class="btn btn-md btn-success mb-1">TAMBAH
                                                PETA</a>
                                        @endif --}}
                                        <a href="{{ route('petas.tabel') }}" class="btn btn-md btn-primary mb-1">Tabel
                                            Matrik</a>
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                            {{-- <button type="button" class="btn btn-success mb-1" data-toggle="modal"
                                                data-target="#importModal">
                                                <i class="fas fa-file-excel"></i> Import Excel
                                            </button> --}}
                                            <a href="{{ route('imported-excel.index') }}" class="btn btn-success mb-1">
                                                <i class="fas fa-file-excel"></i> Import Excel
                                            </a>
                                            <a href="{{ route('peta.penelaah') }}"
                                                class="btn btn-md btn-outline-primary mb-1">
                                                Tambah Penelaah
                                            </a>
                                            <a href="{{ route('peta.export-excel') }}" class="btn btn-success mb-1">
                                                <i class="fas fa-file-excel"></i> Export Excel
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            <th scope="col">Unit Kerja</th>
                                            <th scope="col">Kegiatan</th>
                                            <th scope="col">Penelaah</th>
                                            <th scope="col">Tahun</th>
                                            <th scope="col">Detail</th>
                                            <th scope="col">Tabel Matrik</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = ($jenisCount->currentPage() - 1) * $jenisCount->perPage() + 1; @endphp
                                        @forelse ($jenisCount as $item)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $no++ }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->jenis }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->total }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->penelaah }}
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($item->tahun)->format('Y') }}
                                                </td>

                                                <td class="text-center">
                                                    <a href="{{ route('petaRisikoDetail', $item->jenis) }}"
                                                        class="btn btn-success">Lihat Detail</a>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('petas.tabelUnitKerja', ['unitKerja' => $item->jenis]) }}"
                                                        class="btn fa-solid fa-table bg-info p-2 text-white"
                                                        data-toggle="tooltip" title="Lihat Tabel Matrik Unit Kerja"></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <div class="alert alert-danger">
                                                Data Peta Risiko belum Tersedia.
                                            </div>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- PAGINATION -->
                                {{ $jenisCount->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('petas.index') }}" method="GET">
                        <div class="input-group">
                            <select name="year" class="form-control">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Grafik skor pengaruh kegiatan tahun {{ $selectedYear }}</h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Total kegiatan dengan pengaruh tinggi:
                                <strong>{{ $totalHighImpactActivities }}</strong>
                            </p>
                            <div style="height: 500px; overflow-x: auto;">
                                <canvas id="highImpactChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
                {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var ctx = document.getElementById('highImpactChart').getContext('2d');
                        var chartData = @json($chartData);
                        var chart = new Chart(ctx, {
                            type: 'line',
                            data: chartData,
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Skor Pengaruh'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Id Kegiatan'
                                        },
                                        ticks: {
                                            maxRotation: 0,
                                            minRotation: 0
                                        }
                                    }
                                },
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                    title: {
                                        display: true,
                                        text: 'Grafik Pengaruh Kegiatan Tahun {{ $selectedYear }}'
                                    }
                                }
                            }
                        });
                    });
                </script>
            @endpush
        </section>
    </div>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        //message with toastr
        @if (session()->has('success'))

            toastr.success('{{ session('success') }}', 'BERHASIL!');
        @elseif (session()->has('error'))

            toastr.error('{{ session('error') }}', 'GAGAL!');
        @endif
    </script>

@endsection
