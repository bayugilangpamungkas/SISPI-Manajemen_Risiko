@extends('layout.app')
@section('title', 'Dokumen')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Dokumen</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Dokumen</span>
                </h4>
                <div class="row">

                    <div class="col-md-5 mb-2 d-flex justify-content-start align-items-center">
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
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 5)
                                            <a href="{{ route('dokumens.create') }}"
                                                class="btn btn-md btn-success mb-3">TAMBAH
                                                DOKUMEN</a>
                                        @endif
                                    </div>
                                </div>
                                @if ($dokumens->isEmpty())
                                    <table class="table table-bordered">
                                    @else
                                        <table class="table table-bordered table-responsive">
                                @endif
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Judul</th>
                                        <th scope="col">Jenis</th>
                                        <th colspan="2" scope="col">Dokumen</th>
                                        <th scope="col">Waktu Pengumpulan</th>
                                        <th scope="col">Status</th>
                                        @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 5)
                                            <th colspan="2" scope="col">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($dokumens->currentPage() - 1) * $dokumens->perPage() + 1; @endphp
                                    @forelse ($dokumens as $dokumen)
                                        @if ($dokumen->hasilReviu && $dokumen->dokumen_tindak_lanjut)
                                            {{-- Jika memiliki kedua dokumen --}}
                                            <tr>
                                                <td class="text-center" rowspan="2">{{ $no++ }}</td>
                                                <td class="text-center" rowspan="2">{{ $dokumen->judul }}</td>
                                                <td class="text-center">Dokumen Reviu</td>
                                                <td class="text">{{ $dokumen->hasilReviu }}</td>
                                                <td>
                                                    <a href="{{ asset('hasil_reviu/' . $dokumen->hasilReviu) }}"
                                                        target="_blank" class="btn btn-info btn-sm" title="Buka Dokumen">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($dokumen->hasilReviu_uploaded_at)->format('d F Y') }}
                                                </td>
                                                <td class="text-center" rowspan="2">
                                                    @if ($dokumen->approvalReviu == 'approved')
                                                        <span class="badge badge-success">Disetujui</span>
                                                        <div>
                                                            <small>{{ \Carbon\Carbon::parse($dokumen->approvalReviu_at)->format('d F Y') }}</small>
                                                        </div>
                                                    @elseif($dokumen->approvalReviu == 'rejected' || $dokumen->approvalReviuPIC == 'rejected')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 5)
                                                    <td rowspan="2">
                                                        <a href="/tampilDataDokumen/{{ $dokumen->id }}"
                                                            class="btn fa-regular fa-pen-to-square bg-warning p-2 text-white"
                                                            data-toggle="tooltip" title="Edit Dokumen"></a>
                                                    </td>
                                                    <td class="text-center" rowspan="2">
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('dokumens.destroy', $dokumen->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                                data-toggle="tooltip" title="Hapus Dokumen"></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td class="text-center">Dokumen Tindak Lanjut</td>
                                                <td class="text">{{ $dokumen->dokumen_tindak_lanjut }}</td>
                                                <td>
                                                    <a href="{{ asset('dokumen_tindak_lanjut/' . $dokumen->dokumen_tindak_lanjut) }}"
                                                        target="_blank" class="btn btn-info btn-sm" title="Buka Dokumen">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($dokumen->tindakLanjut_at)->format('d F Y') }}
                                                </td>
                                            </tr>
                                        @elseif($dokumen->hasilReviu)
                                            {{-- Jika hanya memiliki dokumen reviu --}}
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td class="text-center">{{ $dokumen->judul }}</td>
                                                <td class="text-center">Dokumen Reviu</td>
                                                <td class="text">{{ $dokumen->hasilReviu }}</td>
                                                <td>
                                                    <a href="{{ asset('hasil_reviu/' . $dokumen->hasilReviu) }}"
                                                        target="_blank" class="btn btn-info btn-sm" title="Buka Dokumen">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($dokumen->hasilReviu_uploaded_at)->format('d F Y') }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($dokumen->approvalReviu == 'approved')
                                                        <span class="badge badge-success">Disetujui</span>
                                                        <div>
                                                            <small>{{ \Carbon\Carbon::parse($dokumen->approvalReviu_at)->format('d F Y') }}</small>
                                                        </div>
                                                    @elseif($dokumen->approvalReviu == 'rejected' || $dokumen->approvalReviuPIC == 'rejected')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 5)
                                                    <td>
                                                        <a href="/tampilDataDokumen/{{ $dokumen->id }}"
                                                            class="btn fa-regular fa-pen-to-square bg-warning p-2 text-white"
                                                            data-toggle="tooltip" title="Edit Dokumen"></a>
                                                    </td>
                                                    <td class="text-center">
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('dokumens.destroy', $dokumen->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                                data-toggle="tooltip" title="Hapus Dokumen"></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @elseif($dokumen->dokumen_tindak_lanjut)
                                            {{-- Jika hanya memiliki dokumen tindak lanjut --}}
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td class="text-center">{{ $dokumen->judul }}</td>
                                                <td class="text-center">Dokumen Tindak Lanjut</td>
                                                <td class="text">{{ $dokumen->dokumen_tindak_lanjut }}</td>
                                                <td>
                                                    <a href="{{ asset('dokumen_tindak_lanjut/' . $dokumen->dokumen_tindak_lanjut) }}"
                                                        target="_blank" class="btn btn-info btn-sm" title="Buka Dokumen">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($dokumen->tindakLanjut_at)->format('d F Y') }}
                                                </td>
                                                <td class="text-center">
                                                    <!-- Kosong karena tidak ada status approval untuk dokumen tindak lanjut -->
                                                </td>
                                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 5)
                                                    <td>
                                                        <a href="/tampilDataDokumen/{{ $dokumen->id }}"
                                                            class="btn fa-regular fa-pen-to-square bg-warning p-2 text-white"
                                                            data-toggle="tooltip" title="Edit Dokumen"></a>
                                                    </td>
                                                    <td class="text-center">
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('dokumens.destroy', $dokumen->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                                data-toggle="tooltip" title="Hapus Dokumen"></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="alert alert-danger">
                                                    Data Dokumen belum Tersedia.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                </table>
                                <!-- PAGINATION (Hilangi -- nya)-->
                                {{ $dokumens->links('pagination::bootstrap-4') }}

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
