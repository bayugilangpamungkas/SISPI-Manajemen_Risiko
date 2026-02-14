@extends('layout.app')
@section('title', 'Buat Surat Baru')

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('surat.index') }}" class="btn btn-icon btn-light mr-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1>Buat Surat Baru</h1>
                        <div class="section-header-breadcrumb d-none d-sm-block">
                            <div class="breadcrumb-item active"><a href="#">Surat</a></div>
                            <div class="breadcrumb-item">Tambah Baru</div>
                        </div>
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
                        <form action="{{ route('surat.store') }}" method="POST">
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
                                                    @foreach (['Pemberitahuan', 'Undangan', 'Permohonan', 'Lainnya'] as $jenis)
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

                                    <div class="form-group">
                                        <label class="font-weight-600">Isi Surat <span class="text-danger">*</span></label>
                                        <textarea name="isi_surat" class="form-control" rows="8" placeholder="Tulis pesan surat secara lengkap..."
                                            style="min-height: 200px" required>{{ old('isi_surat') }}</textarea>
                                    </div>

                                    {{-- REFERENSI (OPSIONAL) --}}
                                    {{-- <div class="bg-light p-4 rounded mt-4">
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
                                                            <option value="{{ $audit->id }}">{{ $audit->kode_risiko }}
                                                                - {{ $audit->kegiatan }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
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
    <style>
        /* Menghilangkan elemen select asli agar tidak memakan ruang */
        select.select2 {
            display: none;
        }

        /* Memperbaiki ukuran container Select2 agar pas dengan form-control */
        .select2-container {
            width: 100% !important;
            display: block;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #e4e6fc;
            /* Warna border default Stisla */
            height: calc(2.25rem + 2px);
            /* Tinggi standar form-control Bootstrap */
            padding: .375rem .75rem;
            border-radius: .25rem;
        }

        Menghilangkan border biru double saat fokus .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #95a0f4;
        }

        /* Menghilangkan container search secara paksa lewat CSS */
        .select2-search--dropdown {
            display: none !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Init Select2 Tanpa Search Bar
            $('.select2').select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: '100%',
                // Ini kuncinya untuk menghilangkan kotak input search
                minimumResultsForSearch: Infinity
            });

            // Toggle logic tetap sama
            $('#tipeReferensi').on('change', function() {
                const val = $(this).val();
                $('.ref-field').hide();
                if (val === 'Peta Risiko') $('#referensiPetaRisiko').fadeIn();
                if (val === 'Audit') $('#referensiAudit').fadeIn();
            }).trigger('change');
        });
    </script>
@endpush
