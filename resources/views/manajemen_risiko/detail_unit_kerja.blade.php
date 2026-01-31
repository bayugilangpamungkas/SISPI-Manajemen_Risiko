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
                                                    <th class="text-center text-bold" width="3%">No</th>
                                                    <th class="text-center" width="3%"><i
                                                            class="fas fa-check-square"></i>
                                                    </th>
                                                    <th class="text-center" width="8%">ID Kegiatan</th>
                                                    <th class="text-center" width="20%">Nama Kegiatan</th>
                                                    <th class="text-center" width="6%">Jumlah Risiko</th>
                                                    <th class="text-center" width="8%">Sudah Ditampilkan</th>
                                                    <th class="text-center" width="7%">Total Skor Risiko</th>
                                                    <th class="text-center" width="45%">Preview Risiko</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = ($kegiatans->currentPage() - 1) * $kegiatans->perPage() + 1; @endphp
                                                @foreach ($kegiatans as $item)
                                                    <tr>
                                                        {{-- 1. NOMOR --}}
                                                        <td class="text-center align-middle">
                                                            {{ $no++ }}
                                                        </td>

                                                        {{-- 2. CHECKBOX --}}
                                                        <td class="text-center align-middle">
                                                            <div class="custom-control custom-checkbox d-flex justify-content-center">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="check-peta-{{ $item['peta']->id }}-{{ $loop->index }}"
                                                                    name="peta_ids[]"
                                                                    value="{{ $item['peta']->id }}">
                                                                <label class="custom-control-label"
                                                                    for="check-peta-{{ $item['peta']->id }}-{{ $loop->index }}"></label>
                                                            </div>
                                                        </td>

                                                        {{-- 3. ID KEGIATAN --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-secondary py-2 px-3"
                                                                style="font-size: 11px; border-radius: 20px;">
                                                                {{ $item['kegiatan']->id_kegiatan }}
                                                            </span>
                                                        </td>

                                                        {{-- 4. NAMA KEGIATAN --}}
                                                        <td class="align-middle">
                                                            <strong class="text-dark" style="font-size: 13px;">
                                                                {{ $item['kegiatan']->judul }}
                                                            </strong>
                                                        </td>

                                                        {{-- 5. JUMLAH RISIKO --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-warning text-white shadow-sm"
                                                                style="font-size: 13px; width: 32px; height: 32px; line-height: 22px; border-radius: 50%;">
                                                                {{ $item['jumlah_risiko'] }}
                                                            </span>
                                                        </td>

                                                        {{-- 6. SUDAH DITAMPILKAN --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-success shadow-sm"
                                                                style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">
                                                                {{ $item['sudah_tampil'] }} / {{ $item['jumlah_risiko'] }}
                                                            </span>
                                                        </td>

                                                        {{-- 7. TOTAL SKOR --}}
                                                        <td class="text-center align-middle">
                                                            <span class="badge badge-danger shadow-sm"
                                                                style="font-size: 13px; width: 38px; height: 38px; line-height: 28px; border-radius: 50%;">
                                                                {{ $item['total_skor_risiko'] }}
                                                            </span>
                                                        </td>

                                                        {{-- 8. PREVIEW RISIKO --}}
                                                        <td class="align-middle" style="padding: 10px;">
                                                            @if ($item['peta'])
                                                                <div class="d-flex align-items-center" 
                                                                     style="padding: 10px 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid 
                                                                            {{ $item['peta']->tingkat_risiko == 'Extreme' ? '#dc3545' : ($item['peta']->tingkat_risiko == 'High' ? '#ffc107' : ($item['peta']->tingkat_risiko == 'Moderate' ? '#17a2b8' : '#6c757d')) }};">
                                                                    
                                                                    {{-- Badge Tingkat Risiko --}}
                                                                    <span class="badge badge-{{ $item['peta']->tingkat_risiko == 'Extreme' ? 'danger' : ($item['peta']->tingkat_risiko == 'High' ? 'warning text-dark' : ($item['peta']->tingkat_risiko == 'Moderate' ? 'info' : 'secondary')) }}" 
                                                                          style="min-width: 80px; font-size: 11px; padding: 5px 10px; margin-right: 12px; font-weight: 600;">
                                                                        {{ $item['peta']->tingkat_risiko }}
                                                                    </span>
                                                                    
                                                                    {{-- Judul Risiko (Full Text) --}}
                                                                    <span style="flex: 1; font-size: 13px; color: #2c3e50; line-height: 1.4; font-weight: 500;">
                                                                        {{ $item['peta']->judul }}
                                                                    </span>
                                                                    
                                                                    {{-- Icon Status Tampil --}}
                                                                    @if ($item['peta']->tampil_manajemen_risiko == 1)
                                                                        <span class="badge badge-success ml-2" 
                                                                              style="font-size: 10px; padding: 4px 8px;"
                                                                              title="Sudah ditampilkan">
                                                                            <i class="fas fa-check"></i> Tampil
                                                                        </span>
                                                                    @else
                                                                        <span class="badge badge-secondary ml-2" 
                                                                              style="font-size: 10px; padding: 4px 8px;"
                                                                              title="Belum ditampilkan">
                                                                            <i class="fas fa-eye-slash"></i> Hidden
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="alert alert-light text-center mb-0 py-2" style="font-size: 12px;">
                                                                    <i class="fas fa-info-circle text-muted mr-1"></i> 
                                                                    <span class="text-muted font-italic">Tidak ada risiko</span>
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

        /* Styling untuk tabel dengan rowspan */
        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        /* Styling untuk tabel - semua data diulang per baris */
        .table tbody tr td {
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* Styling preview risiko */
        .table tbody tr td div[style*="border-left"] {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover td div[style*="border-left"] {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Badge Custom Colors */
        .badge-warning.text-dark {
            color: #856404 !important;
        }

        /* Responsive table */
        @media (max-width: 1400px) {
            .table {
                font-size: 13px;
            }
        }
    </style>


    @push('scripts')
        <script>
            $(document).ready(function() {
                console.log('Detail Unit Kerja JS Loaded'); // Debug log

                // 1. Hitung checkbox yang dicentang
                $('input[name="peta_ids[]"]').on('change', function() {
                    let count = $('input[name="peta_ids[]"]:checked').length;
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

                    // Ambil semua ID risiko (peta) yang dicentang
                    var selectedIds = [];
                    $('input[name="peta_ids[]"]:checked').each(function() {
                        selectedIds.push($(this).val());
                    });

                    console.log('Selected Peta IDs:', selectedIds); // Debug

                    // Validasi
                    if (selectedIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak ada risiko dipilih',
                            text: 'Silakan centang minimal satu risiko yang ingin ditampilkan.',
                            confirmButtonColor: '#3085d6',
                        });
                        return;
                    }

                    // Tampilkan konfirmasi
                    Swal.fire({
                        title: 'Konfirmasi Update',
                        html: `
                        <div class="text-left">
                            <p>Anda akan menampilkan <strong>${selectedIds.length} risiko</strong> terpilih</p>
                            <div class="alert alert-light small mt-2 mb-0">
                                <p class="mb-0"><i class="fas fa-info-circle text-primary mr-1"></i> ${selectedIds.length} risiko akan ditandai untuk ditampilkan di Manajemen Risiko</p>
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
                                form.action = "{{ route('manajemen-risiko.update-tampil') }}";

                                // CSRF Token
                                var csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = "{{ csrf_token() }}";
                                form.appendChild(csrfInput);

                                // Peta IDs (Risiko yang dipilih)
                                selectedIds.forEach(function(id) {
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'selected_ids[]';
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
