@extends('layout.app')
@section('title', 'Tambah Penelaah')

@section('main')
{{-- Add Waktu Modal --}}
<div class="modal fade" id="addWaktuModal" tabindex="-1" aria-labelledby="addWaktuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tambahTugasKetua', ['jenis' => $peta->jenis]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addWaktuModalLabel">Jadikan Ketua Pemilih Penelaah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Tambah Telaah</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 shadow rounded">
                        <div class="card-body">
                            <form action="{{ route('petas.tambahtugas', ['jenis' => $peta->jenis]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                {{-- <div class="form-group">
                                    <label class="font-weight-bold">HARI TANGGAL</label>
                                    <input type="text" class="form-control @error('waktu') is-invalid @enderror"
                                        id="waktu2" name="waktu" value="{{ old('waktu') }}"
                                        placeholder="Masukkan Hari dan Tanggal Penugasan..." readonly>
                                    @error('waktu')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div> --}}
                                <div class="form-group">
                                    <label class="font-weight-bold">ANGGOTA</label>
                                    <select name ="anggota" class="form-control">
                                        <option value="">- Pilih Anggota -</option>
                                        @foreach ($users as $anggota)
                                            <option value="{{ $anggota->name }}"
                                                {{ old('anggota') == $anggota->name ? 'selected' : null }}>{{ $anggota->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <!-- error message untuk anggota -->
                                    @error('anggota')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-md btn-primary">SIMPAN</button>
                                <button type="reset" class="btn btn-md btn-warning">RESET</button>
                                {{-- <button type="button" class="btn btn-md btn-outline-primary" data-toggle="modal" data-target="#addWaktuModal">Jadikan Ketua Pemilih Penelaah</button> --}}
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
    @endpush

    @push('scripts')
        {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
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
                $("#waktu2").datepicker({
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
