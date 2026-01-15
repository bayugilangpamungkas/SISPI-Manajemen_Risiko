@extends('layout.app')
@section('title', 'Detail Peta')
@section('main')
    <!-- Modal Popup -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Tambah Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('petas.uploadDokumen', $jenis) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jenis" value="{{ $jenis }}">
                        <div class="form-group">
                            <label for="dokumen">Pilih Dokumen:</label>
                            <input type="file" name="dokumen" class="form-control" id="dokumen" required
                                accept=".xls,.xlsx">
                        </div>
                        <small class="form-text text-danger ml-1 mb-2" style="font-style: italic;">
                            *dokumen harus berformat excel
                        </small>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Anggota --}}
    <div class="modal fade" id="anggotaModal" tabindex="-1" role="dialog" aria-labelledby="anggotaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="anggotaModalLabel">Tambah Penelaah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('storeAnggota', $jenis) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="jenis" value="{{ $jenis }}">
                        <select name="anggota" class="form-control select2">
                            <option value="" selected disabled>- Pilih Penelaah -</option>
                            @foreach ($penelaah as $p)
                                <option value="{{ $p->name }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="/petas" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Rincian Peta Risiko</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">Rincian</span>
                    <span class="span1">{{ $jenis }}</span>
                </h4>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form action="{{ route('peta.export-excel-jenis') }}">
                                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                                    <button type="submit" class="btn btn-success mb-1">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </form>


                                <div class="row">
                                    {{-- <div class="col-6">
                                        @if ($firstPeta)
                                            <th class="col-2">Waktu : </th>
                                            <td>
                                                <i class="fa-regular fa-calendar-days mr-1" style="color: #0050db;"></i>
                                                {{ $firstPeta->waktu ?? 'Belum ada waktu' }}
                                            </td>
                                            <br>
                                            <th class="col-2">Penelaah : </th>
                                            <td>{{ $firstPeta->anggota ?? 'Belum ada penelaah' }}</td>
                                        @endif
                                    </div> --}}
                                    {{-- <div class="col-6">
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2 || auth()->user()->id_level == 5)
                                            @if (!$data->first()->dokumen)
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                                                    Tambah Dokumen
                                                </button>
                                            @endif
                                        @endif
                                        @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2)
                                            @if (!$firstPeta->ketuaPenelaah && !$firstPeta->anggota)
                                                <a href="{{ route('petas.tugas', ['jenis' => $firstPeta->jenis]) }}"
                                                    class="btn btn-primary">Tambah Penelaah</a>
                                                </br>
                                                </br>
                                            @endif
                                        @endif
                                        @if (Auth::user()->id_level == 3)
                                            @if ($firstPeta->ketuaPenelaah && !$firstPeta->anggota)
                                                <button data-toggle="modal" data-target="#anggotaModal"
                                                    class="btn btn-primary">Tambah Penelaah</button>
                                                </br>
                                                </br>
                                            @endif
                                        @endif
                                        <th class="col-2">Ketua Penelaah : </th>
                                        @if (!$firstPeta->ketuaPenelaah && !$firstPeta->anggota)
                                            <td>{{ $firstPeta->ketuaPenelaah->user->name ?? 'Belum ada ketua penelaah' }}
                                            </td>
                                        @elseif (!$firstPeta->ketuaPenelaah && $firstPeta->anggota)
                                            <td>Penelaah dipilih langsung oleh admin</td>
                                        @elseif ($firstPeta->ketuaPenelaah)
                                            <td>{{ $firstPeta->ketuaPenelaah->user->name ?? 'Belum ada ketua penelaah' }}
                                        @endif
                                    </div> --}}
                                    <div class="col-5 mt-3">
                                        <form action="{{ route('petaRisikoDetail', $jenis) }}" method="GET">
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
                                    {{-- <div class="mt-3 col-7"> --}}

                                    {{-- </div> --}}
                                </div>

                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            {{-- <th scope="col">PIC</th> --}}
                                            <th scope="col">Judul</th>
                                            <th scope="col">Waktu Telaah Substansi</th>
                                            <th scope="col">Waktu Telaah Teknis</th>
                                            <th scope="col">Waktu Telaah SPI</th>
                                            <th scope="col">Terakhir Diupdate</th>
                                            {{-- <th scope="col">IKU</th>
                                            <th scope="col">Kode</th>
                                            <th scope="col">Tahun</th> --}}
                                            <th colspan="2" scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp
                                        @forelse ($data as $item)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $no++ }}
                                                </td>
                                                {{-- <td class="text-center">
                                                    {{ $item->nama }}
                                                </td> --}}
                                                <td>
                                                    {{ $item->judul }}
                                                </td>
                                                <td>
                                                    {{ $item->waktu_telaah_subtansi ? \Carbon\Carbon::parse($item->waktu_telaah_subtansi)->locale('en')->isoFormat('D MMMM Y') : '' }}
                                                </td>
                                                <td>
                                                    {{ $item->waktu_telaah_teknis ? \Carbon\Carbon::parse($item->waktu_telaah_teknis)->locale('en')->isoFormat('D MMMM Y') : '' }}
                                                </td>
                                                <td>
                                                    {{ $item->waktu_telaah_spi ? \Carbon\Carbon::parse($item->waktu_telaah_spi)->locale('en')->isoFormat('D MMMM Y') : '' }}
                                                </td>
                                                <td>
                                                    {{ $item->updated_at->format('d F Y H:i:s') }}
                                                </td>
                                                {{-- <td class="text-center">
                                                    {{ $item->kegiatan->iku }}
                                                </td> --}}
                                                {{-- <td class="text-center">
                                                    <span class="badge bg-black">
                                                        {{ $item->kode_regist }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $item->created_at->format('Y') }}
                                                </td> --}}
                                                <td class="text-center">
                                                    <a href="{{ route('petas.detailPR', ['id' => $item->id]) }}"
                                                        class="btn fa-solid fa-list bg-success p-2 text-white"
                                                        data-toggle="tooltip" title="Detail Dokumen"></a>
                                                </td>
                                                {{-- @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2 || auth()->user()->id_level == 5)
                                                    <td class="text-center">
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('petas.destroy', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <!-- <button type="submit" class="fa-solid fa-trash bg-danger p-2 text white"></button> -->
                                                            <button type="submit"
                                                                class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                                data-toggle="tooltip" title="Hapus Dokumen"></button>
                                                        </form>
                                                    </td>
                                                @endif --}}
                                            </tr>
                                        @empty
                                            <div class="alert alert-danger">
                                                Data Peta Risiko belum Tersedia.
                                            </div>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- PAGINATION -->
                                {{ $data->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">


                                <div class="row">
                                    {{-- <div class="col-6">
                                        @if ($firstPeta)
                                            <th class="col-2">Waktu : </th>
                                            <td>
                                                <i class="fa-regular fa-calendar-days mr-1" style="color: #0050db;"></i>
                                                {{ $firstPeta->waktu ?? 'Belum ada waktu' }}
                                            </td>
                                            <br>
                                            <th class="col-2">Penelaah : </th>
                                            <td>{{ $firstPeta->anggota ?? 'Belum ada penelaah' }}</td>
                                        @endif
                                    </div> --}}
                                    {{-- <div class="col-6">
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2 || auth()->user()->id_level == 5)
                                            @if (!$data->first()->dokumen)
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                                                    Tambah Dokumen
                                                </button>
                                            @endif
                                        @endif
                                        @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2)
                                            @if (!$firstPeta->ketuaPenelaah && !$firstPeta->anggota)
                                                <a href="{{ route('petas.tugas', ['jenis' => $firstPeta->jenis]) }}"
                                                    class="btn btn-primary">Tambah Penelaah</a>
                                                </br>
                                                </br>
                                            @endif
                                        @endif
                                        @if (Auth::user()->id_level == 3)
                                            @if ($firstPeta->ketuaPenelaah && !$firstPeta->anggota)
                                                <button data-toggle="modal" data-target="#anggotaModal"
                                                    class="btn btn-primary">Tambah Penelaah</button>
                                                </br>
                                                </br>
                                            @endif
                                        @endif
                                        <th class="col-2">Ketua Penelaah : </th>
                                        @if (!$firstPeta->ketuaPenelaah && !$firstPeta->anggota)
                                            <td>{{ $firstPeta->ketuaPenelaah->user->name ?? 'Belum ada ketua penelaah' }}
                                            </td>
                                        @elseif (!$firstPeta->ketuaPenelaah && $firstPeta->anggota)
                                            <td>Penelaah dipilih langsung oleh admin</td>
                                        @elseif ($firstPeta->ketuaPenelaah)
                                            <td>{{ $firstPeta->ketuaPenelaah->user->name ?? 'Belum ada ketua penelaah' }}
                                        @endif
                                    </div> --}}
                                    <div class="col-5 mt-3">
                                        <h3>Kegiatan yang telat ditelaah</h3>
                                    </div>
                                </div>

                                <!-- Table for displaying activities by category -->
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">Substansi</th>
                                            <th scope="col">Teknis</th>
                                            <th scope="col">SPI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $maxRows = max(
                                                count($telatsubstansi ?? []),
                                                count($telatteknis ?? []),
                                                count($telatspi ?? []),
                                            );
                                        @endphp

                                        @for ($i = 0; $i < $maxRows; $i++)
                                            <tr>
                                                <td>
                                                    @if (isset($telatsubstansi[$i]))
                                                        {{ $telatsubstansi[$i]->judul }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($telatteknis[$i]))
                                                        {{ $telatteknis[$i]->judul }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($telatspi[$i]))
                                                        {{ $telatspi[$i]->judul }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor

                                        @if ($maxRows == 0)
                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    <div class="alert alert-danger mb-0">
                                                        Tidak ada kegiatan yang telat ditelaah.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <!-- PAGINATION -->
                                {{ $data->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Bagian Dokumen dan komentar --}}
                {{-- <div class="row">
                    <div class="col-md-12 mt-2">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <table class="table table-white table-sm table-responsive">
                                    @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2 || Auth::user()->id_level == 3 || Auth::user()->id_level == 4 || Auth::user()->id_level == 6)

                                        @if ($firstPeta)
                                            @php
                                                $documents = [
                                                    [
                                                        'name' => $firstPeta->dokumen,
                                                        'path' => 'dokumenPR/',
                                                        'label' => 'Dokumen Peta Risiko',
                                                        'approval' => $firstPeta->approvalPr,
                                                        'uploaded_at' => $firstPeta->dokumen_at,
                                                        'approval_at' => $firstPeta->approvalPr_at,
                                                    ],
                                                ];

                                                $filteredDocuments = array_filter($documents, function ($document) {
                                                    return !is_null($document['name']);
                                                });
                                            @endphp

                                            @if (count($filteredDocuments) > 0)
                                                <tr>
                                                    <th class="col-2">Dokumen : </th>
                                                    <td>
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th scope="col">No</th>
                                                                    <th colspan="2">Nama Berkas</th>
                                                                    <th scope="col">Keterangan</th>
                                                                    <th scope="col">Waktu Pengumpulan</th>
                                                                    <th colspan="3" scope="col">Approving</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $no = 1; @endphp
                                                                @foreach ($filteredDocuments as $document)
                                                                    <tr>
                                                                        <td class="text-center">{{ $no++ }}</td>
                                                                        <td>{{ $document['name'] }}</td>
                                                                        <td>
                                                                            <a href="{{ asset($document['path'] . '/' . $document['name']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-sm"
                                                                                title="Buka Dokumen">
                                                                                <i class="fa-solid fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td>{{ $document['label'] }}</td>
                                                                        <td class="text-center">
                                                                            {{ \Carbon\Carbon::parse($document['uploaded_at'])->format('d F Y') }}
                                                                        </td>
                                                                        <!-- Kolom untuk tombol Approve -->
                                                                        <td>
                                                                            @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2 || Auth::user()->id_level == 3 || Auth::user()->id_level == 4 || Auth::user()->id_level == 6)
                                                                                @if ($document['approval'] == 'approved')
                                                                                    <b>Approved</b>
                                                                                    <p>{{ \Carbon\Carbon::parse($document['approval_at'])->format('d F Y') }}
                                                                                    </p>
                                                                                @elseif($document['approval'] == 'rejected')
                                                                                @else
                                                                                    @if (Auth::user()->name == $firstPeta->anggota && !$firstPeta->ketuaPenelaah)
                                                                                        <!-- Tampilkan tombol approve jika penelaah dipilih oleh admin -->
                                                                                        <form
                                                                                            action="{{ route('petas.approve', ['id' => $firstPeta->id]) }}"
                                                                                            method="POST"
                                                                                            style="display:inline;">
                                                                                            @csrf
                                                                                            <button type="submit"
                                                                                                class="btn btn-success">Approve</button>
                                                                                        </form>
                                                                                    @endif
                                                                                    <!-- Kondisi penelaah yang dipilih oleh ketua -->
                                                                                    @if ($firstPeta->ketuaPenelaah && $firstPeta->ketuaPenelaah->id_ketua == Auth::user()->id)
                                                                                        <!-- Tampilkan tombol approve jika user adalah ketua penelaah -->
                                                                                        <form
                                                                                            action="{{ route('petas.approve', ['id' => $firstPeta->id]) }}"
                                                                                            method="POST"
                                                                                            style="display:inline;">
                                                                                            @csrf
                                                                                            <button type="submit"
                                                                                                class="btn btn-success">Approve</button>
                                                                                        </form>
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        </td>

                                                                        <!-- Kolom untuk tombol Reject -->
                                                                        <td>
                                                                            @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2 || Auth::user()->id_level == 3 || Auth::user()->id_level == 4 || Auth::user()->id_level == 6)
                                                                                @if ($document['approval'] == 'rejected')
                                                                                    <b>Rejected</b>
                                                                                    <p>{{ \Carbon\Carbon::parse($document['approval_at'])->format('d F Y') }}
                                                                                    </p>
                                                                                @elseif($document['approval'] == 'approved')
                                                                                @else
                                                                                    @if (Auth::user()->name == $firstPeta->anggota && !$firstPeta->ketuaPenelaah)
                                                                                        <!-- Tampilkan tombol reject jika penelaah dipilih oleh admin -->
                                                                                        <form
                                                                                            action="{{ route('petas.disapprove', ['id' => $firstPeta->id]) }}"
                                                                                            method="POST"
                                                                                            style="display:inline;">
                                                                                            @csrf
                                                                                            <button type="submit"
                                                                                                class="btn btn-danger">Reject</button>
                                                                                        </form>
                                                                                        <!-- Kondisi penelaah yang dipilih oleh ketua -->
                                                                                    @elseif                                                                                        ($firstPeta->ketuaPenelaah && $firstPeta->ketuaPenelaah->id_ketua == Auth::user()->id)
                                                                                        <!-- Tampilkan tombol reject jika user adalah ketua penelaah -->
                                                                                        <form
                                                                                            action="{{ route('petas.disapprove', ['id' => $firstPeta->id]) }}"
                                                                                            method="POST"
                                                                                            style="display:inline;">
                                                                                            @csrf
                                                                                            <button type="submit"
                                                                                                class="btn btn-danger">Reject</button>
                                                                                        </form>
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        </td>


                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td colspan="2">
                                                    <div class="alert alert-danger">
                                                        Dokumen belum disubmit oleh Auditee.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    {{-- Dokumen Auditee hanya untuk id_level 5 --}}
                {{-- @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 5)
                                        @if (!empty($firstPeta->dokumen))
                                            <tr>
                                                <th class="col-2">Upload Ulang : </th>
                                                <td>
                                                    @if (empty($firstPeta->approvalPr))
                                                        Upload dokumen harus berformat excel (.xls / .xlsx)
                                                        <form action="{{ route('updateDataByJenis', $firstPeta->jenis) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="file_type" value="hasilRubrik">
                                                            <div class="input-group mb-3">
                                                                <input type="file" name="dokumen"
                                                                    class="form-control m-2" id="inputGroupFile"
                                                                    accept=".xls, .xlsx">
                                                                <button type="submit"
                                                                    class="m-2 btn btn-md btn-primary">Upload</button>
                                                            </div>
                                                        </form>
                                                    @else
                                                        <span>Dokumen belum diapprove ataupun ditolak, tidak bisa upload
                                                            ulang.</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th class="col-2">Dokumen PIC : </th>
                                            <td>
                                                @if ($firstPeta)
                                                    @php
                                                        $documents = [
                                                            [
                                                                'name' => $firstPeta->dokumen,
                                                                'path' => 'dokumenPR/',
                                                                'label' => 'Dokumen Peta Risiko',
                                                                'uploaded_at' => $firstPeta->dokumen_at,
                                                            ],
                                                        ];

                                                        $filteredDocuments = array_filter($documents, function (
                                                            $document,
                                                        ) {
                                                            return !is_null($document['name']);
                                                        });
                                                    @endphp
                                                    @php
                                                        $documentHistories = $firstPeta->documentHistories;
                                                    @endphp

                                                    @if ($documentHistories->count() > 0)
                                                        <h5>Riwayat Dokumen</h5>
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th scope="col">No</th>
                                                                    <th colspan="2">Nama Berkas</th>
                                                                    <th scope="col">Status</th>
                                                                    <th scope="col">Waktu Pengumpulan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($documentHistories as $history)
                                                                    <tr>
                                                                        <td class="text-center">{{ $loop->iteration }}
                                                                        </td>
                                                                        <td>{{ $history->dokumen }}</td>
                                                                        <td>
                                                                            <a href="{{ asset('dokumenPR/' . $history->dokumen) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-sm"
                                                                                title="Buka Dokumen">
                                                                                <i class="fa-solid fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            @if ($history->status == 'approved')
                                                                                <span
                                                                                    class="badge badge-success">Disetujui</span>
                                                                            @elseif($history->status == 'rejected')
                                                                                <span
                                                                                    class="badge badge-danger">Ditolak</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge badge-warning">Pending</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ \Carbon\Carbon::parse($history->uploaded_at)->format('d F Y') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif

                                                    @if (count($filteredDocuments) > 0)
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th scope="col">No</th>
                                                                    <th colspan="2">Nama Berkas</th>
                                                                    <th scope="col">Keterangan</th>
                                                                    <th scope="col">Waktu Pengumpulan</th>
                                                                    <th scope="col">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $no = 1; @endphp
                                                                @foreach ($filteredDocuments as $document)
                                                                    <tr>
                                                                        <td class="text-center">{{ $no++ }}</td>
                                                                        <td>{{ $document['name'] }}</td>
                                                                        <td>
                                                                            <a href="{{ asset($document['path'] . '/' . $document['name']) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-sm"
                                                                                title="Buka Dokumen">
                                                                                <i class="fa-solid fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td>{{ $document['label'] }}</td>
                                                                        <td class="text-center">
                                                                            {{ \Carbon\Carbon::parse($document['uploaded_at'])->format('d F Y') }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            @if ($firstPeta->approvalPr == 'approved')
                                                                                <span
                                                                                    class="badge badge-success">Disetujui</span>
                                                                                <div>
                                                                                    <small>{{ \Carbon\Carbon::parse($firstPeta->approvalPr_at)->format('d F Y') }}</small>
                                                                                </div>
                                                                            @elseif($firstPeta->approvalPr == 'rejected')
                                                                                <span
                                                                                    class="badge badge-danger">Ditolak</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge badge-warning">Pending</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @endif --}}

                {{-- Komentar --}}
                {{-- @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2 || Auth::user()->id_level == 3 || Auth::user()->id_level == 4 || Auth::user()->id_level == 6)
                                        <tr>
                                            <th class="col-2">Komentar : </th>
                                            <td>
                                                <div class="card mt-4">
                                                    <div class="card-header">Tambah Komentar Aspek Keungan</div>
                                                    <div class="card-body">
                                                        <form action="{{ route('postComment', $firstPeta->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" value="aspek" name="jenis">
                                                            <div class="form-group">
                                                                <textarea name="comment" class="form-control" rows="3" placeholder="Masukkan komentar" required></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Kirim</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="card mt-4">
                                                    <div class="card-header">Tambah Komentar Analisis Risiko</div>
                                                    <div class="card-body">
                                                        <form action="{{ route('postComment', $firstPeta->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" value="analisis" name="jenis">
                                                            <div class="form-group">
                                                                <textarea name="comment" class="form-control" rows="3" placeholder="Masukkan komentar" required></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Kirim</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($comment_prs_aspek->isNotEmpty() || $comment_prs_analisis->isNotEmpty())
                                        <tr>
                                            <th class="col-2">Daftar Komentar</th>
                                            <td>
                                                <!-- Daftar Komentar -->
                                                <div class="card mt-4">
                                                    <div class="card-header">Komentar Aspek Keuangan</div>
                                                    <div class="card-body">
                                                        @forelse($comment_prs_aspek as $comment)
                                                            <div class="media mb-3">
                                                                <div class="media-body">
                                                                    <h5 class="mt-0">{{ $comment->user->name }}</h5>
                                                                    <p>{{ $comment->comment }}</p>
                                                                    <small>{{ $comment->created_at->format('d M Y') }}</small>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        @empty
                                                            <p>Belum ada komentar.</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Daftar Komentar -->
                                                <div class="card mt-4">
                                                    <div class="card-header">Komentar Analisis Risiko</div>
                                                    <div class="card-body">
                                                        @forelse($comment_prs_analisis as $comment)
                                                            <div class="media mb-3">
                                                                <div class="media-body">
                                                                    <h5 class="mt-0">{{ $comment->user->name }}</h5>
                                                                    <p>{{ $comment->comment }}</p>
                                                                    <small>{{ $comment->created_at->format('d M Y') }}</small>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        @empty
                                                            <p>Belum ada komentar.</p>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif --}}
                {{-- </table>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </section>
    </div>

    @push('style')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container .select2-selection--single {
                height: 38px;
                /* Adjust height to match other form controls */
            }

            .select2-container .select2-selection--single .select2-selection__rendered {
                line-height: 30px;
                /* Align text vertically */
            }

            .select2-container .select2-selection--single .select2-selection__arrow {
                height: 36px;
                /* Adjust height of the dropdown arrow */
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Load jQuery and Bootstrap JavaScript -->
        {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    @endpush

@endsection
