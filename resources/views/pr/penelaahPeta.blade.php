@extends('layout.app')
@section('title', 'Penelaah Peta Risiko')
@section('main')

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Penelaah Peta Risiko</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Penelaah Peta</span>
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
                                <a href="{{ route('petas.index') }}" class="btn btn-md btn-primary mb-3">
                                    Peta Risiko
                                </a>
                                <form action="{{ route('peta.update-penelaah') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Unit Kerja</th>
                                                    <th>Penelaah Saat Ini</th>
                                                    <th>Penelaah Baru</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($unitKerjas as $unitKerja)
                                                    <tr>
                                                        <td>{{ $unitKerja->nama_unit_kerja }}</td>
                                                        <td>{{ $unitKerja->penelaah_peta ?: '-' }}</td>
                                                        <td>
                                                            <select
                                                                class="form-control select2 @error('penelaah') is-invalid @enderror"
                                                                name="penelaah[{{ $unitKerja->id }}]">
                                                                <option value="" disabled selected>Pilih Penelaah
                                                                </option>
                                                                @foreach ($users as $user)
                                                                    <option value="{{ $user->name }}">
                                                                        {{ $user->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Update Penelaah</button>
                                    </div>
                                </form>
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
