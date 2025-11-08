@extends('layout.app')
@section('title','Tambah User')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Tambah User</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow rounded">
                        <div class="card-body">
                            <form action="{{ route('users.tambah') }}" method="POST" enctype="multipart/form-data">
        
                                @csrf
        
                                <div class="form-group">
                                    <label class="font-weight-bold">NAMA</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Masukkan Nama...">
        
                                    <!-- error message untuk nama -->
                                    @error('name')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
        
                                <div class="form-group">
                                    <label class="font-weight-bold">USERNAME</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Masukkan Username...">
        
                                    <!-- error message untuk nama -->
                                    @error('username')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">EMAIL</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Masukkan Email...">
        
                                    <!-- error message untuk merek -->
                                    @error('email')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">NIP</label>
                                    <input type="text" class="form-control @error('nip') is-invalid @enderror" name="nip" value="{{ old('nip') }}" placeholder="Masukkan NIP...">
        
                                    <!-- error message untuk merek -->
                                    @error('nip')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">PASSWORD</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" placeholder="Masukkan Password...">
        
                                    <!-- error message untuk merek -->
                                    @error('password')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">KONFIRMASI PASSWORD</label>
                                    <input type="password" class="form-control" id="confirmation" name="confirmation" placeholder="Password Confirmation">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">UNIT KERJA</label>
                                    <select id="unit_kerja" name ="id_unit_kerja" class="form-control">
                                        <option value="">- Pilih Unit Kerja -</option>
                                        @foreach ($unit_kerjas as $unit_kerja)
                                            <option value="{{ $unit_kerja->id }}"
                                                {{ old('id_unit_kerja') == $unit_kerja ? 'selected' : '' }}>{{ $unit_kerja->nama_unit_kerja }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <!-- error message untuk merek -->
                                    @error('unit_kerja')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">JABATAN</label>
                                    <select id="level" name ="id_level" class="form-control">
                                        <option value="">- Pilih Jabatan -</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level->id }}" {{ old('id_level') == $level ? 'selected' : '' }}>{{ $level->name }}</option>
                                        @endforeach
                                        {{-- @for ($i = 2; $i <= 5; $i++)
                                            @php
                                                $level = '';
                                                switch ($i) {
                                                    case 2:
                                                        $level = 'Admin';
                                                        break;
                                                    case 3:
                                                        $level = 'Ketua';
                                                        break;
                                                    case 4:
                                                        $level = 'Anggota';
                                                        break;
                                                    case 5:
                                                        $level = 'Auditee';
                                                        break;
                                                }
                                            @endphp
                                            <option value="{{ $i }}" {{ old(level) == $i ? 'selected' : null }}>{{ $level }}</option>
                                            <option value="{{ $i }}" {{ old('level') == $i ? 'selected' : '' }}>{{ $level }}</option>
                                        @endfor --}}
                                    </select>
                                    <!-- error message untuk merek -->
                                    @error('level')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div id="auditeeForm" style="display: none;">
                                    <div class="form-group">
                                        <label class="font-weight-bold">AUDITEE BAGIAN</label>
                                        <select name="bagian_auditee" class="form-control">
                                            <option value="">- Pilih Bagian -</option>
                                            <option value="1">Bagian 1</option>
                                            <option value="2">Bagian 2</option>
                                            <option value="3">Bagian 3</option>
                                            <option value="4">Bagian 4</option>
                                            <option value="5">Bagian 5</option>
                                        </select>
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
<script>
    $(document).ready(function(){
        $('#level').change(function(){
            if($(this).val() == '5'){ // If Auditee is selected
                $('#auditeeForm').show();
            } else {
                $('#auditeeForm').hide();
            }
        });
    });
</script>

@endsection
