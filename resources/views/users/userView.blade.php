@extends('layout.app')
@section('title', 'User')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>User</h1>
        </div>
        <div class="section-body">
            <h4 class="tittle-1">
                <span class="span0">List</span>
                <span class="span1">User</span>
            </h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow rounded">
    
                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-md-6 mb-3">
                                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                        <a href="{{ route('users.create') }}" class="btn btn-md btn-success mb-3">TAMBAH
                                            USER</a>
                                    @endif
                                </div> --}}
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="{{ route('users.create') }}" class="btn btn-success mb-3">TAMBAH USER</a>
                                        </div>
                                        <div class="col-md-6">
                                            <form action="/userView/search" class="form=inline" method="GET">
                                                <div class="input-group mb-2">
                                                    <input type="search" name="search" class="form-control float-right"
                                                        placeholder="Search: Masukkan Nama...">
                                                    <div class="input-group-append">
                                                        <button type="submit" class="btn btn-default">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>     
                                </div>
                            </div>
    
    
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">Nama</th>
                                        <th scope="col">NIP</th>
                                        <th scope="col">Jabatan</th>
                                        <th scope="col">Unit Kerja</th>
                                        <th colspan="4" scope="col">Aksi</th>
    
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
    
                                            <td class="text">
                                                {{ $user->name }}
                                            </td>
                                            <td class="text">
                                                {{ $user->nip }}
                                            </td>
                                            <td class="text">
                                                {{ $user->Level->name }}
                                            </td>
                                            <td class="text">
                                                {{ $user->unitKerja->nama_unit_kerja }}
                                            </td>
                                            @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                <td><a href="{{ route('tampilDataUser', $user->id) }}"
                                                        class="btn fa-regular fa-pen-to-square bg-warning p-2 text-white"
                                                        data-toggle="tooltip" title="Edit User"></a> </td>
                                            @endif
                                            @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                <td class="text-center">
                                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                        action="{{ route('users.destroy', $user->id) }}" method="POST">
    
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                            data-toggle="tooltip" title="Hapus User"></button>
                                                    </form>
                                                </td>
                                            @endif
                                            @if (!$user->is_approved && (auth()->user()->id_level == 1 || auth()->user()->id_level == 2))
                                                <td>
                                                    <form action="{{ route('users.approve', $user->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                    </form>
                                                </td>
                                            @endif
                                            @if (!$user->is_approved && (auth()->user()->id_level == 1 || auth()->user()->id_level == 2))
                                                <td>
                                                    <form action="{{ route('users.disapprove', $user->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger">Disapprove</button>
                                                    </form>
                                                </td>
                                            @endif
    
    
    
                                        </tr>
                                    @empty
                                        <div class="alert alert-danger">
                                            Data Anggota belum Tersedia.
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                            <!-- PAGINATION (Hilangi -- nya)-->
                            {{ $users->links('pagination::bootstrap-4') }}
    
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
