@extends('layout.app')
@section('title', 'Detail Peta Risiko')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Detail Peta Risiko</h1>
            </div>
            <div class="section-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="tittle-1">
                        <span class="span0">Detail Peta</span>
                    </h4>
                    @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                        {{-- <a href="{{ route('petas.destroy', $petas->id) }}" class="btn btn-danger mb-2">
                            Hapus Kegiatan
                        </a>
                        <form action="{{ route('petas.destroy', $petas->id) }}" method=""></form> --}}
                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                        action="{{ route('petas.destroy', $petas->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn bg-danger p-2 text-white"
                                                            title="Hapus Kegiatan"><i class="fa-solid fa-trash"></i> Hapus Kegiatan</button>
                                                    </form>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">

                                <table class="table table-white table-sm">
                                    <tr>
                                        <th class="col-2">Judul Kegiatan : </th>
                                        <td>{{ $petas->judul }}</td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Unit Kerja : </th>
                                        <td>{{ $petas->jenis }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th class="col-2">PIC : </th>
                                        <td>{{ $petas->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">IKU : </th>
                                        <td>{{ $petas->kegiatan->iku }}</td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Kode Regist : </th>
                                        <td>{{ $petas->kode_regist }}</td>
                                    </tr> --}}
                                    <tr>
                                        <th class="col-2">Identifikasi Risiko : </th>
                                        <td>
                                            <table class="table table-bordered">
                                                <tbody>
                                                    {{-- <tr>
                                                        <th class="col-3">Sasaran Strategis:</th>
                                                        <td>{{ $petas->kegiatan->sasaran }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Program Kerja:</th>
                                                        <td>{{ $petas->kegiatan->proker }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Indikator:</th>
                                                        <td>{{ $petas->kegiatan->indikator }}</td>
                                                    </tr> --}}
                                                    <tr>
                                                        <th class="col-3">Anggaran:</th>
                                                        <td>{{ $petas->anggaran }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Pernyataan Risiko:</th>
                                                        <td>{{ $petas->pernyataan }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Kategori Risiko:</th>
                                                        <td>{{ $petas->kategori }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Uraian Dampak:</th>
                                                        <td>{{ $petas->uraian }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Metode Pencapaian:</th>
                                                        <td>{{ $petas->metode }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Skor Probabilitas:</th>
                                                        <td>{{ $petas->skor_kemungkinan }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Skor Dampak:</th>
                                                        <td>{{ $petas->skor_dampak }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="col-3">Tingkat Risiko:</th>
                                                        <td>{{ $petas->tingkat_risiko }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                {{-- <table class="table table-responsive">
                                    @if (Auth::user()->id_level == 1 ||
                                            Auth::user()->id_level == 2 ||
                                            Auth::user()->id_level == 3 ||
                                            Auth::user()->id_level == 4 ||
                                            Auth::user()->id_level == 6)
                                        <tr>
                                            <th class="col-2">Komentar : </th>
                                            <td>
                                                <div class="card mt-4">
                                                    <div class="card-header">Tambah Komentar Aspek Keungan
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="{{ route('postComment', $petas->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" value="keuangan" name="jenis">
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
                                                    <div class="card-header">Tambah Komentar Analisis Risiko
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="{{ route('postComment', $petas->id) }}"
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
                                            <th class="col-2">Daftar Komentar:</th>
                                            <td colspan="2">
                                                <div class="d-flex justify-content-between">
                                                    <div class="card mt-4 flex-grow-1 me-3">
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

                                                    <div class="card mt-4 flex-grow-1 ms-3">
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
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                </table> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <!-- Load jQuery and Bootstrap JavaScript -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
