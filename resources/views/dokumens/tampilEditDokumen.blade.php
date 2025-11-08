@extends('layout.app')
@section('title', 'Edit Dokumen')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Edit Data Dokumen</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <h5>Dokumen Perbaikan:</h5>
                                <a
                                    href="{{ asset('koreksi_reviu_pic/' . $dokumens->koreksiReviuPIC) }}">{{ $dokumens->koreksiReviuPIC }}</a>
                                <h5>Komentar PIC:</h5>
                                @foreach ($comments as $comment)
                                    @if ($comment->type === 'pic')
                                        <li>{{ $comment->comment }}</li>
                                        <!-- Tambahkan informasi tambahan seperti waktu komentar atau penulis jika perlu -->
                                    @endif
                                @endforeach
                                <form action="/updateDataDokumen/{{ $dokumens->id }}" method="POST"
                                    enctype="multipart/form-data">

                                    @csrf

                                    {{-- <div class="form-group">
                                <label class="font-weight-bold">JUDUL</label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" name="judul" value="{{ $dokumens->judul }}" placeholder="Masukkan Judul Dokumen...">
    
                                <!-- error message untuk nama -->
                                @error('judul')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div> --}}

                                    {{-- <div class="form-group">
                                <label class="font-weight-bold">JENIS</label>
                                <select name ="jenis" class="form-control">
                                    <option value="">- Pilih Jenis Dokumen -</option>
                                    <option value="Peraturan">Reviu</option>
                                    <option value="Template">Keuangan</option>
                                </select>
    
                                <!-- error message untuk merek -->
                                @error('jenis')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div> --}}

                                    <div class="card border mb-3">
                                        <label for="dokumen" class="form-label m-2"><b>Upload ulang dokumen reviu
                                                (.dox/.docx)</b></br>
                                            <small>ukuran maksimal 10MB *</small></label>
                                        <div class="input-group mb-3">
                                            <input type="file" name="dokumen" class="form-control m-2"
                                                id="inputGroupFile" accept=".doc,.docx">
                                            <label for="inputGroupFile" class="input-group-text m-2">Upload</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-md btn-primary">SIMPAN</button>
                                    <button type="reset" class="btn btn-md btn-warning">RESET</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>

@endsection
