{{-- @extends('layout.app')
@section('title', 'Tambah Rekomendasi')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>{{ isset($post) ? 'Edit Rekomendasi' : 'Tambah Rekomendasi' }}</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form action="{{ isset($post) ? route('tindak-lanjut.updateRekomendasi', $post->id) : route('tindak-lanjut.storeRekomendasi') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <!-- Judul -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Judul</label>
                                        <select name="judul" id="judul" class="form-control">
                                            <option value="">Pilih Judul</option>
                                            @foreach ($posts as $p)
                                                <option value="{{ $p->id }}" {{ isset($post) && $post->id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->judul }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('judul')
                                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Daftar RTM -->
                                    <div id="rtm-container">
                                        @if (isset($post) && $post->rtms->isNotEmpty())
                                            @foreach ($post->rtms as $index => $rtm)
                                                @include('partials.rekomendasi-form', ['index' => $index, 'rtm' => $rtm])
                                            @endforeach
                                        @else
                                            @include('partials.rekomendasi-form', ['index' => 0])
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" id="add-rtm" class="btn btn-md btn-success">Tambah RTM</button>
                                    </div>


                                    <button type="submit" class="btn btn-md btn-primary">SUBMIT</button>
                                    <button type="reset" class="btn btn-md btn-warning">RESET</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let rtmIndex = {{ isset($post) ? $post->rtms->count() : 1 }};

                // Tambah RTM
                $('#add-rtm').on('click', function() {
                    const newRtmForm = `
                        @include('partials.rekomendasi-form', ['index' => '__INDEX__'])
                    `.replace(/__INDEX__/g, rtmIndex);

                    $('#rtm-container').append(newRtmForm);
                    rtmIndex++;
                });

                // Hapus RTM
                $(document).on('click', '.remove-rtm', function() {
                    $(this).closest('.rtm-form').remove();
                });
            });
        </script>
    @endpush
@endsection --}}


{{-- @extends('layout.app')
@section('title', 'Tambah Rekomendasi')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>{{ isset($post) ? 'Edit Rekomendasi' : 'Tambah Rekomendasi' }}</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form action="{{ isset($post) ? route('tindak-lanjut.updateRekomendasi', $post->id) : route('tindak-lanjut.storeRekomendasi') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Judul -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Judul</label>
                                        <select name="judul" id="judul" class="form-control">
                                            <option value="">Pilih Judul</option>
                                            @foreach ($posts as $p)
                                                <option value="{{ $p->id }}" {{ isset($post) && $post->id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->judul }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('judul')
                                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Daftar RTM -->
                                    <div id="rtm-container">
                                        @if (isset($post) && $post->rtms->isNotEmpty())
                                            @foreach ($post->rtms as $index => $rtm)
                                                @include('partials.rekomendasi-form', ['index' => $index, 'rtm' => $rtm])
                                            @endforeach
                                        @else
                                            @include('partials.rekomendasi-form', ['index' => 0])
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" id="add-rtm" class="btn btn-md btn-success">Tambah RTM</button>
                                    </div>

                                    <button type="submit" class="btn btn-md btn-primary">SUBMIT</button>
                                    <button type="reset" class="btn btn-md btn-warning">RESET</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let rtmIndex = {{ isset($post) ? $post->rtms->count() : 1 }};

                // Tambah RTM
                $('#add-rtm').on('click', function() {
                    const newRtmForm = `@include('partials.rekomendasi-form', ['index' => '__INDEX__'])`.replace(/__INDEX__/g, rtmIndex);
                    $('#rtm-container').append(newRtmForm);
                    rtmIndex++;
                });

                // Hapus RTM
                $(document).on('click', '.remove-rtm', function() {
                    $(this).closest('.rtm-form').remove();
                });

                // AJAX untuk mengambil data RTM berdasarkan Judul
                $('#judul').on('change', function() {
                    const postId = $(this).val();
                    if (postId) {
                        $.ajax({
                            url: '/get-rtm/' + postId,
                            type: 'GET',
                            success: function(response) {
                                // Kosongkan semua form RTM sebelumnya
                                $('#rtm-container').empty();

                                // Tambahkan data RTM ke form
                                if (response && response.length > 0) {
                                    response.forEach(function(rtm, index) {
                                        const newRtmForm = `
                                            @include('partials.rekomendasi-form', ['index' => '__INDEX__', 'rtm' => rtm])
                                        `.replace(/__INDEX__/g, index).replace('__rtm__', JSON.stringify(rtm));
                                        $('#rtm-container').append(newRtmForm);
                                    });
                                } else {
                                    $('#rtm-container').append('<div class="alert alert-warning">Tidak ada RTM yang terkait dengan judul ini.</div>');
                                }
                            },
                            error: function() {
                                alert('Terjadi kesalahan saat memuat data RTM.');
                            }
                        });
                    } else {
                        $('#rtm-container').empty();
                    }
                });
            });
        </script>
    @endpush
@endsection --}}


