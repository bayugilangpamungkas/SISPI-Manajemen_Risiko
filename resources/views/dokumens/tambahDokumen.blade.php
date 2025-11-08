@extends('layout.app')
@section('title','Tambah Dokumen')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Tambah Dokumen</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow rounded">
                        <div class="card-body">
                            <form action="{{ route('dokumens.store') }}" method="POST" enctype="multipart/form-data">
        
                                @csrf
        
                                <div class="form-group">
                                    <label class="font-weight-bold">JUDUL KEGIATAN</label>
                                    <select name="judul" class="form-control select2 @error('jenis') is-invalid @enderror">
                                        <option value="" disabled selected>Pilih Judul Kegiatan</option>
                                        @foreach ($post as $p)
                                            <option value="{{ $p->id }}">{{ $p->judul }}</option>
                                        @endforeach
                                    </select>
        
                                    <!-- error message untuk nama -->
                                    @error('judul')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
        
                                {{-- <div class="form-group">
                                    <label class="font-weight-bold">JENIS</label>
                                    <select name ="jenis" class="form-control">
                                        <option value="">- Pilih Jenis Dokumen -</option>
                                        <option value="Reviu">Reviu</option>
                                        <option value="Audit">Audit</option>
                                    </select>
        
                                    <!-- error message untuk merek -->
                                    @error('jenis')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div> --}}
        
                                <div class="card border mb-3">
        
                                <label for="dokumen" class="form-label m-2"><b>Upload dokumen reviu (.doc / .docx *)</b></br>
                                <small>ukuran maksimal 10MB</small></label>
                                <div class="input-group mb-3">
                                    <input type="file" name="dokumen" class="form-control m-2" id="inputGroupFile" accept=".docx, .doc">
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


@push('style')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
        <!-- Jquery harus dimuat terlebih dahulu -->
        {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
        <!-- Kemudian, Bootstrap -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Pilih Judul Kegiatan',
                    allowClear: true
                });
            });
        </script>
    @endpush

@endsection
