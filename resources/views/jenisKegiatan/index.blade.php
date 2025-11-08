@extends('layout.app')
@section('title', 'Template Dokumen')
@section('main')
{{-- Add Jenis Template Modal --}}
<div class="modal fade" id="addJenisTemplateModal" tabindex="-1" aria-labelledby="addJenisTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jenis-template.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addJenisTemplateModalLabel">Tambah Jenis Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-grup">
                        <label for="jenis">Jenis</label>
                        <input type="text" name="jenis" class="form-control" required placeholder="Masukkan Jenis">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Jenis Template Modal --}}
@foreach($jenisKegiatan as $jenis)
<div class="modal fade" id="editJenisTemplateModal{{ $jenis->id }}" tabindex="-1" aria-labelledby="editJenisTemplateModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jenis-template.update', $jenis->id) }}" method="POST" id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editJenisTemplateModalLabel">Edit Jenis Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-grup">
                        <label for="jenis">Jenis</label>
                        <input type="text" name="jenis" class="form-control" required placeholder="Masukkan Jenis" value="{{ $jenis->jenis}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
            <h1>Jenis Template Dokumen</h1>
        </div>
        <div class="section-body">
            <h4 class="tittle-1">
                <span class="span0">List</span>
                <span class="span1">Jenis Template Dokumen</span>
            </h4>
            <div class="row">
    
                <div class="col-md-5 mb-2 d-flex justify-content-start align-items-center">
                    <form action="jenis-template/search" class="form=inline" method="GET">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control float-right"
                                placeholder="Search: Masukkan Jenis">
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
                                <div class="col-md-6 mb-1">
                                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                        <button class="btn btn-md btn-success mb-3" data-toggle="modal"
                                        data-target="#addJenisTemplateModal">TAMBAH
                                            JENIS KEGIATAN</button>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-1 d-flex justify-content-end">
                                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                        <a href="{{ route('template-dokumen.index') }}" class="btn btn-md btn-outline-primary mb-3">Template Dokumen</a>
                                    @endif
                                </div>
                            </div>
    
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Jenis</th>
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                            <th scope="col">Aksi</th>
                                        @endif
    
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($jenisKegiatan->currentPage() - 1) * $jenisKegiatan->perPage() + 1; @endphp
                                    @forelse ($jenisKegiatan as $dokumen)
                                        <tr>
                                            <td class="text-center">
                                                {{ $no++ }}
                                            </td>
                                            <td class="text-center">
                                                {{ $dokumen->jenis }}
                                            </td>

                                            {{-- <td>
                                                <!-- Menambahkan tombol download -->
                                                <a href="{{ route('download.dokumen', $dokumen->id) }}"
                                                    class="btn btn-success btn-sm" title="Unduh Dokumen">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            </td> --}}
                                            @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)

                                                <td 
                                                class="text-center">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <button data-target="#editJenisTemplateModal{{ $dokumen->id }}"
                                                            class="btn fa-regular fa-pen-to-square bg-warning p-2 text-white"
                                                            data-toggle="modal" title="Edit Jenis"></button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                        action="{{ route('jenis-template.destroy', $dokumen->id) }}" method="POST">
    
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                            data-toggle="tooltip" title="Hapus Jenis"></button>
                                                    </form>
                                                    </div>
                                                </div>
                                                </td>
                                            @endif
    
                                        </tr>
                                    @empty
                                        <div class="alert alert-danger">
                                            Data Template Dokumen belum Tersedia.
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                            <!-- PAGINATION (Hilangi -- nya)-->
                            {{ $jenisKegiatan->links('pagination::bootstrap-4') }}
    
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
