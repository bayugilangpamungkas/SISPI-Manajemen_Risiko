@extends('layout.app')
@section('title', 'Tambah Tugas')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Tambah Tugas</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label class="font-weight-bold">HARI TANGGAL</label>
                                        <input type="text" class="form-control @error('waktu') is-invalid @enderror"
                                            id="waktu" name="waktu" value="{{ old('waktu') }}"
                                            placeholder="Masukkan Hari dan Tanggal Penugasan..." readonly>
                                        @error('waktu')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">TEMPAT</label>
                                        <input type="text" class="form-control @error('tempat') is-invalid @enderror"
                                            name="tempat" value="{{ old('tempat') }}"
                                            placeholder="Masukkan Tempat Penugasan...">
                                        @error('tempat')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Unit Kerja</label>
                                        <select id="unitKerja" name="unitkerja" class="form-control" text="black">
                                            <option value="">- Pilih Unit Kerja -</option>
                                            @foreach ($unitKerja as $uk)
                                                <option value="{{ $uk->id }}"
                                                    {{ old('unitkerja') == $uk->id ? 'selected' : null }}>
                                                    {{ $uk->nama_unit_kerja }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unitkerja')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">JENIS</label>
                                        <select id="jenis" name="jenis"
                                            class="form-control @error('jenis') is-invalid @enderror">
                                            <option value="">- Pilih Jenis Tugas -</option>
                                            <option value="{{ $jenisKegiatan[0]->id }}"
                                                {{ old('jenis') == $jenisKegiatan[0]->jenis ? 'selected' : null }}>
                                                {{ $jenisKegiatan[0]->jenis }}
                                            </option>
                                            <option value="{{ $jenisKegiatan[1]->id }}"
                                                {{ old('jenis') == $jenisKegiatan[1]->jenis ? 'selected' : null }}>
                                                {{ $jenisKegiatan[1]->jenis }}
                                            </option>
                                        </select>
                                        @error('jenis')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="picField">
                                        <label class="font-weight-bold">PIC (PENANGGUNG JAWAB)</label>
                                        <select name="tanggungjawab" class="form-control" text="black">
                                            <option value="">- Pilih PIC -</option>
                                            @foreach ($pic as $tanggungjawab)
                                                <option value="{{ $tanggungjawab->name }}"
                                                    {{ old('pic') == $tanggungjawab->name ? 'selected' : null }}>
                                                    {{ $tanggungjawab->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('tanggungjawab')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="anggotaField" style="display: none;">
                                        <label class="font-weight-bold">ANGGOTA</label>
                                        <select id="anggota" name="anggota[]"
                                            class="js-example-basic-multiple form-control" multiple="multiple">
                                            {{-- <option value="">- Pilih Anggota -</option> --}}
                                            @foreach ($pic as $member)
                                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('anggota')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">JUDUL</label>
                                        <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                            name="judul" value="{{ old('judul') }}"
                                            placeholder="Masukkan Judul Tugas...">
                                        @error('judul')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">DESKRIPSI TUGAS</label>
                                        <input type="text" class="form-control @error('deskripsi') is-invalid @enderror"
                                            name="deskripsi" value="{{ old('deskripsi') }}"
                                            placeholder="Masukkan Deskripsi Tugas...">
                                        @error('deskripsi')
                                            <div class="alert alert-danger mt-2">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">BIDANG</label>
                                        <input type="text" id="bidang"
                                            class="form-control @error('bidang') is-invalid @enderror" name="bidang"
                                            value="{{ old('bidang') }}" placeholder="Masukkan Bidang Tugas..." readonly>
                                        @error('bidang')
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
        </section>
    </div>

    @push('style')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            .select2-container {
                width: 100% !important;
            }

            .select2-selection--multiple {
                min-height: 38px !important;
                border: 1px solid #ced4da !important;
            }

            .select2-container--default .select2-selection--multiple {
                border-radius: 4px;
            }

            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                padding-left: 2% !important;
                padding-right: 0px !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2
                // $('#anggota').select2({
                //     allowClear: true,
                //     width: '100%'
                // });

                const jenisSelect = document.getElementById('jenis');
                const bidangInput = document.getElementById('bidang');
                const anggotaField = document.getElementById('anggotaField');
                const picField = document.getElementById('picField');

                jenisSelect.addEventListener('change', function() {
                    const selectedJenis = jenisSelect.options[jenisSelect.selectedIndex].text;
                    bidangInput.value = selectedJenis;

                    if (this.value === '1' || this.value == 1) {
                        anggotaField.style.display = 'block';
                        picField.style.display = 'none';

                        // Clear PIC field
                        $('select[name="tanggungjawab"]').val('').trigger('change');

                        // Clear and reinitialize anggota field
                        $('#anggota').val([]).trigger('change');
                        $('#anggota').select2();
                    } else {
                        anggotaField.style.display = 'none';
                        picField.style.display = 'block';

                        // Clear anggota field
                        $('#anggota').val([]).trigger('change');

                        // Clear PIC field
                        $('select[name="tanggungjawab"]').val('').trigger('change');
                    }
                });
            });

            // Datepicker code
            $(function() {
                $.datepicker.setDefaults($.datepicker.regional['id']);
                $("#waktu").datepicker({
                    dateFormat: "DD, d MM yy",
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        var dayNames = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];
                        var day = dayNames[date.getUTCDay()];
                        var formattedDate = day + ", " + $.datepicker.formatDate("d MM yy", date);
                        $(this).val(formattedDate);
                    }
                }).attr('readonly', 'readonly');
            });

            $.datepicker.regional['id'] = {
                closeText: 'Tutup',
                prevText: '←',
                nextText: '→',
                currentText: 'Hari ini',
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ],
                monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                dayNames: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                dayNamesShort: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                dayNamesMin: ['Mi', 'Se', 'Se', 'Ra', 'Ka', 'Ju', 'Sa'],
                weekHeader: 'Mg',
                dateFormat: 'dd/mm/yy',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['id']);
        </script>
    @endpush

@endsection
