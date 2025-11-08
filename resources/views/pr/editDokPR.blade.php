@extends('layout.app')
@section('title', 'Tambah Dokumen')

@section('main')
<div class="main-content">
 <section class="section">
    <div class="section-header d-flex align-items-center">
        <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
        <h1>Edit Dokumen</h1>
    </div>
    <div class="section-body">
        <div class="row">
            {{-- bagian kiri --}}
            <div class="col-md-12">
                <div class="card border-0 shadow rounded">
                    <div class="card-body">
                        <form action="{{ route('petas.update', $petas->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="font-weight-bold">JUDUL KEGIATAN</label>
                                {{-- <select class="form-control select2 @error('id_kegiatan') is-invalid @enderror" name="id_kegiatan">
                                    <option value="" disabled selected>Pilih Kegiatan</option>
                                    @foreach($kegiatan as $kegiatan)
                                        <option value="{{ $kegiatan->id }}" {{ $petas->id_kegiatan == $kegiatan->id ? 'selected' : '' }}>{{ $kegiatan->judul }}</option>
                                    @endforeach
                                    </select> --}}
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" name="judul"
                                    value="{{ $petas->judul }}" placeholder="Masukkan Judul Kegiatan...">

                                <!-- error message untuk judul -->
                                @error('judul')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">ANGGARAN</label>
                                {{-- <select class="form-control select2 @error('id_kegiatan') is-invalid @enderror" name="id_kegiatan">
                                    <option value="" disabled selected>Pilih Kegiatan</option>
                                    @foreach($kegiatan as $kegiatan)
                                        <option value="{{ $kegiatan->id }}" {{ $petas->id_kegiatan == $kegiatan->id ? 'selected' : '' }}>{{ $kegiatan->judul }}</option>
                                    @endforeach
                                    </select> --}}
                                <input type="number" class="form-control @error('anggaran') is-invalid @enderror" name="anggaran"
                                    value="{{ $petas->anggaran }}" placeholder="Masukkan Anggaran Kegiatan...">

                                <!-- error message untuk anggaran -->
                                @error('anggaran')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">UNIT KERJA</label></br>
                                <select name="jenis" id="unit_kerja" class="form-control">
                                    <option value="" disabled selected>Pilih Unit Kerja</option>
                                    @foreach($unitKerjas as $unit)
                                        <option value="{{ $unit->nama_unit_kerja }}" {{ $petas->jenis == $unit->nama_unit_kerja ? 'selected' : '' }}>{{ $unit->nama_unit_kerja }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">PERNYATAAN RISIKO</label>
                                <textarea class="form-control @error('pernyataan') is-invalid @enderror" name="pernyataan"
                                    placeholder="Masukkan Pernyataan Risiko..." rows="3">{{ $petas->pernyataan }}</textarea>

                                <!-- error message untuk judul -->
                                @error('pernyataan')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">KATEGORI RISIKO</label>
                                <select class="form-control @error('kategori') is-invalid @enderror"
                                    name="kategori">
                                    <option value="" disabled>Pilih Kategori</option>
                                    <option value="Risiko Strategis" {{ $petas->kategori == "Risiko Strategis" ? 'selected' : '' }}>1. Risiko Strategis</option>
                                    <option value="Risiko Operasional" {{ $petas->kategori == "Risiko Operasional" ? 'selected' : '' }}>2. Risiko Operasional</option>
                                    <option value="Risiko Keuangan" {{ $petas->kategori == "Risiko Keuangan" ? 'selected' : '' }}>3. Risiko Keuangan</option>
                                    <option value="Risiko Kepatuhan" {{ $petas->kategori == "Risiko Kepatuhan" ? 'selected' : '' }}>4. Risiko Kepatuhan</option>
                                    <option value="Risiko Kecurangan" {{ $petas->kategori == "Risiko Kecurangan" ? 'selected' : '' }}>5. Risiko Kecurangan</option>
                                </select>

                                <!-- error message untuk judul -->
                                @error('kategori')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">URAIAN DAMPAK</label>
                                <textarea class="form-control @error('uraian') is-invalid @enderror" name="uraian"
                                    placeholder="Masukan Uraian..." rows="3">{{ $petas->uraian }}</textarea>

                                <!-- error message untuk judul -->
                                @error('uraian')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">METODE PENCAPAIAN</label>
                                {{-- <select class="form-control @error('metode') is-invalid @enderror"
                                    name="metode">
                                    <option value="" disabled>Pilih Metode</option>
                                    <option value="1. Memberikan keyakinan yang memadai bagi tercapainya efektivitas dan efisiensi pencapaian tujuan penyelenggaraan pemerintahan negara" {{ $petas->metode == "1. Memberikan keyakinan yang memadai bagi tercapainya efektivitas dan efisiensi pencapaian tujuan penyelenggaraan pemerintahan negara" ? 'selected' : '' }}>1. Memberikan keyakinan yang memadai bagi tercapainya efektivitas dan efisiensi pencapaian tujuan penyelenggaraan pemerintahan negara</option>
                                    <option value="2. Keandalan pelaporan keuangan" {{ $petas->metode == "2. Keandalan pelaporan keuangan" ? 'selected' : '' }}>2. Keandalan pelaporan keuangan</option>
                                    <option value="3. Pengamanan aset negara" {{ $petas->metode == "3. Pengamanan aset negara" ? 'selected' : '' }}>3. Pengamanan aset negara</option>
                                    <option value="4. Ketaatan terhadap peraturan perundang-undangan" {{ $petas->metode == "4. Ketaatan terhadap peraturan perundang-undangan" ? 'selected' : '' }}>4. Ketaatan terhadap peraturan perundang-undangan</option>
                                </select> --}}
                                <textarea class="form-control @error('uraian') is-invalid @enderror" name="metode"
                                    placeholder="Masukan Metode Pencapaian..." rows="3">{{ $petas->metode }}</textarea>

                                <!-- error message untuk judul -->
                                @error('metode')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">SKOR PROBABILITAS</label>
                                <select class="form-control @error('skor_kemungkinan') is-invalid @enderror"
                                    name="skor_kemungkinan">
                                    <option value="">Pilih Skor</option>
                                    <option value="1" {{ $petas->skor_kemungkinan == 1 ? 'selected' : '' }}>1. Sangat Jarang</option>
                                    <option value="2" {{ $petas->skor_kemungkinan == 2 ? 'selected' : '' }}>2. Jarang</option>
                                    <option value="3" {{ $petas->skor_kemungkinan == 3 ? 'selected' : '' }}>3. Kadang-kadang</option>
                                    <option value="4" {{ $petas->skor_kemungkinan == 4 ? 'selected' : '' }}>4. Sering</option>
                                    <option value="5" {{ $petas->skor_kemungkinan == 5 ? 'selected' : '' }}>5. Sangat Sering</option>
                                </select>

                                <!-- error message untuk judul -->
                                @error('skor_kemungkinan')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">SKOR DAMPAK</label>
                                <select class="form-control @error('skor_dampak') is-invalid @enderror"
                                    name="skor_dampak">
                                    <option value="" disabled>Pilih Skor</option>
                                    <option value="1" {{ $petas->skor_dampak == 1 ? 'selected' : '' }}>1. Sangat Sedikit Berpengaruh</option>
                                    <option value="2" {{ $petas->skor_dampak == 2 ? 'selected' : '' }}>2. Sedikit Berpengaruh</option>
                                    <option value="3" {{ $petas->skor_dampak == 3 ? 'selected' : '' }}>3. Cukup Berpengaruh</option>
                                    <option value="4" {{ $petas->skor_dampak == 4 ? 'selected' : '' }}>4. Berpengaruh</option>
                                    <option value="5" {{ $petas->skor_dampak == 5 ? 'selected' : '' }}>5. Sangat Berpengaruh</option>
                                </select>

                                <!-- error message untuk judul -->
                                @error('skor_dampak')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-md btn-primary">SIMPAN</button>
                            <button type="reset" class="btn btn-md btn-warning">RESET</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    @endpush
@endsection
