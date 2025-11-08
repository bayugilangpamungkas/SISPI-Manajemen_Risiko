@extends('layout.app')
@section('title', 'Penelaah Peta Risiko')
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
                <h1>Import Data Peta</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Import Excel</span>
                </h4>
                <!-- Rekapitulasi -->
                {{-- <div class="row mb-1">
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
                </div> --}}
                {{-- search bar --}}
                {{-- <div class="row">
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
                </div> --}}


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
                                <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                                    data-target="#importModal">
                                    <i class="fas fa-file-excel"></i> Import Excel
                                </button>
                                <a href="{{ route('petas.index') }}" class="btn btn-md btn-primary mb-3">
                                    Peta Risiko
                                </a>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama File</th>
                                                <th>Jumlah Data</th>
                                                <th>Tanggal Import</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php $no = ($imported->currentPage() - 1) * $imported->perPage() + 1; @endphp
                                            @forelse ($imported as $item)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $item->nama_file }}</td>
                                                    <td>{{ $item->jumlah_data ?: '-' }}</td>
                                                    <td>{{ $item->created_at ?: '-' }}</td>
                                                </tr>
                                            @empty
                                                <div class="alert alert-danger">
                                                    Data Import Excel belum Tersedia.
                                                </div>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <!-- PAGINATION -->
                                {{ $imported->links('pagination::bootstrap-4') }}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
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
