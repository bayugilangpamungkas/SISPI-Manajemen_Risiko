@extends('layout.app')
@section('title', 'Edit Dokumen')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Edit Dokumen</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow rounded">
                        <div class="card-body">
                            <form action="/updateData/{{ $petas->id }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="font-weight-bold">JUDUL</label>
                                    <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                        name="judul" value="{{ old('judul') }}" placeholder="Masukkan Judul Tugas...">
    
                                    <!-- error message untuk judul -->
                                    @error('judul')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
    
                                <div class="form-group">
                                    <label class="font-weight-bold">JENIS</label>
                                    <input type="text" class="form-control @error('jenis') is-invalid @enderror"
                                        name="jenis" value="{{ old('jenis') }}" placeholder="Masukkan Jenis Tugas...">
    
                                    <!-- error message untuk jenis -->
                                    @error('jenis')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
    
                                <div class="card border mb-3">
                                    <label for="dokumen" class="form-label m-2"><b>DOKUMEN PETA RISIKO</b></label>
                                    <div class="input-group mb-3">
                                        <input type="file" name="dokumen" class="form-control m-2" id="inputGroupFile">
                                        <label for="inputGroupFile" class="input-group-text m-2">Upload</label>
                                    </div>
                                    <small class="form-text text-danger ml-4" style="font-style: italic;">
                                        *dokumen harus berformat excel
                                    </small>
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

@endsection
