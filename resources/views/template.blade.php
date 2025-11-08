@extends('layout.main')
@section('title', 'Template')
@section('isi')

    <div class="col-md-16 p-5 pt-2">
        <h3><i class="fa-solid fa-file-arrow-down mr-2"></i>MANUAL BOOK</h3>
        <hr>
        <h4 class="tittle-1">
            <span class="span0">List</span>
            <span class="span1">Dokumen</span>
        </h4>
        <div class="row">

            {{-- <div class="col-md-5 mb-2">
                <form action="/dokumen/search" class="form=inline" method="GET">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control float-right"
                            placeholder="Search: Masukkan Judul">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div> --}}

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow rounded">

                    <div class="card-body">
                        {{-- <div class="row">
                            <div class="col-md-6 mb-1">
                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 5)
                                    <a href="{{ route('dokumens.create') }}" class="btn btn-md btn-success mb-3">TAMBAH
                                        DOKUMEN</a>
                                @endif
                            </div>
                        </div> --}}

                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">No</th>
                                    <th colspan="1" scope="col">Dokumen</th>
                                    <th colspan="1" scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($files as $file)
                                    <tr>
                                        <td class="text-center">
                                            {{ $no++ }}
                                        </td>
                                        <td class="text">
                                            {{ $file['name'] }}
                                        </td>
                                        <td>
                                            <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                            <a href="{{ asset($file['path']) }}" target="_blank" class="btn btn-info btn-sm" title="Buka Dokumen">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
