@extends('layout.app')
@section('title', 'Panel')

@section('main')
<!-- Modal Menu -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/admin/panel" method="post">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label for="menu" style="font-weight: 100;">Menu</label>
                    <input type="text" id="inputmenu" class="form-control" name="name"
                        placeholder="Masukkan Menu" value="{{ old('menu') }}">
                </div>
                <div class="mb-3">
                    <label for="menu" style="font-weight: 100;">Link</label>
                    <select class="custom-select" name="link">
                        <option selected>Pilih Halaman</option>
                        <option value="/dashboard">Dashboard</option>
                        <option value="/posts">PIC</option>
                        <option value="/petas">Peta Risiko</option>
                        <option value="/dokumens">Dokumen Reviu</option>
                        <option value="/dokumen-tindak-lanjut">Dokumen Tindak Lanjut</option>
                        <option value="/users">User</option>
                        <option value="/laporanAkhir">Laporan Akhir</option>
                        <option value="/reviewKetua">PIC Ketua</option>
                        <option value="/feedback">Feedback</option>
                        <option value="/template">Manualbook</option>
                        <option value="/unit-kerja">Unit Kerja</option>
                        <option value="/admin/panel">Menu</option>
                        <!-- Ganti dengan route yang sesuai -->
                        <option value="/profileDataUser/{{ Auth::user()->id }}">Profil</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="menu" style="font-weight: 100;">Icon</label>
                    <select class="custom-select" name="icon" style=" font-family: 'FontAwesome' ">
                        <option value="fa-solid fa-gauge">&#xf624; </option>
                        <option value="fa-solid fa-list-check">&#xf0ae; </option>
                        <option value="fa-regular fa-newspaper">&#xf1ea; </option>
                        <option value="fa-solid fa-user">&#xf007; </option>
                        <option value="fa-regular fa-user">&#xf2bd; </option>
                        <option value="fa-solid fa-file">&#xf15b; </option>
                        <option value="fa-regular fa-file">&#xf15b; </option>
                        <option value="fa-brands fa-facebook">&#xf09a; </option>
                        <option value="fa-solid fa-gear">&#xf013; </option>
                        <option value="fa-solid fa-file-arrow-down">&#xf56d; </option>
                        <option value="fa-regular fa-bookmark">&#xf02e; </option>
                        <option value="fa-solid fa-thumbs-up">&#xf164; </option>



                    </select>

                    <label for="menu" style="font-weight: 100;">Visibility</label>
                </div>
                <div class="mb-3">
                    @foreach ($levels as $level)
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input"
                                id="customCheck{{ $level->id }}" name="level{{ $level->id }}"
                                value="{{ $level->id }}">
                            <label class="custom-control-label" style="font-weight: 100;"
                                for="customCheck{{ $level->id }}">{{ $level->name }}</label>
                        </div>
                    @endforeach

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>

    </div>