@extends('layout.app')
@section('title', 'Tambah Rekomendasi')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>{{ isset($post) ? 'Edit Rekomendasi' : 'Tambah Rekomendasi' }}</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form
                                    action="{{ isset($post) ? route('tindak-lanjut.updateRekomendasi', $post->id) : route('tindak-lanjut.storeRekomendasi') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <!-- Judul -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Judul</label>
                                        <select name="judul" id="judul" class="form-control">
                                            <option value="">Pilih Judul</option>
                                            @foreach ($posts as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ isset($post) && $post->id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->judul }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('judul')
                                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Daftar RTM -->
                                    <div id="rtm-container">
                                        @if (isset($post) && $post->rtms->isNotEmpty())
                                            @foreach ($post->rtms as $index => $rtm)
                                                @include('partials.rekomendasi-form', [
                                                    'index' => $index,
                                                    'rtm' => $rtm,
                                                ])
                                            @endforeach
                                        @else
                                            @include('partials.rekomendasi-form', ['index' => 0])
                                        @endif
                                    </div>

                                    <button type="button" id="add-rtm" class="btn btn-md btn-success">Tambah RTM</button>
                                    <button type="submit" class="btn btn-md btn-primary">SUBMIT</button>
                                    <button type="reset" class="btn btn-md btn-warning">RESET</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let rtmIndex = {{ isset($post) ? $post->rtms->count() : 1 }};

                // Handle judul selection change
                $('#judul').on('change', function() {
                    const selectedId = $(this).val();
                    if (selectedId) {
                        // Fetch RTM data for selected judul
                        $.ajax({
                            url: `/get-rtm/${selectedId}`,
                            method: 'GET',
                            success: function(response) {
                                // Clear existing RTM forms
                                $('#rtm-container').empty();
                                rtmIndex = 0;

                                // Add RTM forms for each RTM in response
                                response.rtms.forEach(rtm => {
                                    const newRtmForm = `
                                        <div class="rtm-form mb-4 border rounded p-3 shadow-sm">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Temuan</label>
                                                <textarea name="rtm[${rtmIndex}][temuan]" class="form-control">${rtm.temuan || ''}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Rekomendasi</label>
                                                <textarea name="rtm[${rtmIndex}][rekomendasi]" class="form-control">${rtm.rekomendasi || ''}</textarea>
                                            </div>

                                            <div class="d-flex justify-content-end mb-3">
                                                <button type="button" class="btn btn-sm btn-danger remove-rtm">Hapus</button>
                                            </div>
                                        </div>
                                    `;
                                    $('#rtm-container').append(newRtmForm);
                                    rtmIndex++;
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching RTMs:', error);
                            }
                        });
                    } else {
                        // Clear RTM forms if no judul selected
                        $('#rtm-container').empty();
                        rtmIndex = 0;
                        $('#rtm-container').append(`@include('partials.rekomendasi-form', ['index' => 0])`);
                        rtmIndex = 1;
                    }
                });

                // Tambah RTM
                $('#add-rtm').on('click', function() {
                    const newRtmForm = `
                        @include('partials.rekomendasi-form', ['index' => '__INDEX__'])
                    `.replace(/__INDEX__/g, rtmIndex);

                    $('#rtm-container').append(newRtmForm);
                    rtmIndex++;
                });

                // Hapus RTM
                $(document).on('click', '.remove-rtm', function() {
                    $(this).closest('.rtm-form').remove();
                });
            });
        </script>
    @endpush
@endsection
