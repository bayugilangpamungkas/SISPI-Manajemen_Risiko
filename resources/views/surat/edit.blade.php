@extends('layout.app')
@section('title', 'Edit Surat')

@push('style')
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <style>
        .note-editor.note-frame {
            border: 1px solid #e4e6fc;
            border-radius: .25rem;
        }

        .note-editor.note-frame.focus {
            border-color: #95a0f4;
            box-shadow: 0 0 0 .2rem rgba(109, 110, 243, .15);
        }

        .note-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e4e6fc;
        }

        .note-editable {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 13pt !important;
            line-height: 1.9 !important;
            min-height: 320px !important;
            text-align: justify;
            color: #000;
        }

        .note-placeholder {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 13pt !important;
            color: #aaa;
        }

        .note-statusbar {
            display: none !important;
        }

        .isi-surat-invalid .note-editor.note-frame {
            border-color: #dc3545 !important;
        }

        .select2-container {
            width: 100% !important;
            display: block;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #e4e6fc;
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            border-radius: .25rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #95a0f4;
        }

        .badge-field {
            font-size: 0.7rem;
            vertical-align: middle;
            margin-left: 4px;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('surat.show', $surat->id) }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size:1.3rem"></i>
                    </a>
                    <div>
                        <h1>Edit Surat</h1>
                        <small class="text-muted">Perbarui informasi surat — perubahan akan di-generate ulang ke PDF</small>
                    </div>
                </div>
            </div>

            <div class="section-body">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        <i class="fas fa-exclamation-triangle mr-2"></i> Terdapat kesalahan:
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <form id="formEditSurat" action="{{ route('surat.update', $surat->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card card-primary shadow-sm">

                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-envelope-open-text mr-2"></i>Informasi Surat
                                    </h4>
                                </div>

                                <div class="card-body">

                                    {{-- ══ Hidden fields: jenis_surat & tujuan_surat ══ --}}
                                    <input type="hidden" name="jenis_surat"
                                        value="{{ old('jenis_surat', $surat->jenis_surat ?? 'Lainnya') }}">
                                    <input type="hidden" name="tujuan_surat"
                                        value="{{ old('tujuan_surat', $surat->tujuan_surat ?? '-') }}">

                                    {{-- ══ BARIS 1: Nomor · Tanggal ══ --}}
                                    <div class="row">
                                        {{-- Nomor Surat --}}
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label class="font-weight-600">
                                                    Nomor Surat <span class="text-danger">*</span>
                                                    <span class="badge badge-info badge-field">tampil di PDF</span>
                                                </label>
                                                <input type="text" name="nomor_surat"
                                                    class="form-control @error('nomor_surat') is-invalid @enderror"
                                                    value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                                                @error('nomor_surat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Tanggal Surat --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-600">
                                                    Tanggal Surat <span class="text-danger">*</span>
                                                    <span class="badge badge-info badge-field">tampil di PDF</span>
                                                </label>
                                                <input type="date" name="tanggal_surat" class="form-control"
                                                    value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ══ BARIS 2: Hal (Perihal) · Lampiran ══ --}}
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="font-weight-600">
                                                    Hal (Perihal) <span class="text-danger">*</span>
                                                    <span class="badge badge-info badge-field">tampil di PDF</span>
                                                </label>
                                                <input type="text" name="perihal"
                                                    class="form-control @error('perihal') is-invalid @enderror"
                                                    value="{{ old('perihal', $surat->perihal) }}"
                                                    placeholder="Contoh: Permohonan Surat Tugas Audit Internal" required>
                                                @error('perihal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-600">
                                                    Lampiran
                                                    <span class="badge badge-info badge-field">tampil di PDF</span>
                                                </label>
                                                <input type="text" name="lampiran" class="form-control"
                                                    value="{{ old('lampiran', $surat->lampiran ?? '-') }}"
                                                    placeholder="Contoh: 1 Berkas, atau -">
                                                <small class="form-text text-muted">Isi <code>-</code> jika tidak ada
                                                    lampiran.</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ══ ISI SURAT — Summernote ══ --}}
                                    <div class="form-group mb-0" id="wrapperIsiSurat">
                                        <label class="font-weight-600">
                                            Isi Surat <span class="text-danger">*</span>
                                            <span class="badge badge-info badge-field">tampil di PDF</span>
                                        </label>
                                        <textarea id="isiSurat" name="isi_surat">{{ old('isi_surat', $surat->isi_surat) }}</textarea>
                                        @error('isi_surat')
                                            <div class="text-danger mt-1" style="font-size:.875rem;">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Tulis isi surat mulai dari salam pembuka hingga penutup.
                                            Tanda tangan Ketua SPI akan ditambahkan otomatis oleh sistem.
                                        </small>
                                    </div>

                                </div>{{-- /card-body --}}

                                <div class="card-footer bg-whitesmoke text-right">
                                    <a href="{{ route('surat.show', $surat->id) }}" class="btn btn-secondary mr-2">
                                        <i class="fas fa-times mr-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fas fa-save mr-2"></i> Update & Generate PDF
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            // ── SELECT2 untuk Tujuan Surat ─────────────────────────────
            $('#tujuanSurat').select2({
                placeholder: "-- Pilih Unit Kerja Tujuan --",
                allowClear: true,
                width: '100%',
            });

            // ── SUMMERNOTE ─────────────────────────────────────────────
            $('#isiSurat').summernote({
                lang: 'en-US',
                height: 360,
                minHeight: 360,
                maxHeight: null,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']],
                ],
                callbacks: {
                    onChange: function(contents) {
                        $('#isiSurat').val(contents);
                    }
                }
            });

            // ── VALIDASI SUBMIT ────────────────────────────────────────
            $('#formEditSurat').on('submit', function(e) {
                var konten = $('#isiSurat').summernote('code');
                var bersih = konten.replace(/<[^>]*>/g, '').trim();
                if (bersih === '') {
                    e.preventDefault();
                    $('#wrapperIsiSurat').addClass('isi-surat-invalid');
                    $('html, body').animate({
                        scrollTop: $('#wrapperIsiSurat').offset().top - 100
                    }, 400);
                    if ($('#isiSuratError').length === 0) {
                        $('#wrapperIsiSurat').append(
                            '<div id="isiSuratError" class="text-danger mt-1" style="font-size:.875rem;">' +
                            '<i class="fas fa-exclamation-circle mr-1"></i>Isi surat tidak boleh kosong.</div>'
                        );
                    }
                } else {
                    $('#isiSurat').val(konten);
                    $('#wrapperIsiSurat').removeClass('isi-surat-invalid');
                    $('#isiSuratError').remove();
                }
            });

            $('#isiSurat').on('summernote.change', function() {
                $('#wrapperIsiSurat').removeClass('isi-surat-invalid');
                $('#isiSuratError').remove();
            });
        });
    </script>
@endpush
