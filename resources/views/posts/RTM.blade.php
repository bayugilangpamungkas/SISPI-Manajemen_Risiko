@extends('layout.app')
@section('title', 'Dokumen RTM')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Dokumen RTM</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Kegiatan</span>
                </h4>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <form action="{{ route('rtm') }}" method="GET" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="search" name="search" class="form-control"
                                    placeholder="Search: Masukkan Judul" value="{{ request('search') }}">
                            </div>

                            <div class="input-group mr-2">
                                <select name="year" class="form-control">
                                    <option value="">Pilih Tahun</option>
                                    @php
                                        $currentYear = date('Y');
                                        $startYear = 2020; // Sesuaikan dengan tahun awal data
                                    @endphp
                                    @for ($year = $currentYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>

                            @if (request('search') || request('year'))
                                <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary ml-2">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow rounded">
                        <div class="card-body">
                            @php
                                $showButton = false;
                                foreach ($pic as $item) {
                                    if (
                                        $item->tanggungjawab == auth()->user()->name &&
                                        !in_array($item->id_level, [1, 2, 3, 6])
                                    ) {
                                        $showButton = true;
                                        break;
                                    }
                                }

                                if (!$showButton && in_array(auth()->user()->id_level, [1, 2, 3, 6])) {
                                    $showButton = true;
                                }
                            @endphp

                            @if ($showButton)
                                <a href="{{ route('rtm.create') }}" class="btn btn-md btn-success mb-3"
                                    style="font-size: 0.85rem !important;">TAMBAH/EDIT RTM</a>
                            @endif
                            <a href="{{ route('rtm.export-excel', ['year' => request('year'), 'search' => request('search')]) }}"
                                class="btn btn-success mb-3 float-right" style="font-size: 0.85rem !important;">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Kegiatan</th>
                                        <th scope="col">PIC</th>
                                        <th scope="col">Rekomendasi</th>
                                        <th scope="col">Rencana Tindak Lanjut</th>
                                        <th scope="col">Rencana Waktu Tindak Lanjut</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($posts->currentPage() - 1) * $posts->perPage() + 1; @endphp
                                    @forelse ($posts as $post)
                                        @php $rtmCount = $post->rtm->count(); @endphp

                                        {{-- Iterasi setiap RTM terkait --}}
                                        @foreach ($post->rtm as $index => $rtm)
                                            <tr>
                                                {{-- Kolom No (hanya tampil di baris pertama RTM) --}}
                                                @if ($index == 0)
                                                    <td class="text-center" rowspan="{{ $rtmCount }}">
                                                        {{ $no++ }}
                                                    </td>
                                                @endif

                                                {{-- Kolom Judul (hanya tampil di baris pertama RTM) --}}
                                                @if ($index == 0)
                                                    <td class="text" rowspan="{{ $rtmCount }}">
                                                        {{ $post->judul }}
                                                    </td>
                                                @endif

                                                {{-- Kolom Tanggung Jawab (hanya tampil di baris pertama RTM) --}}
                                                @if ($rtm->pic_rtm->isEmpty())
                                                    <td></td>
                                                @else
                                                    <td class="text-center">
                                                        @foreach ($rtm->pic_rtm as $pic)
                                                            <span class="badge badge-primary">
                                                                {{ $pic->unitKerja->nama_unit_kerja }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                @endif

                                                {{-- Kolom Rekomendasi (dari RTM) --}}
                                                <td class="text">
                                                    {{ $rtm->rekomendasi }}
                                                </td>

                                                {{-- Kolom Rencana Tindak Lanjut --}}
                                                <td class="text">
                                                    {{ $rtm->rencanaTinJut }}
                                                </td>

                                                {{-- Kolom Rencana Waktu Tindak Lanjut --}}
                                                <td class="text-center">
                                                    @if ($rtm->rencanaWaktuTinJut)
                                                        {{ \Carbon\Carbon::parse($rtm->rencanaWaktuTinJut)->format('d F Y') }}
                                                    @endif
                                                </td>

                                                {{-- Kolom Status RTM --}}
                                                <td class="text">
                                                    @if ($rtm->status_rtm == 'Open')
                                                        <span class="badge badge-success">Open</span>
                                                    @elseif($rtm->status_rtm == 'Closed')
                                                        <span class="badge badge-danger">Closed</span>
                                                    @else
                                                        <span class="badge badge-warning">In Progress</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="alert alert-danger">
                                                    Data Dokumen belum Tersedia.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                            <!-- PAGINATION (Hilangi -- nya)-->
                            {{ $posts->links('pagination::bootstrap-4') }}

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
    </script>
    </section>
    </div>
@endsection
