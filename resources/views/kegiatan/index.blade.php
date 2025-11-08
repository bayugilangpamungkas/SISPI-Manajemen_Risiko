@extends('layout.app')
@section('title', 'Master Kegiatan')
@section('main')
    <style>
        .select2-container--default .select2-search--dropdown .select2-search__field {
            display: block !important;
            width: 100% !important;
            padding: 6px !important;
        }

        .select2-dropdown {
            z-index: 99999;
        }

        .select2-search__field:focus {
            outline: none;
        }
    </style>

    <div class="modal fade" id="uploadModal" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kegiatan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">
                            Tambah Kegiatan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">UNIT KERJA</label>
                            <select class="select2 form-control @error('id_unit_kerja') is-invalid @enderror"
                                name="id_unit_kerja" data-placeholder="Pilih Unit Kerja">
                                <option></option> <!-- Penting: Tambahkan empty option untuk placeholder -->
                                @foreach ($unitKerjas as $unitKerja)
                                    <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama_unit_kerja }}</option>
                                @endforeach
                            </select>
                            <!-- error message untuk judul -->
                            @error('id_unit_kerja')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">JUDUL KEGIATAN</label>
                            <input type="text" class="form-control" name="judul" placeholder="Masukkan Judul Kegiatan">

                            <!-- error message untuk judul -->
                            @error('judul')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">IKU</label>
                            <input type="text" class="form-control @error('iku') is-invalid @enderror" name="iku"
                                value="{{ old('iku') }}" placeholder="Masukkan IKU...">

                            <!-- error message untuk judul -->
                            @error('iku')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">SASARAN STRATEGIS</label>
                            <select class="form-control @error('sasaran') is-invalid @enderror" name="sasaran">
                                <option value="" disabled selected>Pilih Sasaran</option>
                                <option value="1. Meningkatnya kualitas lulusan pendidikan tinggi"
                                    {{ old('kategori') == 1 ? 'selected' : '' }}>1. Meningkatnya kualitas lulusan pendidikan
                                    tinggi</option>
                                <option value="2. Meningkatnya kualitas dosen pendidikan tinggi"
                                    {{ old('kategori') == 2 ? 'selected' : '' }}>2. Meningkatnya kualitas dosen pendidikan
                                    tinggi</option>
                                <option value="3. Meningkatnya kualitas kurikulum dan pembelajaran"
                                    {{ old('kategori') == 3 ? 'selected' : '' }}>3. Meningkatnya kualitas kurikulum dan
                                    pembelajaran</option>
                                <option
                                    value="4. Meningkatnya tata kelola satuan kerja di lingkungan Ditjen Pendidikan Vokasi"
                                    {{ old('kategori') == 4 ? 'selected' : '' }}>4. Meningkatnya tata kelola satuan kerja
                                    di
                                    lingkungan Ditjen Pendidikan Vokasi</option>
                            </select>
                            <!-- error message untuk judul -->
                            @error('sasaran')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">PROGRAM KERJA</label>
                            <input type="text" class="form-control @error('proker') is-invalid @enderror" name="proker"
                                value="{{ old('proker') }}" placeholder="Masukkan Program Kerja...">

                            <!-- error message untuk judul -->
                            @error('proker')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">INDIKATOR</label>
                            <input type="text" class="form-control @error('indikator') is-invalid @enderror"
                                name="indikator" value="{{ old('indikator') }}" placeholder="Masukkan Indikator...">

                            <!-- error message untuk judul -->
                            @error('indikator')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">ANGGARAN</label>
                            <input type="text" class="form-control @error('anggaran') is-invalid @enderror"
                                name="anggaran" value="{{ old('anggaran') }}" placeholder="Masukkan Anggaran...">

                            <!-- error message untuk judul -->
                            @error('anggaran')
                                <div class="alert alert-danger mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Year Modal -->
    <div class="modal fade" id="deleteYearModal" tabindex="-1" aria-labelledby="deleteYearModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kegiatan.deleteByYear') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteYearModalLabel">Hapus Data Kegiatan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="year" class="form-label">Pilih Tahun</label>
                            <input type="number" class="form-control" id="year" name="year" required
                                min="2000" max="{{ date('Y') + 1 }}" value="{{ date('Y') }}">
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Perhatian: Tindakan ini akan menghapus semua data kegiatan pada tahun yang dipilih dan tidak
                            dapat dibatalkan.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="confirmDelete">Hapus Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kegiatan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Kegiatan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih file Excel</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <a href="{{ route('kegiatan.template') }}" class="btn btn-success">Download Template</a> --}}
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($kegiatans as $kegiatan)
        <div class="modal fade" id="editModal{{ $kegiatan->id }}" tabindex="-1"
            aria-labelledby="editModal{{ $kegiatan->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('kegiatan.update', $kegiatan->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModal{{ $kegiatan->id }}">
                                Edit Kegiatan - {{ $kegiatan->judul }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">UNIT KERJA</label>
                                <select class="select2 form-control @error('id_unit_kerja') is-invalid @enderror"
                                    name="id_unit_kerja" data-placeholder="Pilih Unit Kerja">
                                    @foreach ($unitKerjas as $unitKerja)
                                        <option value="{{ $unitKerja->id }}"
                                            {{ $kegiatan->id_unit_kerja == $unitKerja->id ? 'selected' : '' }}>
                                            {{ $unitKerja->nama_unit_kerja }}</option>
                                    @endforeach
                                </select>
                                <!-- error message untuk judul -->
                                @error('id_unit_kerja')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">JUDUL KEGIATAN</label>
                                <input type="text" class="form-control" name="judul"
                                    placeholder="Masukkan Judul Kegiatan" value="{{ $kegiatan->judul }}">

                                <!-- error message untuk judul -->
                                @error('judul')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">IKU</label>
                                <input type="text" class="form-control @error('iku') is-invalid @enderror"
                                    name="iku" value="{{ $kegiatan->iku }}" placeholder="Masukkan IKU...">

                                <!-- error message untuk judul -->
                                @error('iku')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">SASARAN STRATEGIS</label>
                                <select class="form-control @error('sasaran') is-invalid @enderror" name="sasaran">
                                    <option value="" disabled>Pilih Sasaran</option>
                                    <option value="1. Meningkatnya kualitas lulusan pendidikan tinggi"
                                        {{ $kegiatan->sasaran == '1. Meningkatnya kualitas lulusan pendidikan tinggi' ? 'selected' : '' }}>
                                        1. Meningkatnya kualitas lulusan pendidikan
                                        tinggi</option>
                                    <option value="2. Meningkatnya kualitas dosen pendidikan tinggi"
                                        {{ $kegiatan->sasaran == '2. Meningkatnya kualitas dosen pendidikan' ? 'selected' : '' }}>
                                        2. Meningkatnya kualitas dosen pendidikan
                                        tinggi</option>
                                    <option value="3. Meningkatnya kualitas kurikulum dan pembelajaran"
                                        {{ $kegiatan->sasaran == '3. Meningkatnya kualitas kurikulum dan pembelajaran' ? 'selected' : '' }}>
                                        3. Meningkatnya kualitas kurikulum dan
                                        pembelajaran</option>
                                    <option
                                        value="4. Meningkatnya tata kelola satuan kerja di lingkungan Ditjen Pendidikan Vokasi"
                                        {{ $kegiatan->sasaran == '4. Meningkatnya tata kelola satuan kerja di lingkungan Ditjen Pendidikan Vokasi' ? 'selected' : '' }}>
                                        4. Meningkatnya tata kelola satuan kerja di
                                        lingkungan Ditjen Pendidikan Vokasi</option>
                                </select>
                                <!-- error message untuk judul -->
                                @error('sasaran')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">PROGRAM KERJA</label>
                                <input type="text" class="form-control @error('proker') is-invalid @enderror"
                                    name="proker" value="{{ $kegiatan->proker }}"
                                    placeholder="Masukkan Program Kerja...">

                                <!-- error message untuk judul -->
                                @error('proker')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">INDIKATOR</label>
                                <input type="text" class="form-control @error('indikator') is-invalid @enderror"
                                    name="indikator" value="{{ $kegiatan->indikator }}"
                                    placeholder="Masukkan Indikator...">

                                <!-- error message untuk judul -->
                                @error('indikator')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">ANGGARAN</label>
                                <input type="text" class="form-control @error('anggaran') is-invalid @enderror"
                                    name="anggaran" value="{{ $kegiatan->anggaran }}"
                                    placeholder="Masukkan Anggaran...">

                                <!-- error message untuk judul -->
                                @error('anggaran')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left"
                        style="font-size: 1.3rem"></i></a>
                <h1>Master Kegiatan</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">List</span>
                    <span class="span1">Kegiatan</span>
                </h4>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <form action="{{ route('kegiatan.index') }}" method="GET" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="search" name="search" class="form-control"
                                    placeholder="Search: Masukkan Judul" value="{{ request('search') }}">
                            </div>

                            <div class="input-group mr-2">
                                <select name="year" class="form-control">
                                    <option value="">Pilih Tahun</option>
                                    @php
                                        $currentYear = date('Y');
                                        $startYear = 2020; // Sesuaikan dengan tahun awal data
                                    @endphp
                                    @for ($year = $currentYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>

                            @if (request('search') || request('year'))
                                <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary ml-2">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                    <a href="{{ route('posts.index') }}" class="btn btn-md btn-success mb-3"
                                        data-toggle="modal" data-target="#uploadModal"
                                        style="font-size: 0.85rem !important;">TAMBAH KEGIATAN</a>
                                    <button type="button" class="btn btn-md btn-success mb-3 ml-2" data-toggle="modal"
                                        data-target="#importModal">
                                        IMPORT DATA DARI EXCEL
                                    </button>
                                    <button type="button" class="btn btn-danger mb-3 ml-2" data-toggle="modal"
                                        data-target="#deleteYearModal">
                                        Hapus Data Berdasarkan Tahun
                                    </button>
                                    @endif
                                    <button id="exportExcelButton" class="btn btn-success mb-3 float-right">
                                        <i class="fas fa-file-excel"></i> Export to Excel
                                    </button>
                                <table class="table table-bordered table-responsive" id="tableKegiatan">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">No</th>
                                            <th scope="col">Judul</th>
                                            @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                <th scope="col">Unit Kerja</th>
                                            @endif
                                            <th scope="col">IKU</th>
                                            <th scope="col">Sasaran</th>
                                            <th scope="col">Proker</th>
                                            <th scope="col">Indikator</th>
                                            <th scope="col">Anggaran</th>
                                            <th scope="col">Tanggal Dibuat</th>
                                            @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                <th colspan="2" scope="col">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = ($kegiatans->currentPage() - 1) * $kegiatans->perPage() + 1; @endphp
                                        @forelse ($kegiatans as $kegiatan)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $no++ }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $kegiatan->judul }}
                                                </td>
                                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                    <td class="text-center">
                                                        {{ $kegiatan->unitKerja->nama_unit_kerja }}
                                                    </td>
                                                @endif
                                                <td class="text">
                                                    {{ $kegiatan->iku }}
                                                </td>
                                                <td class="text">
                                                    {{ $kegiatan->sasaran }}
                                                </td>
                                                <td class="text">
                                                    {{ $kegiatan->proker }}
                                                </td>
                                                <td class="text">
                                                    {{ $kegiatan->indikator }}
                                                </td>
                                                <td class="text">
                                                    {{ $kegiatan->anggaran }}
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($kegiatan['updated_at'])->format('d F Y') }}
                                                </td>
                                                @if (auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                    <td><button class="btn btn-warning fa-solid fa-pencil p-2"
                                                            data-toggle="modal"
                                                            data-target="#editModal{{ $kegiatan->id }}"></button></td>
                                                    <td>
                                                        <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                            action="{{ route('kegiatan.destroy', $kegiatan) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <!-- <button type="submit" class="fa-solid fa-trash bg-danger p-2 text white"></button> -->
                                                            <button type="submit"
                                                                class="btn fa-solid fa-trash bg-danger p-2 text-white"
                                                                data-toggle="tooltip" title="Hapus Kegiatan"></button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <div class="alert alert-danger">
                                                Data Kegiatan belum Tersedia.
                                            </div>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- PAGINATION (Hilangi -- nya)-->
                                {{ $kegiatans->links('pagination::bootstrap-4') }}

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

            <script>
                //message with toastr
                @if (session()->has('success'))

                    toastr.success('{{ session('success') }}', 'BERHASIL!');
                @elseif (session()->has('error'))

                    toastr.error('{{ session('error') }}', 'GAGAL!');
                @endif
            </script>
            <script>
                document.getElementById('confirmDelete').addEventListener('click', function(e) {
                    e.preventDefault();
                    const year = document.getElementById('year').value;

                    if (confirm(`Anda yakin ingin menghapus semua data kegiatan tahun ${year}?`)) {
                        this.closest('form').submit();
                    }
                });
            </script>
            <script>
                function exportTableToExcel(tableId, filename = 'Master Kegiatan.xlsx') {
                    var wb = XLSX.utils.book_new();
                    var ws = XLSX.utils.table_to_sheet(document.getElementById(tableId));
                    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                    XLSX.writeFile(wb, filename);
                }
            
                document.getElementById('exportExcelButton').addEventListener('click', function() {
                    exportTableToExcel('tableKegiatan');
                });
            </script>
        </section>
    </div>

    @push('style')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    {{-- @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Pilih Unit Kerja',
                    allowClear: true,
                    dropdownParent: $('#uploadModal'),
                    width: '100%'
                });

                // Reset Select2 on modal close
                $('#uploadModal').on('hidden.bs.modal', function() {
                    $('.select2').val('').trigger('change');
                });
            });
        </script>
    @endpush --}}

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    dropdownParent: $('#uploadModal'),
                    width: '100%',
                    placeholder: 'Pilih Unit Kerja',
                    allowClear: true,
                    // Tambahkan konfigurasi search
                    searchInputPlaceholder: 'Cari Unit Kerja',
                    // Aktifkan fitur search
                    searchable: true,
                    // Konfigurasi matcher untuk search
                    matcher: function(params, data) {
                        // Jika search kosong, tampilkan semua
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        if (typeof data.text === 'undefined') {
                            return null;
                        }

                        // Convert ke lowercase untuk case-insensitive search
                        var term = params.term.toLowerCase();
                        var text = data.text.toLowerCase();

                        if (text.indexOf(term) > -1) {
                            return data;
                        }

                        return null;
                    }
                });

                // Debug: cek apakah Select2 sudah terinisialisasi
                console.log('Select2 initialized');
            });
        </script>
    @endpush

@endsection
