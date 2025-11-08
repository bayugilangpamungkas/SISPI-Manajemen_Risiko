@extends('layout.app')
@section('title', 'Dokumen Tindak Lanjut')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Dokumen Tindak Lanjut</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Dokumen</span>
                </h4>
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <form action="/tindakLanjut/search" class="form=inline" method="GET">
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
                                @if (auth()->user()->id_level == 1 ||
                                        auth()->user()->id_level == 2 ||
                                        auth()->user()->id_level == 3 ||
                                        auth()->user()->id_level == 4 ||
                                        auth()->user()->id_level == 6)
                                    <a href="{{ route('posts.index') }}" class="btn btn-md btn-outline-primary mb-3"
                                        style="font-size: 0.85rem !important;">RENCANA KEGIATAN</a>
                                @endif
                                @if (auth()->user()->id_level == 1 ||
                                        auth()->user()->id_level == 2 ||
                                        auth()->user()->id_level == 3 ||
                                        auth()->user()->id_level == 6)
                                    <a href="{{ route('reviewKetua') }}" class="btn btn-md btn-outline-primary mb-3"
                                        style="font-size: 0.85rem !important;">APPROVE KEGIATAN</a>
                                @endif
                                @if (auth()->user()->id_level == 1 ||
                                        auth()->user()->id_level == 2 ||
                                        auth()->user()->id_level == 3 ||
                                        auth()->user()->id_level == 4 ||
                                        auth()->user()->id_level == 6)
                                    <a href="{{ route('laporanAkhir') }}" class="btn btn-md btn-outline-primary mb-3"
                                        style="font-size: 0.85rem !important;">LAPORAN AKHIR</a>
                                @endif
                                {{-- <a href="{{ route('rtm') }}" class="btn btn-md btn-outline-primary mb-3"
                                    style="font-size: 0.85rem !important;">RTM</a> --}}
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
                                        <tr>
                                            <td class="text-center">
                                                {{ $no++ }}
                                            </td>
                                            <td class="text-center">
                                                {{ $post->judul_tindak_lanjut }}
                                            </td>
                                            <td class="text-center">
                                                {{ $post->jenis_kegiatan }}
                                            </td>
                                            <td class="text">
                                                {{ $post->dokumen_tindak_lanjut }}
                                            </td>
                                            <td>
                                                <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                <a href="{{ asset('dokumen_tindaklanjut/' . $post->dokumen_tindak_lanjut) }}"
                                                    target="_blank" class="btn btn-info btn-sm" title="Buka Dokumen">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($post['tindakLanjut_at'])->format('d F Y') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $post->temuan }}
                                            </td>
                                            <td class="text-center">
                                                {{ $post->rekomendasi }}
                                            </td>
                                        </tr>
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
