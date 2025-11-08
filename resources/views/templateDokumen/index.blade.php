@extends('layout.app')
@section('title', 'Template Dokumen')
@section('main')

    @php
        $defaultKeterangan = [
            'Template Dokumen Reviu',
            'Template Berita Acara',
            'Template Lembar Pengesahan',
            'Template Kertas Kerja',
            'Template Dokumen Tindak Lanjut',
            'Dokumen Peraturan',
        ];
    @endphp

    @foreach ($jenisDokumen as $jenis)
        @php 
            $documents = $jenis->templateDokumen;
            // Filter keterangan berdasarkan jenis ID
            $filteredKeterangan = [];
            if ($jenis->id == 1) {
                $filteredKeterangan = [$defaultKeterangan[4]]; // Hanya Template Dokumen Tindak Lanjut
            } elseif ($jenis->id == 2) {
                $filteredKeterangan = array_slice($defaultKeterangan, 0, 5); // Semua template kecuali Dokumen Peraturan
            } elseif ($jenis->id == 3) {
                $filteredKeterangan = [$defaultKeterangan[5]]; // Hanya Dokumen Peraturan
            }
            $rowCount = count($filteredKeterangan);
        @endphp

        @for ($index = 0; $index < $rowCount; $index++)
            @php
                $keterangan = $filteredKeterangan[$index];
                
                $acceptTypes = '.doc,.docx';
                $fileTypeInfo = 'Format file yang diperbolehkan: DOC, DOCX, ukuran maksimal 10MB *';

                if ($keterangan == 'Template Kertas Kerja') {
                    $acceptTypes = '.xls,.xlsx';
                    $fileTypeInfo = 'Format file yang diperbolehkan: XLS, XLSX, ukuran maksimal 10MB *';
                }

                if ($documents == null) {
                    $document = null;
                } else {
                    $document = $documents->firstWhere('judul', $keterangan);
                }
            @endphp

            @if ($document)
                <div class="modal fade" id="editTemplateDokumenModal{{ $jenis->id }}{{ $index }}" tabindex="-1"
                    aria-labelledby="editTemplateDokumenModalLabel{{ $jenis->id }}{{ $index }}">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('template-dokumen.update', $document->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title"
                                        id="editTemplateDokumenModalLabel{{ $jenis->id }}{{ $index }}">
                                        Edit File - {{ $keterangan }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="dokumen">Pilih File</label>
                                        <input type="file" class="form-control-file" id="dokumen" name="dokumen"
                                            accept="{{ $acceptTypes }}" required>
                                        <small class="form-text text-muted">{{ $fileTypeInfo }}</small>
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
            @else
                <div class="modal fade" id="uploadModal{{ $jenis->id }}{{ $index }}" tabindex="-1"
                    aria-labelledby="uploadModalLabel{{ $jenis->id }}{{ $index }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('template-dokumen.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="jenis" value="{{ $jenis->id }}">
                                <input type="hidden" name="judul" value="{{ $keterangan }}">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel{{ $jenis->id }}{{ $index }}">
                                        Upload File - {{ $keterangan }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="dokumen">Pilih File</label>
                                        <input type="file" class="form-control-file" id="dokumen" name="dokumen"
                                            accept="{{ $acceptTypes }}" required>
                                        <small class="form-text text-muted">{{ $fileTypeInfo }}</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endfor
    @endforeach

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left"
                        style="font-size: 1.3rem"></i></a>
                <h1>Template Dokumen</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Template Dokumen</span>
                </h4>
                <div class="row">
                    <div class="col-md-5 mb-2 d-flex justify-content-start align-items-center">
                        <form action="template-dokumen/search" class="form=inline" method="GET">
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
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            <th scope="col">Jenis Kegiatan</th>
                                            <th scope="col" colspan="2">Nama Berkas</th>
                                            <th scope="col">Keterangan</th>
                                            <th scope="col">Waktu Pengumpulan</th>
                                            @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                <th scope="col">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($jenisDokumen as $jenis)
                                            @php 
                                                $documents = $jenis->templateDokumen;
                                                // Filter keterangan berdasarkan jenis ID
                                                $filteredKeterangan = [];
                                                if ($jenis->id == 1) {
                                                    $filteredKeterangan = [$defaultKeterangan[4]]; // Hanya Template Dokumen Tindak Lanjut
                                                } elseif ($jenis->id == 2) {
                                                    $filteredKeterangan = array_slice($defaultKeterangan, 0, 5); // Semua template kecuali Dokumen Peraturan
                                                } elseif ($jenis->id == 3) {
                                                    $filteredKeterangan = [$defaultKeterangan[5]]; // Hanya Dokumen Peraturan
                                                }
                                                $rowCount = count($filteredKeterangan);
                                            @endphp
                                            @for ($index = 0; $index < $rowCount; $index++)
                                                @php
                                                    $keterangan = $filteredKeterangan[$index];
                                                    
                                                    if ($documents == null) {
                                                        $document = null;
                                                    } else {
                                                        $document = $documents->firstWhere('judul', $keterangan);
                                                    }
                                                @endphp
                                                <tr>
                                                    @if ($index == 0)
                                                        <td rowspan="{{ $rowCount }}" class="text-center">
                                                            {{ $no++ }}
                                                        </td>
                                                        <td rowspan="{{ $rowCount }}">
                                                            {{ $jenis->jenis }}
                                                        </td>
                                                    @endif

                                                    <td>
                                                        {{ $document ? $document->dokumen : 'Belum diupload' }}
                                                    </td>
                                                    <td>
                                                        @if ($document)
                                                            <a href="{{ asset('template_dokumen/' . $document->dokumen) }}"
                                                                target="_blank" class="btn btn-success btn-sm"
                                                                title="Buka Dokumen">
                                                                <i class="fa-solid fa-download"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $keterangan }}</td>
                                                    <td>
                                                        {{ $document ? $document->updated_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : '-' }}
                                                    </td>

                                                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                        <td>
                                                            @if ($document)
                                                                <button
                                                                    data-target="#editTemplateDokumenModal{{ $jenis->id }}{{ $index }}"
                                                                    class="btn btn-warning" data-toggle="modal"
                                                                    title="Edit Dokumen">Ubah</button>
                                                            @else
                                                                <button type="button" class="btn btn-primary"
                                                                    data-toggle="modal"
                                                                    data-target="#uploadModal{{ $jenis->id }}{{ $index }}">
                                                                    Upload
                                                                </button>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endfor
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if (session()->has('success'))
            toastr.success('{{ session('success') }}', 'BERHASIL!');
        @elseif (session()->has('error'))
            toastr.error('{{ session('error') }}', 'GAGAL!');
        @endif
    </script>

@endsection