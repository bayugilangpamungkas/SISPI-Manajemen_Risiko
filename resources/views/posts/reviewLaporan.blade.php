@extends('layout.app')
@section('title', 'PIC Kegiatan')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>PIC Kegiatan</h1>
            </div>
            <div class="section-body">
                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 3)
                    <h4 class="tittle-1">
                        <span class="span0">List</span>
                        <span class="span1">Tugas Untuk Disetujui</span>
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-0 shadow rounded">
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered mt-2">
                                        <thead>
                                            <tr class="text-center">
                                                <th scope="col">Waktu</th>
                                                <th scope="col">Tempat</th>
                                                <th scope="col">Jenis</th>
                                                <th scope="col">Judul</th>
                                                <th scope="col">Deskripsi</th>
                                                <th scope="col">PIC</th>
                                                {{-- <th scope="col">Anggota</th> --}}
                                                <th colspan="3" scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pendingPosts as $post)
                                                <tr>
                                                    <td class="text-center">{{ $post->waktu }}</td>
                                                    <td class="text-center">{{ $post->tempat }}</td>
                                                    <td class="text-center">
                                                        {{ $jenisKegiatan[$post->jenis]->jenis ?? 'N/A' }}</td>
                                                    <!-- Access jenis by post's id_jenis -->
                                                    <td class="text">{{ $post->judul }}</td>
                                                    <td class="text">{{ $post->deskripsi }}</td>
                                                    <td class="text-center"><span
                                                            class="badge badge-primary">{{ $post->tanggungjawab }}</span>
                                                    </td>
                                                    {{-- <td class="text-center"><span
                                                            class="badge badge-secondary">{{ $post->anggota }}</span></td> --}}
                                                    <td>
                                                        <a href="/detailTugas/{{ $post->id }}"
                                                            class="btn fa-solid fa-list bg-success p-2 text-white"
                                                            data-toggle="tooltip" title="Detail Tugas"></a>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('posts.approve_task', $post->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn fa-solid fa-check bg-primary p-2 text-white"
                                                                data-toggle="tooltip" title="Approve Tugas"></button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('posts.disapprove_task', $post->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn fa-solid fa-times bg-danger p-2 text-white"
                                                                data-toggle="tooltip" title="Disapprove Tugas"></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <div class="alert alert-danger">
                                                    Data Pending Post belum Tersedia.
                                                </div>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    {{ $pendingPosts->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Tugas</span>
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <form action="{{ route('posts.index') }}" method="GET">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control float-right"
                                    placeholder="Search: Masukkan Judul/ Waktu/ PIC ">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                            <a href="{{ route('posts.create') }}" class="btn btn-md btn-success mb-3"
                                                style="font-size: 0.85rem !important;">TAMBAH
                                                TUGAS</a>
                                        @endif
                                        @if (auth()->user()->id_level == 1 ||
                                                auth()->user()->id_level == 2 ||
                                                auth()->user()->id_level == 3 ||
                                                auth()->user()->id_level == 6)
                                            <a href="{{ route('reviewKetua') }}"
                                                class="btn btn-md btn-outline-primary mb-3"
                                                style="font-size: 0.85rem !important;">APPROVE KEGIATAN</a>
                                        @endif
                                        @if (auth()->user()->id_level == 1 ||
                                                auth()->user()->id_level == 2 ||
                                                auth()->user()->id_level == 3 ||
                                                auth()->user()->id_level == 4 ||
                                                auth()->user()->id_level == 6)
                                            <a href="{{ route('laporanAkhir') }}"
                                                class="btn btn-md btn-outline-primary mb-3"
                                                style="font-size: 0.85rem !important;">LAPORAN AKHIR</a>
                                        @endif
                                        <a href="{{ route('dokumenTindakLanjut') }}"
                                            class="btn btn-md btn-outline-primary mb-3"
                                            style="font-size: 0.85rem !important;">DOKUMEN TINDAK LANJUT</a>
                                        {{-- <a href="{{ route('rtm') }}"
                                            class="btn btn-md btn-outline-primary mb-3"
                                            style="font-size: 0.85rem !important;">RTM</a> --}}
                                    </div>
                                    <button id="exportExcelButton" class="btn btn-success mb-3 float-right">
                                        <i class="fas fa-file-excel"></i> Export to Excel
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered mt-2" id="tableKegiatan">
                                        <thead>
                                            <tr class="text-center">
                                                <th scope="col">Waktu</th>
                                                <th scope="col">Tempat</th>
                                                <th scope="col">Jenis</th>
                                                <th scope="col">Judul</th>
                                                <th scope="col">Deskripsi</th>
                                                <th scope="col">PIC</th>
                                                {{-- <th scope="col">Anggota</th> --}}
                                                <th colspan="4" scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($approvedPosts as $post)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $post->waktu }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $post->tempat }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $jenisKegiatan[$post->jenis]->jenis ?? 'N/A' }}
                                                    </td>
                                                    <td class="text">
                                                        {{ $post->judul }}
                                                    </td>
                                                    <td class="text">
                                                        {{ $post->deskripsi }}
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-primary">
                                                            {{ $post->tanggungjawab }}
                                                        </span>
                                                    </td>
                                                    {{-- <td class="text-center">
                                                        <span class="badge badge-secondary">
                                                            {{ $post->anggota }}
                                                        </span>
                                                    </td> --}}
                                                    @if (auth()->user()->id_level == 1 ||
                                                            auth()->user()->id_level == 2 ||
                                                            auth()->user()->id_level == 3 ||
                                                            auth()->user()->id_level == 4 ||
                                                            auth()->user()->id_level == 6)
                                                        <td><a href="/detailTugas/{{ $post->id }}"
                                                                class="btn fa-solid fa-list bg-success p-2 text-white"
                                                                data-toggle="tooltip" title="Detail Tugas"></a> </td>
                                                    @endif
                                                    {{-- @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 3 || auth()->user()->id_level == 6)
                                    <td><a href="/detailTugasKetua/{{ $post->id }}" class="btn fa-solid fa-list bg-primary p-2 text-white" data-toggle="tooltip" title="Detail Tugas Ketua"></a> </td>
                                    @endif --}}
                                                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                        <td><a href="/tampilData/{{ $post->id }}"
                                                                class="btn fa-regular fa-pen-to-square bg-warning p-2 text-white"
                                                                data-toggle="tooltip" title="Edit Tugas"></a> </td>
                                                    @endif
                                                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                        <td class="text-center">
                                                            <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                                action="{{ route('posts.destroy', $post->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <!-- <button type="submit" class="fa-solid fa-trash bg-danger p-2 text white"></button> -->
                                                                <button type="submit"
                                                                    class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                                    data-toggle="tooltip" title="Hapus Tugas"></button>
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <div class="alert alert-danger">
                                                    Data Post belum Tersedia.
                                                </div>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <!-- PAGINATION -->
                                {{ $approvedPosts->links('pagination::bootstrap-4') }}
                                </>
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
                </script>
                <script>
                    function exportTableToExcel(tableId, filename = 'Kegiatan.xlsx') {
                        var wb = XLSX.utils.book_new();
                        var ws = XLSX.utils.table_to_sheet(document.getElementById(tableId));
                        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                        XLSX.writeFile(wb, filename);
                    }

                    document.getElementById('exportExcelButton').addEventListener('click', function() {
                        exportTableToExcel('tableKegiatan');
                    });
                </script>
        </section>
    </div>
@endsection
