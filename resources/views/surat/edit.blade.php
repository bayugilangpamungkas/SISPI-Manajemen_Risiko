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
            box-shadow: 0 0 0 .2rem rgba(109,110,243,.15);
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
        .note-statusbar { display: none !important; }
        .isi-surat-invalid .note-editor.note-frame {
            border-color: #dc3545 !important;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('surat.show', $surat->id) }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Edit Surat</h1>
                        <small class="text-muted">Perbarui informasi surat</small>
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
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-600">Nomor Surat <span class="text-danger">*</span></label>
                                                <input type="text" name="nomor_surat"
                                                    class="form-control @error('nomor_surat') is-invalid @enderror"
                                                    value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                                                @error('nomor_surat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-600">Jenis Surat <span class="text-danger">*</span></label>
                                                <select name="jenis_surat" class="form-control selectric" required>
                                                    @foreach (['Pemberitahuan', 'Undangan', 'Permohonan', 'Lainnya'] as $jenis)
                                                        <option value="{{ $jenis }}"
                                                            {{ old('jenis_surat', $surat->jenis_surat) == $jenis ? 'selected' : '' }}>
                                                            {{ $jenis }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-600">Tanggal Surat <span class="text-danger">*</span></label>
                                                <input type="date" name="tanggal_surat" class="form-control"
                                                    value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-600">Tujuan Surat <span class="text-danger">*</span></label>
                                        <select name="tujuan_surat" id="tujuanSurat" class="select2" required>
                                            <option value=""></option>
                                            @foreach ($unitKerjas as $unit)
                                                <option value="{{ $unit->nama_unit_kerja }}"
                                                    {{ old('tujuan_surat', $surat->tujuan_surat) == $unit->nama_unit_kerja ? 'selected' : '' }}>
                                                    {{ $unit->nama_unit_kerja }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-600">Perihal <span class="text-danger">*</span></label>
                                        <input type="text" name="perihal" class="form-control"
                                            value="{{ old('perihal', $surat->perihal) }}" required>
                                    </div>

                                    <div class="form-group" id="wrapperIsiSurat">
                                        <label class="font-weight-600">
                                            Isi Surat <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="isiSurat" name="isi_surat">{{ old('isi_surat', $surat->isi_surat) }}</textarea>
                                        @error('isi_surat')
                                            <div class="text-danger mt-1" style="font-size: 0.875rem;">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Gunakan <kbd>Enter</kbd> untuk paragraf baru.
                                        </small>
                                    </div>

                                    <div class="bg-light p-4 rounded mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-link mr-2"></i>Referensi Surat (Opsional)</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tipe Referensi</label>
                                                    <select name="tipe_referensi" id="tipeReferensi" class="form-control">
                                                        <option value="Tanpa Referensi"
                                                            {{ old('tipe_referensi', $surat->tipe_referensi) == 'Tanpa Referensi' ? 'selected' : '' }}>
                                                            Tanpa Referensi</option>
                                                        <option value="Peta Risiko"
                                                            {{ old('tipe_referensi', $surat->tipe_referensi) == 'Peta Risiko' ? 'selected' : '' }}>
                                                            Peta Risiko</option>
                                                        <option value="Audit"
                                                            {{ old('tipe_referensi', $surat->tipe_referensi) == 'Audit' ? 'selected' : '' }}>
                                                            Audit / Manajemen Risiko</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div id="referensiPetaRisiko" class="form-group ref-field" style="display: none;">
                                                    <label>Pilih Peta Risiko</label>
                                                    <select name="referensi_id_peta" class="form-control select2" style="width:100%">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($petaRisikos as $peta)
                                                            <option value="{{ $peta->id }}"
                                                                {{ old('referensi_id', $surat->referensi_id) == $peta->id ? 'selected' : '' }}>
                                                                {{ $peta->kode_regist }} - {{ $peta->judul }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div id="referensiAudit" class="form-group ref-field" style="display: none;">
                                                    <label>Pilih Hasil Audit</label>
                                                    <select name="referensi_id_audit" class="form-control select2" style="width:100%">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($hasilAudits as $audit)
                                                            <option value="{{ $audit->id }}"
                                                                {{ old('referensi_id', $surat->referensi_id) == $audit->id ? 'selected' : '' }}>
                                                                {{ $audit->kode_risiko ?? '-' }} - {{ $audit->kegiatan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer bg-whitesmoke text-right">
                                    <a href="{{ route('surat.show', $surat->id) }}" class="btn btn-secondary mr-2">Batal</a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save mr-2"></i> Update Surat
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
        $(document).ready(function () {

            $('.select2').select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            function handleReferensi() {
                var val = $('#tipeReferensi').val();
                $('.ref-field').hide();
                if (val === 'Peta Risiko') $('#referensiPetaRisiko').fadeIn();
                if (val === 'Audit')       $('#referensiAudit').fadeIn();
            }
            handleReferensi();
            $('#tipeReferensi').on('change', handleReferensi);

            $('#isiSurat').summernote({
                lang: 'en-US',
                height: 320,
                minHeight: 320,
                maxHeight: null,
                toolbar: [
                    ['style',  ['bold', 'italic', 'underline', 'clear']],
                    ['font',   ['strikethrough']],
                    ['color',  ['color']],
                    ['para',   ['ul', 'ol', 'paragraph']],
                    ['table',  ['table']],
                    ['insert', ['link', 'picture']],
                    ['view',   ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function (contents) {
                        $('#isiSurat').val(contents);
                    }
                }
            });

            $('#formEditSurat').on('submit', function (e) {
                var konten = $('#isiSurat').summernote('code');
                var bersih = konten.replace(/<[^>]*>/g, '').trim();
                if (bersih === '') {
                    e.preventDefault();
                    $('#wrapperIsiSurat').addClass('isi-surat-invalid');
                    $('html, body').animate({ scrollTop: $('#wrapperIsiSurat').offset().top - 100 }, 400);
                    if ($('#isiSuratError').length === 0) {
                        $('#wrapperIsiSurat').append('<div id="isiSuratError" class="text-danger mt-1" style="font-size:0.875rem;"><i class="fas fa-exclamation-circle mr-1"></i>Isi surat tidak boleh kosong.</div>');
                    }
                } else {
                    $('#isiSurat').val(konten);
                    $('#wrapperIsiSurat').removeClass('isi-surat-invalid');
                    $('#isiSuratError').remove();
                }
            });

            $('#isiSurat').on('summernote.change', function () {
                $('#wrapperIsiSurat').removeClass('isi-surat-invalid');
                $('#isiSuratError').remove();
            });

        });
    </script>