</div>
</div>
<!-- Modal Head Menu -->
<div class="modal fade" id="HeadModal" tabindex="-1" role="dialog" aria-labelledby="HeadModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/admin/panel/head-menu" method="post">
            @csrf
            <div class="modal-body" id="modal-body">
                <div class="mb-3">
                    <label for="name" style="font-weight: 100;">Head Menu</label>
                    <input type="text" id="inputname" class="form-control" name="name"
                        placeholder="Masukkan Head Menu" value="{{ old('menu') }}">
                </div>
                <div class="mb-3">
                    <label for="menu" style="font-weight: 100;">Icon</label>
                    <select class="custom-select" name="icon" style=" font-family: 'FontAwesome' ">
                        <option value="fa-solid fa-gauge">&#xf624; </option>
                        <option value="fa-solid fa-list-check">&#xf0ae; </option>
                        <option value="fa-regular fa-newspaper">&#xf1ea; </option>
                        <option value="fa-solid fa-user">&#xf007; </option>
                        <option value="fa-regular fa-user">&#xf2bd; </option>
                        <option value="fa-solid fa-file">&#xf15b; </option>
                        <option value="fa-regular fa-file">&#xf15b; </option>
                        <option value="fa-brands fa-facebook">&#xf09a; </option>
                        <option value="fa-solid fa-gear">&#xf013; </option>
                        <option value="fa-solid fa-file-arrow-down">&#xf56d; </option>
                        <option value="fa-regular fa-bookmark">&#xf02e; </option>
                        <option value="fa-solid fa-thumbs-up">&#xf164; </option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="menu" style="font-weight: 100;">Menu</label>
                    <div class="row mb-3">
                        <div class="col-sm-10">
                            <select class="custom-select" name="id_menu[]">
                                <option selected value="">Pilih Menu</option>
                                @foreach ($menus_head as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" onclick="addInput()">
                                +
                            </button>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>

    </div>
</div>
</div>

@foreach($menus as $menu)
<!-- Modal -->
<div class="modal fade" id="editModal{{ $menu->id }}" tabindex="-1"
    role="dialog" aria-labelledby="editModalLabel{{ $menu->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $menu->id }}">
                    Edit Menu {{ $menu->name }}</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/panel/{{ $menu->id }}" method="post">
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="menu"
                            style="font-weight: 100;">Menu</label>
                        <input type="text" id="inputmenu" class="form-control"
                            name="name" placeholder="Masukkan Menu"
                            value="{{ $menu->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="menu"
                            style="font-weight: 100;">Link</label>
                        <select class="custom-select" name="link">
                            <option>Pilih Halaman</option>
                            <option value="/dashboard"
                                {{ $menu->link == '/dashboard' ? 'selected' : '' }}>
                                Dashboard</option>
                            <option value="/posts"
                                {{ $menu->link == '/posts' ? 'selected' : '' }}>
                                PIC</option>
                            <option value="/petas"
                                {{ $menu->link == '/petas' ? 'selected' : '' }}>
                                Peta Risiko</option>
                            <option value="/dokumens"
                                {{ $menu->link == '/dokumens' ? 'selected' : '' }}>
                                Dokumen Reviu</option>
                            <option value="/dokumen-tindak-lanjut"
                                {{ $menu->link == '/dokumen-tindak-lanjut' ? 'selected' : '' }}>
                                Dokumen Tindak Lanjut</option>
                            <option value="/users"
                                {{ $menu->link == '/users' ? 'selected' : '' }}>
                                User</option>
                            <option value="/laporanAkhir"
                                {{ $menu->link == '/laporanAkhir' ? 'selected' : '' }}>
                                Laporan Akhir</option>
                            <option value="/reviewKetua"
                                {{ $menu->link == '/reviewKetua' ? 'selected' : '' }}>
                                PIC Ketua</option>
                            <option
                                value="/profileDataUser/{{ Auth::user()->id }}"
                                {{ $menu->link == '/profileDataUser' ? 'selected' : '' }}>
                                Profil</option>
                            <option value="/feedback"
                                {{ $menu->link == '/feedback' ? 'selected' : '' }}>
                                Feedback</option>
                            <option value="/admin/panel"
                                {{ $menu->link == '/admin/panel' ? 'selected' : '' }}>
                                Menu</option>
                            <option value="/template"
                                {{ $menu->link == '/template' ? 'selected' : '' }}>
                                Manualbook</option>
                            <option value="/unit-kerja"
                                {{ $menu->link == '/unit-kerja' ? 'selected' : '' }}>
                                Unit Kerja</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="menu"
                            style="font-weight: 100;">Icon</label>
                        <select class="custom-select" name="icon"
                            style=" font-family: 'FontAwesome' ">
                            <option value="fa-solid fa-gauge"
                                {{ $menu->icon == 'fa-solid fa-gauge' ? 'selected' : '' }}>
                                &#xf624; </option>
                            <option value="fa-solid fa-list-check"
                                {{ $menu->icon == 'fa-solid fa-list-check' ? 'selected' : '' }}>
                                &#xf0ae; </option>
                            <option value="fa-regular fa-newspaper"
                                {{ $menu->icon == 'fa-regular fa-newspaper' ? 'selected' : '' }}>
                                &#xf1ea; </option>
                            <option value="fa-solid fa-user"
                                {{ $menu->icon == 'fa-solid fa-user' ? 'selected' : '' }}>
                                &#xf007; </option>
                            <option value="fa-regular fa-user"
                                {{ $menu->icon == 'fa-reguler fa-user' ? 'selected' : '' }}>
                                &#xf2bd; </option>
                            <option value="fa-solid fa-file"
                                {{ $menu->icon == 'fa-solid fa-file' ? 'selected' : '' }}>

                                &#xf15b; </option>
                            <option value="fa-regular fa-file"
                                {{ $menu->icon == 'fa-solid fa-file' ? 'selected' : '' }}>

                                &#xf15b; </option>
                            <option value="fa-solid fa-gear"
                                {{ $menu->icon == 'fa-solid fa-gear' ? 'selected' : '' }}>

                                &#xf013; </option>
                            <option value="fa-solid fa-thumbs-up"
                                {{ $menu->icon == 'fa-solid fa-thumbs-up' ? 'selected' : '' }}>

                                &#xf164; </option>
                            <option value="fa-solid fa-file-arrow-down"
                                {{ $menu->icon == 'fa-solid fa-file-arrow-down' ? 'selected' : '' }}>

                                &#xf56d; </option>
                            <option value="fa-regular fa-bookmark"
                                {{ $menu->icon == 'fa-regular fa-bookmark' ? 'selected' : '' }}>

                                &#xf02e; </option>

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save
                        changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@foreach($head_menus as $menu)
<!--edit Modal -->
<div class="modal fade" id="editModalHead{{ $menu->id }}" tabindex="-1"
    role="dialog" aria-labelledby="editModalLabel{{ $menu->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $menu->id }}">
                    Edit Head Menu {{ $menu->name }}</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/panel/{{ $menu->id }}/head-menu" method="post">
                @method('put')
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="menu"
                            style="font-weight: 100;">Menu</label>
                        <input type="text" id="inputmenu" class="form-control"
                            name="name" placeholder="Masukkan Menu"
                            value="{{ $menu->name }}">
                    </div>

                    <div class="mb-3">
                        <label for="menu"
                            style="font-weight: 100;">Icon</label>
                        <select class="custom-select" name="icon"
                            style=" font-family: 'FontAwesome' ">
                            <option value="fa-solid fa-gauge"
                                {{ $menu->icon == 'fa-solid fa-gauge' ? 'selected' : '' }}>
                                &#xf624; </option>
                            <option value="fa-solid fa-list-check"
                                {{ $menu->icon == 'fa-solid fa-list-check' ? 'selected' : '' }}>
                                &#xf0ae; </option>
                            <option value="fa-regular fa-newspaper"
                                {{ $menu->icon == 'fa-regular fa-newspaper' ? 'selected' : '' }}>
                                &#xf1ea; </option>
                            <option value="fa-solid fa-user"
                                {{ $menu->icon == 'fa-solid fa-user' ? 'selected' : '' }}>
                                &#xf007; </option>
                            <option value="fa-regular fa-user"
                                {{ $menu->icon == 'fa-reguler fa-user' ? 'selected' : '' }}>
                                &#xf2bd; </option>
                            <option value="fa-solid fa-file"
                                {{ $menu->icon == 'fa-solid fa-file' ? 'selected' : '' }}>

                                &#xf15b; </option>
                            <option value="fa-regular fa-file"
                                {{ $menu->icon == 'fa-solid fa-file' ? 'selected' : '' }}>

                                &#xf15b; </option>
                            <option value="fa-solid fa-gear"
                                {{ $menu->icon == 'fa-solid fa-gear' ? 'selected' : '' }}>

                                &#xf013; </option>
                            <option value="fa-solid fa-thumbs-up"
                                {{ $menu->icon == 'fa-solid fa-thumbs-up' ? 'selected' : '' }}>

                                &#xf164; </option>
                            <option value="fa-solid fa-file-arrow-down"
                                {{ $menu->icon == 'fa-solid fa-file-arrow-down' ? 'selected' : '' }}>

                                &#xf56d; </option>
                            <option value="fa-regular fa-bookmark"
                                {{ $menu->icon == 'fa-regular fa-bookmark' ? 'selected' : '' }}>

                                &#xf02e; </option>

                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save
                        changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- add Modal -->
<div class="modal fade" id="addModalHead{{ $menu->id }}" tabindex="-1"
    role="dialog" aria-labelledby="addModalHeadLabel{{ $menu->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalHeadLabel{{ $menu->id }}">
                    Tambah Menu {{ $menu->name }}</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/panel/{{ $menu->id }}/menu" method="post">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <select class="custom-select" name="id_menu">
                            <option selected value="">Pilih Menu</option>
                            @foreach ($menus_head as $head_menu)
                                <option value="{{ $head_menu->id }}">{{ $head_menu->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save
                        changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Admin Panel - Konfigurasi Menu</h1>
        </div>
        <div class="section-body">
            <div class="row">
                
                <div class="col-md-12">
                    <div class="row pl-3">
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                                <li class="nav-item">
                                    <a class="btn btn-md btn-light rounded-top" href="#tab-menu" data-toggle="tab">MENU</a>
                                </li>
                                <li class="nav-item">
                                    <a class="btn btn-md btn-light rounded-top" href="#tab-head-menu"
                                        data-toggle="tab">HEAD</a>
                                </li>
                            </ul>
                        </div>
    
                    </div>
                    <div class="card border-0 shadow rounded tab-content">
                        <div class="card-body tab-pane active m-3" id="tab-menu">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-md btn-success mb-3" data-toggle="modal"
                                        data-target="#exampleModal">
                                        TAMBAH MENU
                                    </button>
                                    {{-- <button type="button" class="btn btn-md btn-success mb-3" data-toggle="modal"
                                        data-target="#HeadModal">
                                        TAMBAH HEAD MENU
                                    </button> --}}
                                    {{-- <a href="/" class="btn btn-md btn-success mb-3">TAMBAH Menu</a> --}}
                                </div>
                                <div class="col-md-6">
                                    <form action="/" class="form=inline" method="GET">
                                        <div class="input-group mb-5">
                                            <input type="search" name="search" class="form-control float-right"
                                                placeholder="Search: Masukkan Kata Kunci...">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
    
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Menu</th>
                                        <th scope="col">Link</th>
                                        @foreach ($levels as $level)
                                            <th scope="col">{{ $level->name }}</th>
                                        @endforeach
                                        <th scope="col">Action</th>
    
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($menus as $menu)
                                        <tr>
    
                                            <td class="text">
                                                {{ $menu->name }}
                                            </td>
                                            <td class="text">
                                                <a href="{{ $menu->link }}" class="btn btn-sm btn-primary"> <i
                                                        class="fa-solid fa-link"></i> link</a>
                                            </td>
                                            @foreach ($levels as $level)
                                                <td class="text">
                                                    <div class="form-check">
                                                        <label>
                                                            <form action="/admin/panel/{{ $menu->id }}" method="post">
                                                                @method('put')
                                                                @csrf
                                                                <input type="hidden" id="inputmenu" class="form-control"
                                                                    name="level" value="{{ $level->id }}">
                                                                @php
                                                                    $checkMenu = $List_menus
                                                                        ->where('id_level', $level->id)
                                                                        ->where('id_menu', $menu->id)
                                                                        ->first();
                                                                @endphp
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-{{ isset($checkMenu) ? 'success' : 'secondary' }}"
                                                                    style="margin:0;"
                                                                    onclick="return confirm('Apakah anda yakin?')">
                                                                    <i
                                                                        class="fa-solid fa-{{ isset($checkMenu) ? 'check' : 'square' }}"></i>
                                                                </button>
                                                            </form>
                                                        </label>
                                                    </div>
                                                </td>
                                            @endforeach
    
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                    data-target="#editModal{{ $menu->id }}">Edit</button>
                                                <form action="/admin/panel/{{ $menu->id }}" method="post"
                                                    class="d-inline">
                                                    @method('delete')
                                                    @csrf
    
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Apakah anda yakin?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <div class="alert alert-danger">
                                            Data Menu belum Tersedia.
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
    
                        </div>
                        <div class="card-body tab-pane m-3" id="tab-head-menu">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-md btn-success mb-3" data-toggle="modal"
                                        data-target="#HeadModal">
                                        TAMBAH HEAD MENU
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <form action="/" class="form=inline" method="GET">
                                        <div class="input-group mb-5">
                                            <input type="search" name="search" class="form-control float-right"
                                                placeholder="Search: Masukkan Kata Kunci...">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
    
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Head Menu</th>
                                        <th scope="col">Menu</th>
                                        <th scope="col">Action</th>
    
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($head_menus as $menu)
                                        <tr>
    
                                            <td class="text">
                                                {{ $menu->name }}
                                            </td>
                                            <td></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                    data-target="#addModalHead{{ $menu->id }}">Add</button>
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                                    data-target="#editModalHead{{ $menu->id }}">Edit</button>
                                                <form action="/admin/panel/{{ $menu->id }}/head-menu" method="post"
                                                    class="d-inline">
                                                    @method('delete')
                                                    @csrf
    
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Apakah anda yakin?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @foreach ($menu->Menu as $m)
                                            <tr>
                                                <td></td>
                                                <td class="text">
                                                    {{ $m->name }}
                                                </td>
                                                <td>
                                                    <form action="/admin/panel/{{ $m->id }}/menu" method="post"
                                                        class="d-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Apakah anda yakin?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <div class="alert alert-danger">
                                            Data Menu belum Tersedia.
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
    
            var index = 1;
    
            function addInput() {
                index++;
                var row = `
    
                <div class="row row` + index + ` mb-3">
                    <div class="col-sm-10">
                        <select class="custom-select" name="id_menu[]">
                            <option selected value="">Pilih Menu</option>
                            @foreach ($menus_head as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger" id="` + index + `" onclick="removeInput(this.id)">
                            -
                        </button>
                    </div>
                </div>
                `;
    
                $('#modal-body').append(function() {
                    return row;
                });
                // document.getElementById('modal-body').append(row);
            }
    
            function removeInput(id) {
                $(".row" + id).remove();
                console.log("#row" + id)
            }
        </script>
    </section>
</div>
    

@endsection
