@extends('layout.auth')

@section('title', 'Register')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Register</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">

                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">NAMA</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" placeholder="Masukkan Nama...">

                        <!-- error message untuk nama -->
                        @error('name')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">USERNAME</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username"
                            value="{{ old('username') }}" placeholder="Masukkan Username...">

                        <!-- error message untuk nama -->
                        @error('username')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">EMAIL</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" placeholder="Masukkan Email...">

                        <!-- error message untuk merek -->
                        @error('email')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">NIP</label>
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" name="nip"
                            value="{{ old('nip') }}" placeholder="Masukkan NIP...">

                        <!-- error message untuk merek -->
                        @error('nip')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">PASSWORD</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                            value="{{ old('password') }}" placeholder="Masukkan Password...">

                        <!-- error message untuk merek -->
                        @error('password')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">KONFIRMASI PASSWORD</label>
                        <input type="password" class="form-control" id="confirmation" name="confirmation"
                            placeholder="Password Confirmation">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">UNIT KERJA</label>
                        <select id="unit_kerja" name ="id_unit_kerja" class="form-control">
                            <option value="">- Pilih Unit Kerja -</option>
                            @foreach ($unit_kerjas as $unit_kerja)
                                <option value="{{ $unit_kerja->id }}"
                                    {{ old('id_unit_kerja') == $unit_kerja ? 'selected' : '' }}>
                                    {{ $unit_kerja->nama_unit_kerja }}
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
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">JABATAN</label>
                        <select id="level" name ="id_level" class="form-control">
                            <option value="">- Pilih Jabatan -</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ old('id_level') == $level ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        <!-- error message untuk merek -->
                        @error('level')
                            <div class="alert alert-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    {{-- <div id="auditeeForm" style="display: none;">
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
                    </div> --}}
                    <div class="form-group col-md-12" style="text-align: end">
                        <button type="reset" class="btn btn-md btn-warning">RESET</button>
                        <button type="submit" class="btn btn-md btn-primary">REGISTER</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('library/jquery.pwstrength/jquery.pwstrength.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/auth-register.js') }}"></script>
@endpush
