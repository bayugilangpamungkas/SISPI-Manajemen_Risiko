@extends('layout.app')
@section('title', 'Edit Surat')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('surat.index') }}" class="mr-3">
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
                        <i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow rounded">
                    <div class="card-body">
                        <form action="{{ route('surat.update', $surat->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nomor Surat <span
                                                class="text-danger">*</span></label>
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
                                        <label class="font-weight-bold">Jenis Surat <span
                                                class="text-danger">*</span></label>
                                        <select name="jenis_surat" class="form-control" required>
                                            <option value="Pemberitahuan"
                                                {{ old('jenis_surat', $surat->jenis_surat) == 'Pemberitahuan' ? 'selected' : '' }}>
                                                Pemberitahuan</option>
                                            <option value="Undangan"
                                                {{ old('jenis_surat', $surat->jenis_surat) == 'Undangan' ? 'selected' : '' }}>
                                                Undangan</option>
                                            <option value="Permohonan"
                                                {{ old('jenis_surat', $surat->jenis_surat) == 'Permohonan' ? 'selected' : '' }}>
                                                Permohonan</option>
                                            <option value="Lainnya"
                                                {{ old('jenis_surat', $surat->jenis_surat) == 'Lainnya' ? 'selected' : '' }}>
                                                Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tanggal Surat <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_surat" class="form-control"
                                            value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Tujuan Surat <span class="text-danger">*</span></label>
                                <select name="tujuan_surat" id="tujuanSurat" class="form-control select2" required>
                                    <option value="">-- Pilih Unit Kerja Tujuan --</option>
                                    @foreach ($unitKerjas as $unit)
                                        <option value="{{ $unit->nama_unit_kerja }}"
                                            {{ old('tujuan_surat', $surat->tujuan_surat) == $unit->nama_unit_kerja ? 'selected' : '' }}>
                                            {{ $unit->nama_unit_kerja }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Pilih Unit Kerja sebagai tujuan surat
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Perihal <span class="text-danger">*</span></label>
                                <input type="text" name="perihal" class="form-control"
                                    value="{{ old('perihal', $surat->perihal) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Isi Surat <span class="text-danger">*</span></label>
                                <textarea name="isi_surat" class="form-control" rows="10" required>{{ old('isi_surat', $surat->isi_surat) }}</textarea>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3"><i class="fas fa-link"></i> Referensi Surat (Opsional)</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tipe Referensi</label>
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
                                    <div class="form-group" id="referensiPetaRisiko"
                                        style="display: {{ old('tipe_referensi', $surat->tipe_referensi) == 'Peta Risiko' ? 'block' : 'none' }};">
                                        <label class="font-weight-bold">Pilih Peta Risiko</label>
                                        <select name="referensi_id_peta" class="form-control">
                                            <option value="">-- Pilih Peta Risiko --</option>
                                            @foreach ($petaRisikos as $peta)
                                                <option value="{{ $peta->id }}"
                                                    {{ old('referensi_id', $surat->referensi_id) == $peta->id ? 'selected' : '' }}>
                                                    {{ $peta->kode_regist }} - {{ $peta->judul }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="referensiAudit"
                                        style="display: {{ old('tipe_referensi', $surat->tipe_referensi) == 'Audit' ? 'block' : 'none' }};">
                                        <label class="font-weight-bold">Pilih Hasil Audit</label>
                                        <select name="referensi_id_audit" class="form-control">
                                            <option value="">-- Pilih Hasil Audit --</option>
                                            @foreach ($hasilAudits as $audit)
                                                <option value="{{ $audit->id }}"
                                                    {{ old('referensi_id', $surat->referensi_id) == $audit->id ? 'selected' : '' }}>
                                                    {{ $audit->kode_risiko }} - {{ $audit->kegiatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Surat
                                </button>
                                <a href="{{ route('surat.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* 1. PAKSA DROPDOWN SELALU DI BAWAH SECARA VISUAL */
        .select2-container--open .select2-dropdown--above {
            border-top: 1px solid #aaa !important;
            border-bottom: 1px solid #aaa !important;
            border-radius: 4px !important;
        }

        /* Memastikan container dropdown memiliki ruang yang cukup */
        .select2-container {
            z-index: 9999 !important;
        }

        /* Hilangkan efek rounded bawah pada box input saat dropdown terbuka di bawah */
        .select2-container--open .select2-selection {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            // 1. Inisialisasi Select2 dengan penempatan body
            $('#tujuan_surat').select2({
                width: '100%',
                dropdownParent: $('body')
            });

            // 2. LOGIKA PAKSA KE BAWAH (Override default Select2)
            $('#tujuan_surat').on('select2:open', function(e) {
                // Ambil elemen dropdown yang baru saja dibuka
                const dropdown = $('.select2-dropdown');
                const container = $('.select2-container');

                // Paksa hapus class 'above' dan ganti ke 'below'
                dropdown.removeClass('select2-dropdown--above').addClass('select2-dropdown--below');
                container.removeClass('select2-container--above').addClass('select2-container--below');

                // Hitung ulang posisi agar menempel tepat di bawah box input
                const selection = $(this).next('.select2-container');
                const offset = selection.offset();

                $('.select2-dropdown').css({
                    top: (offset.top + selection.outerHeight()) + 'px',
                    left: offset.left + 'px'
                });
            });

            // 3. Logika Show/Hide Referensi
            function handleReferensi() {
                const tipe = $('#tipeReferensi').val();
                $('#referensiPetaRisiko').hide();
                $('#referensiAudit').hide();

                if (tipe === 'Peta Risiko') {
                    $('#referensiPetaRisiko').fadeIn();
                } else if (tipe === 'Audit') {
                    $('#referensiAudit').fadeIn();
                }
            }

            handleReferensi();
            $('#tipeReferensi').on('change', function() {
                handleReferensi();
            });
        });
    </script>
@endpush
