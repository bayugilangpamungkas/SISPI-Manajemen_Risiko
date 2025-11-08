@extends('layout.app')
@section('title', 'Tindak Lanjut')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Tindak Lanjut</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Tindak Lanjut</span>
                </h4>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <form action="{{ route('tindak-lanjut.index') }}" method="GET" class="form-inline">
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
                                    <a href="{{ route('tindak-lanjut.create') }}" class="btn btn-md btn-success mb-3"
                                        style="font-size: 0.85rem !important;">TAMBAH/EDIT Rekomendasi</a>
                                @endif
                                <form action="{{ route('tindak-lanjut.export-excel') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="year" value="{{ request('year') }}">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-success mb-3 float-right">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </form>
                                @if ($posts->isEmpty())
                                    <table class="table table-bordered">
                                    @else
                                        <table class="table table-bordered table-responsive">
                                @endif
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Judul</th>
                                        <th scope="col">Jenis Kegiatan</th>
                                        <th colspan="2" scope="col">Dokumen</th>
                                        <th scope="col">Waktu Pengumpulan</th>
                                        <th scope="col">Temuan</th>
                                        <th scope="col">Rekomendasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($posts->currentPage() - 1) * $posts->perPage() + 1; @endphp
                                    @forelse ($posts as $post)
                                        @php $rtmCount = $post->rtm->count(); @endphp
                                        @foreach ($post->rtm as $index => $rtm)
                                            <tr>
                                                @if ($index == 0)
                                                    <td class="text-center" rowspan="{{ $rtmCount }}">
                                                        {{ $no++ }}
                                                    </td>
                                                @endif
                                                @if ($index == 0)
                                                    <td class="text" rowspan="{{ $rtmCount }}">
                                                        {{ $post->judul_tindak_lanjut }}
                                                    </td>
                                                @endif
                                                @if ($index == 0)
                                                    <td class="text" rowspan="{{ $rtmCount }}">
                                                        {{ $post->jenis_kegiatan }}
                                                    </td>
                                                @endif
                                                @if ($index == 0)
                                                    <td class="text" rowspan="{{ $rtmCount }}">
                                                        {{ $post->dokumen_tindak_lanjut }}
                                                    </td>
                                                @endif
                                                @if ($index == 0)
                                                    <td rowspan="{{ $rtmCount }}">
                                                        <a href="{{ asset('dokumen_tindaklanjut/' . $post->dokumen_tindak_lanjut) }}"
                                                            target="_blank" class="btn btn-info btn-sm"
                                                            title="Buka Dokumen">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                    </td>
                                                @endif
                                                @if ($index == 0)
                                                    <td class="text-center" rowspan="{{ $rtmCount }}">
                                                        {{ \Carbon\Carbon::parse($post['tindakLanjut_at'])->format('d F Y') }}
                                                    </td>
                                                @endif
                                                <td class="text">
                                                    {{ $rtm->temuan }}
                                                </td>
                                                <td class="text">
                                                    {{ $rtm->rekomendasi }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <div class="alert alert-danger">
                                            Data Dokumen belum Tersedia.
                                        </div>
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
