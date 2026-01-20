@extends('layout.app')
@section('title', 'Detail Unit Kerja - ' . $unitKerja)

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- HEADER --}}
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('manajemen-risiko.data', ['tahun' => $tahun]) }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Detail Unit Kerja: {{ $unitKerja }}</h1>
                        <small class="text-muted">Daftar kegiatan dan risiko untuk unit kerja {{ $unitKerja }} - Tahun
                            {{ $tahun }}</small>
                    </div>
                </div>
            </div>


            {{-- FILTER SECTION --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded">
                        <div class="card-header bg-primary text-white border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-filter"></i> Filter Tahun
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="GET"
                                action="{{ route('manajemen-risiko.detail-unit', ['unitKerja' => $unitKerja]) }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-weight-bold small text-uppercase">Tahun</label>
                                        <select name="tahun" class="form-control form-control-sm"
                                            onchange="this.form.submit()">
                                            @foreach (range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-9 d-flex align-items-end justify-content-start">
                                        <a href="{{ route('manajemen-risiko.data', ['tahun' => $tahun]) }}"
                                            class="btn btn-secondary btn-sm mr-2">
                                            <i class="fas fa-arrow-left"></i> Kembali ke Data Manajemen Risiko
                                        </a>

                                        {{-- PERBAIKAN TOMBOL UPDATE DI SINI --}}
                                        <button type="button" class="btn btn-dark btn-sm" id="btn-update-kegiatan">
                                            <i class="fas fa-sync-alt"></i> Update Kegiatan
                                            <span id="selected-count" class="badge badge-info ml-1"
                                                style="display:none">0</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- DATA TABLE SECTION --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm rounded">
                            <div
                                class="card-header bg-primary text-white border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="fas fa-list"></i> Daftar Kegiatan dengan Risiko
                                </h6>
                            </div>
                            <div class="card-body">
                                @if (count($kegiatans->items()) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr class="text-center">
                                                    <th class="text-center text-bold" width="5%">No</th>
                                                    <th class="text-center" width="5%"><i
                                                            class="fas fa-check-square"></i>
                                                    </th>
                                                    <th class="text-center" width="10%">ID Kegiatan</th>
                                                    <th class="text-center" width="25%">Nama Kegiatan</th>
                                                    <th class="text-center" width="10%">Jumlah Risiko</th>
                                                    <th class="text-center" width="12%">Sudah Ditampilkan</th>
                                                    <th class="text-center" width="12%">Total Skor Risiko</th>
                                                    <th class="text-center" width="21%">Preview Risiko</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = ($kegiatans->currentPage() - 1) * $kegiatans->perPage() + 1; @endphp
                                                @foreach ($kegiatans as $item)
                                                    <tr>
                                                        {{-- 1. NOMOR --}}
                                                        <td class="text-center align-middle">{{ $no++ }}</td>

                                                        {{-- 2. CHECKBOX --}}
                                                        <td class="text-center align-middle">
                                                            <div
                                                                class="custom-control custom-checkbox d-flex justify-content-center">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="check-{{ $item['kegiatan']->id_kegiatan }}"
                                                                    name="kegiatan_ids[]"
                                                                    value="{{ $item['kegiatan']->id_kegiatan }}">
                                                                <label class="custom-control-label"
                                                                    for="check-{{ $item['kegiatan']->id_kegiatan }}"></label>
                                                            </div>
                                                        </td>

                                                        {{-- 3. ID KEGIATAN --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-secondary py-2 px-3"
                                                                style="font-size: 12px; border-radius: 20px;">
                                                                {{ $item['kegiatan']->id_kegiatan }}
                                                            </span>
                                                        </td>

                                                        {{-- 4. NAMA KEGIATAN (Rata Kiri agar mudah dibaca, tapi vertikal tengah) --}}
                                                        <td class="align-middle">
                                                            <strong class="text-dark" style="font-size: 14px;">
                                                                {{ $item['kegiatan']->judul }}
                                                            </strong>
                                                        </td>

                                                        {{-- 5. JUMLAH RISIKO --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-warning text-white shadow-sm"
                                                                style="font-size: 14px; width: 35px; height: 35px; line-height: 25px; border-radius: 50%;">
                                                                {{ $item['jumlah_risiko'] }}
                                                            </span>
                                                        </td>

                                                        {{-- 6. SUDAH DITAMPILKAN --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-success shadow-sm"
                                                                style="font-size: 13px; padding: 8px 15px; border-radius: 20px;">
                                                                {{ $item['sudah_tampil'] }} / {{ $item['jumlah_risiko'] }}
                                                            </span>
                                                        </td>

                                                        {{-- 7. TOTAL SKOR --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-danger shadow-sm"
                                                                style="font-size: 14px; width: 40px; height: 40px; line-height: 30px; border-radius: 50%;">
                                                                {{ $item['total_skor_risiko'] }}
                                                            </span>
                                                        </td>

                                                        {{-- 8. PREVIEW RISIKO --}}
                                                        <td class="align-middle">
                                                            @if ($item['petas']->count() > 0)
                                                                <ul class="list-unstyled mb-0 pl-1">
                                                                    @foreach ($item['petas']->take(3) as $peta)
                                                                        <li class="mb-2 d-flex align-items-center">
                                                                            <span
                                                                                class="badge mr-2 badge-{{ $peta->tingkat_risiko == 'Extreme' ? 'danger' : ($peta->tingkat_risiko == 'High' ? 'warning text-white' : ($peta->tingkat_risiko == 'Moderate' ? 'info' : 'secondary')) }}"
                                                                                style="min-width: 60px;">
                                                                                {{ $peta->tingkat_risiko }}
                                                                            </span>
                                                                            <span class="text-truncate"
                                                                                style="max-width: 150px; font-size: 12px;"
                                                                                title="{{ $peta->judul }}">
                                                                                {{ $peta->judul }}
                                                                            </span>
                                                                            @if ($peta->tampil_manajemen_risiko == 1)
                                                                                <i class="fas fa-check-circle text-success ml-1"
                                                                                    title="Sudah ditampilkan"></i>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                    @if ($item['petas']->count() > 3)
                                                                        <li class="text-muted small mt-1">
                                                                            <i class="fas fa-ellipsis-h"></i>
                                                                            +{{ $item['petas']->count() - 3 }} risiko
                                                                            lainnya
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            @else
                                                                <div class="text-muted small font-italic text-center">
                                                                    <i class="fas fa-info-circle mr-1"></i> Tidak ada
                                                                    risiko
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Pagination --}}
                                    <div class="mt-3 d-flex justify-content-end">
                                        {{ $kegiatans->appends(['tahun' => $tahun])->links('pagination::bootstrap-4') }}
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0 text-center">
                                        <i class="fas fa-info-circle fa-lg mb-2 d-block"></i>
                                        Tidak ada kegiatan dengan risiko untuk unit kerja
                                        <strong>{{ $unitKerja }}</strong> pada tahun
                                        <strong>{{ $tahun }}</strong>.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .badge-lg {
            font-size: 14px;
            padding: 6px 10px;
        }

        .table thead th {
            vertical-align: middle;
        }
    </style>

    @push('scripts')
        <script>
            $(document).ready(function() {
                console.log('Detail Unit Kerja JS Loaded'); // Debug log

                // 1. Hitung checkbox yang dicentang
                $('input[name="kegiatan_ids[]"]').on('change', function() {
                    let count = $('input[name="kegiatan_ids[]"]:checked').length;
                    console.log('Checkbox changed, selected:', count); // Debug
                    if (count > 0) {
                        $('#selected-count').text(count).show();
                    } else {
                        $('#selected-count').hide();
                    }
                });

                // 2. Tombol Update Kegiatan - FIXED VERSION
                $('#btn-update-kegiatan').on('click', function(e) {
                    e.preventDefault();
                    console.log('Update button clicked'); // Debug

                    // Ambil semua ID yang dicentang
                    var selectedIds = [];
                    $('input[name="kegiatan_ids[]"]:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    console.log('Selected IDs:', selectedIds); // Debug

                    // Validasi
                    if (selectedIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak ada kegiatan dipilih',
                            text: 'Silakan centang minimal satu kegiatan yang ingin ditampilkan.',
                            confirmButtonColor: '#3085d6',
                        });
                        return;
                    }

                    // Tampilkan konfirmasi
                    Swal.fire({
                        title: 'Konfirmasi Update',
                        html: `
                        <div class="text-left">
                            <p>Anda akan menampilkan <strong>${selectedIds.length} kegiatan</strong> terpilih:</p>
                            <div class="alert alert-light small mt-2 mb-0">
                                <ul class="mb-0 pl-3">
                                    ${selectedIds.map((id, index) => 
                                        `<li>Kegiatan #${index + 1} (ID: ${id})</li>`
                                    ).join('')}
                                </ul>
                            </div>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle"></i> 
                                Risiko dari kegiatan ini akan ditampilkan di halaman Manajemen Risiko.
                            </p>
                        </div>
                    `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '<i class="fas fa-check"></i> Ya, Update!',
                        cancelButtonText: '<i class="fas fa-times"></i> Batal',
                        width: '550px',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return new Promise((resolve) => {
                                // Buat form untuk submit
                                var form = document.createElement('form');
                                form.method = 'POST';
                                form.action =
                                    "{{ route('manajemen-risiko.tampilkan-kegiatan') }}";

                                // CSRF Token
                                var csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = "{{ csrf_token() }}";
                                form.appendChild(csrfInput);

                                // Tahun
                                var tahunInput = document.createElement('input');
                                tahunInput.type = 'hidden';
                                tahunInput.name = 'tahun';
                                tahunInput.value = "{{ $tahun }}";
                                form.appendChild(tahunInput);

                                // Unit Kerja
                                var unitInput = document.createElement('input');
                                unitInput.type = 'hidden';
                                unitInput.name = 'unit_kerja';
                                unitInput.value = "{{ $unitKerja }}";
                                form.appendChild(unitInput);

                                // Kegiatan IDs
                                selectedIds.forEach(function(id) {
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'kegiatan_ids[]';
                                    input.value = id;
                                    form.appendChild(input);
                                });

                                // Submit form
                                document.body.appendChild(form);
                                console.log('Submitting form to:', form.action); // Debug
                                form.submit();
                                resolve();
                            });
                        }
                    });
                });

                // Debug: Cek jika jQuery terload
                console.log('jQuery version:', $.fn.jquery);
            });
        </script>

        @if (session('reload_parent'))
            <script>
                $(document).ready(function() {
                    // Tunggu 1 detik lalu refresh halaman data manajemen risiko
                    setTimeout(function() {
                        // Redirect ke halaman data manajemen risiko
                        window.location.href = "{{ route('manajemen-risiko.data', ['tahun' => $tahun]) }}";
                    }, 1500);
                });
            </script>
        @endif


        @if (session('auto_refresh'))
            <script>
                $(document).ready(function() {
                    console.log('Auto-refresh triggered for parent page');

                    // Tampilkan toast sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Berhasil!',
                        html: `{!! session('success') !!}`,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect ke halaman Data Manajemen Risiko setelah konfirmasi
                            const tahun = "{{ session('updated_tahun', $tahun) }}";
                            const url = "{{ route('manajemen-risiko.data') }}?tahun=" + tahun;

                            // Buka di tab baru atau refresh parent
                            if (window.opener && !window.opener.closed) {
                                // Jika dibuka dari popup/modal, refresh parent
                                window.opener.location.href = url;
                                window.close();
                            } else {
                                // Redirect langsung
                                window.location.href = url;
                            }
                        }
                    });
                });
            </script>
        @endif
    @endpush
