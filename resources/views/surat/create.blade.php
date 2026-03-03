@extends('layout.app')
@section('title', 'Buat Surat Baru')

@push('style')
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <style>
        /* ── Wrapper Summernote: border & radius konsisten dengan form lain ── */
        .note-editor.note-frame {
            border: 1px solid #e4e6fc;
            border-radius: .25rem;
        }

        .note-editor.note-frame.focus {
            border-color: #95a0f4;
            box-shadow: 0 0 0 .2rem rgba(109, 110, 243, .15);
        }

        /* ── Toolbar: font standar surat resmi ── */
        .note-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e4e6fc;
        }

        /* ── Area tulis: font Times New Roman, tinggi nyaman ── */
        .note-editable {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 13pt !important;
            line-height: 1.9 !important;
            min-height: 320px !important;
            text-align: justify;
            color: #000;
        }

        /* ── Placeholder teks ── */
        .note-placeholder {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 13pt !important;
            color: #aaa;
        }

        /* ── Sembunyikan status bar bawah (tidak diperlukan) ── */
        .note-statusbar {
            display: none !important;
        }

        /* ── Validasi: border merah jika kosong saat submit ── */
        .isi-surat-invalid .note-editor.note-frame {
            border-color: #dc3545 !important;
        }

        /* Select2 */
        select.select2 {
            display: none;
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

        .select2-search--dropdown {
            display: none !important;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('surat.index') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Buat Surat Baru</h1>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- VALIDATION ALERTS --}}
                @if (session('error') || $errors->any())
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert"><span>&times;</span></button>
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            @if (session('error'))
                                {{ session('error') }}
                            @else
                                Mohon periksa kembali inputan Anda.
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <form id="formSurat" action="{{ route('surat.store') }}" method="POST">
                            @csrf
                            <div class="card card-primary shadow-sm">
                                <div class="card-body">

                                    {{-- INFORMASI UTAMA --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-600">Nomor Surat <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="nomor_surat"
                                                    class="form-control @error('nomor_surat') is-invalid @enderror"
                                                    value="{{ old('nomor_surat') }}"
                                                    placeholder="000/SPI/{{ date('m/Y') }}" required>
                                                @error('nomor_surat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-600">Jenis Surat <span
                                                        class="text-danger">*</span></label>
                                                <select name="jenis_surat"
                                                    class="form-control selectric @error('jenis_surat') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih --</option>
                                                    @foreach (['Surat Tugas', 'Surat Pemberitahuan Audit', 'Surat Permintaan Data', 'Nota Dinas', 'Undangan', 'Laporan Hasil Audit', 'Berita Acara', 'Permohonan', 'Lainnya'] as $jenis)
                                                        <option value="{{ $jenis }}"
                                                            {{ old('jenis_surat') == $jenis ? 'selected' : '' }}>
                                                            {{ $jenis }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-600">Tanggal Surat <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="tanggal_surat" class="form-control"
                                                    value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-600">Tujuan Surat <span
                                                class="text-danger">*</span></label>
                                        <select name="tujuan_surat" id="tujuanSurat" class="select2" required>
                                            <option value=""></option>
                                            @foreach ($unitKerjas as $unit)
                                                <option value="{{ $unit->nama_unit_kerja }}"
                                                    {{ old('tujuan_surat') == $unit->nama_unit_kerja ? 'selected' : '' }}>
                                                    {{ $unit->nama_unit_kerja }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-600">Perihal <span class="text-danger">*</span></label>
                                        <input type="text" name="perihal" class="form-control"
                                            value="{{ old('perihal') }}" placeholder="Tuliskan perihal surat" required>
                                    </div>

                                    {{-- ═══════════════════════════════════════════
                                         ISI SURAT — Summernote WYSIWYG Editor
                                    ════════════════════════════════════════════ --}}
                                    <div class="form-group" id="wrapperIsiSurat">
                                        <label class="font-weight-600">
                                            Isi Surat <span class="text-black">*</span>
                                        </label>

                                        {{-- Textarea hidden — nilai dikirim ke server --}}
                                        <textarea id="isiSurat" name="isi_surat">{{ old('isi_surat') }}</textarea>

                                        @error('isi_surat')
                                            <div class="text-danger mt-1" style="font-size: 0.875rem;">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </div>
                                        @enderror

                                        {{-- <small class="form-text text-muted mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Gunakan <kbd>Enter</kbd> untuk paragraf baru.
                                            Paragraf akan tampil terpisah pada hasil cetak PDF.
                                        </small> --}}
                                    </div>

                                    {{-- REFERENSI (OPSIONAL) --}}
                                    <div class="bg-light p-4 rounded mt-4">
                                        <h6 class="text-primary mb-3"><i class="fas fa-link mr-2"></i>Referensi Surat
                                            (Opsional)</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tipe Referensi</label>
                                                    <select name="tipe_referensi" id="tipeReferensi" class="form-control">
                                                        <option value="Tanpa Referensi">Tanpa Referensi</option>
                                                        <option value="Peta Risiko"
                                                            {{ old('tipe_referensi') == 'Peta Risiko' ? 'selected' : '' }}>
                                                            Peta Risiko</option>
                                                        <option value="Audit"
                                                            {{ old('tipe_referensi') == 'Audit' ? 'selected' : '' }}>Audit
                                                            / Manajemen Risiko</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div id="referensiPetaRisiko" class="form-group ref-field"
                                                    style="display: none;">
                                                    <label>Pilih Peta Risiko</label>
                                                    <select name="referensi_id_peta" class="form-control select2"
                                                        style="width: 100%">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($petaRisikos as $peta)
                                                            <option value="{{ $peta->id }}">{{ $peta->kode_regist }} -
                                                                {{ $peta->judul }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div id="referensiAudit" class="form-group ref-field"
                                                    style="display: none;">
                                                    <label>Pilih Hasil Audit</label>
                                                    <select name="referensi_id_audit" class="form-control select2"
                                                        style="width: 100%">
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($hasilAudits as $audit)
                                                            <option value="{{ $audit->id }}">
                                                                {{ $audit->kode_risiko ?? 'Audit #' . $audit->id }}
                                                                @if ($audit->kegiatan)
                                                                    - {{ $audit->kegiatan }}
                                                                @endif
                                                                @if ($audit->unit_kerja)
                                                                    ({{ $audit->unit_kerja }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer bg-whitesmoke text-right">
                                    <a href="{{ route('surat.index') }}" class="btn btn-secondary mr-2">Batal</a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-save mr-2"></i> Simpan Surat
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

            // ── 1. SELECT2 ──────────────────────────────────────────────
            $('.select2').select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            // ── 2. TOGGLE REFERENSI ──────────────────────────────────────
            $('#tipeReferensi').on('change', function() {
                const val = $(this).val();
                $('.ref-field').hide();
                if (val === 'Peta Risiko') $('#referensiPetaRisiko').fadeIn();
                if (val === 'Audit') $('#referensiAudit').fadeIn();
            }).trigger('change');

            // ── 3. SUMMERNOTE INIT ───────────────────────────────────────
            $('#isiSurat').summernote({
                lang: 'en-US',
                height: 320,
                minHeight: 320,
                maxHeight: null,
                placeholder: 'Contoh:\nDengan hormat,\n\nSehubungan dengan pelaksanaan audit internal...\n\nDemikian surat ini kami sampaikan.',

                // Toolbar: hanya fitur yang relevan untuk surat resmi
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],

                // Callback: sinkronkan ke textarea saat konten berubah
                callbacks: {
                    onChange: function(contents) {
                        $('#isiSurat').val(contents);
                    }
                }
            });

            // ── 4. VALIDASI SEBELUM SUBMIT ───────────────────────────────
            $('#formSurat').on('submit', function(e) {
                const konten = $('#isiSurat').summernote('code');
                const bersih = konten.replace(/<[^>]*>/g, '').trim(); // strip HTML tags

                if (bersih === '') {
                    e.preventDefault();
                    $('#wrapperIsiSurat').addClass('isi-surat-invalid');
                    // Scroll ke field isi surat
                    $('html, body').animate({
                        scrollTop: $('#wrapperIsiSurat').offset().top - 100
                    }, 400);
                    // Tampilkan pesan error sementara
                    if ($('#isiSuratError').length === 0) {
                        $('#wrapperIsiSurat').append(
                            '<div id="isiSuratError" class="text-danger mt-1" style="font-size:0.875rem;">' +
                            '<i class="fas fa-exclamation-circle mr-1"></i>Isi surat tidak boleh kosong.</div>'
                        );
                    }
                } else {
                    // Pastikan textarea terisi sebelum form submit
                    $('#isiSurat').val(konten);
                    $('#wrapperIsiSurat').removeClass('isi-surat-invalid');
                    $('#isiSuratError').remove();
                }
            });

            // ── 5. HAPUS ERROR SAAT USER MULAI MENGETIK ─────────────────
            $('#isiSurat').on('summernote.change', function() {
                $('#wrapperIsiSurat').removeClass('isi-surat-invalid');
                $('#isiSuratError').remove();
            });

        });
    </script>
@endpush
