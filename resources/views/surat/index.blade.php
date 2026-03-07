@extends('layout.app')
@section('title', 'Manajemen Surat')

@section('main')
    <div class="main-content">
        <section class="section">
            {{-- ========== HEADER SECTION ========== --}}
            <div class="section-header mb-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm mr-3 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="mb-1" style="font-size: 1.75rem; font-weight: 700; color: #2c3e50;">
                                Manajemen Surat
                            </h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                                Kelola surat administratif SPI Politeknik Negeri Malang
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-body">
                {{-- ========== ALERTS ========== --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-lg mr-3"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-lg mr-3"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif


                {{-- ========== FILTER SECTION ========== --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            {{-- Bagian Filter --}}
                            <div class="col-lg-8">
                                <form method="GET" action="{{ route('surat.index') }}" id="filterForm">
                                    <div class="row" style="gap-y: 10px;">
                                        <div class="col-12 col-md-6">
                                            <label class="small font-weight-bold text-muted mb-1">
                                                <i class="fas fa-clipboard-check fa-fw text-primary mr-1"></i> STATUS
                                            </label>
                                            <select name="status"
                                                class="form-control form-control-sm shadow-sm border-0 bg-light"
                                                onchange="document.getElementById('filterForm').submit()">
                                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status
                                                </option>
                                                <option value="Draft" {{ $status == 'Draft' ? 'selected' : '' }}>Draft
                                                </option>
                                                <option value="Final" {{ $status == 'Final' ? 'selected' : '' }}>Final
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="small font-weight-bold text-muted mb-1">
                                                <i class="fas fa-calendar-alt fa-fw text-primary mr-1"></i> TAHUN
                                            </label>
                                            <select name="tahun"
                                                class="form-control form-control-sm shadow-sm border-0 bg-light"
                                                onchange="document.getElementById('filterForm').submit()">
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $tahun == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="jenis_surat" value="all">
                                    </div>
                                </form>
                            </div>

                            {{-- Bagian Tombol Tambah (Action) --}}
                            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                                <a href="{{ route('surat.create') }}"
                                    class="btn btn-primary shadow-sm d-inline-flex align-items-center">
                                    <i class="fas fa-plus fa-fw mr-2"></i> Buat Surat Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== DATA TABLE ========== --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-white d-flex align-items-center">
                            <i class="fas fa-list-alt mr-2"></i>
                            Daftar Surat
                            <span class="badge badge-light text-primary ml-2">{{ $surats->total() }} Surat</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center border-0">No</th>
                                        <th width="160" class="text-center border-0">Nomor Surat</th>
                                        <th class="border-0">Perihal</th>
                                        <th width="120" class="text-center border-0">Tanggal</th>
                                        <th width="100" class="text-center border-0">Status</th>
                                        <th width="160" class="text-center border-0">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($surats->currentPage() - 1) * $surats->perPage() + 1; @endphp
                                    @forelse ($surats as $surat)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border">{{ $no++ }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary p-2" style="font-size: 0.8rem; letter-spacing: 0.3px;">
                                                    {{ $surat->nomor_surat }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="text-dark" style="line-height: 1.5; font-size: 0.875rem;">
                                                    <div class="text-muted small mb-1">{{ $surat->tujuan_surat }}</div>
                                                    {{ Str::limit($surat->perihal, 60) }}
                                                    @if (strlen($surat->perihal) > 60)
                                                        <i class="fas fa-info-circle text-info ml-1" data-toggle="tooltip"
                                                            title="{{ $surat->perihal }}"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="font-weight-bold text-dark" style="font-size: 0.875rem;">
                                                    {{ $surat->tanggal_surat->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($surat->status == 'Draft')
                                                    <span class="badge badge-warning p-2" style="font-size: 0.8rem;">
                                                        <i class="fas fa-edit mr-1"></i> Draft
                                                    </span>
                                                @else
                                                    <span class="badge badge-success p-2" style="font-size: 0.8rem;">
                                                        <i class="fas fa-check-circle mr-1"></i> Final
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center align-items-center flex-nowrap"
                                                    style="gap: 4px;">

                                                    {{-- Lihat Detail --}}
                                                    <a href="{{ route('surat.show', $surat->id) }}"
                                                        class="btn btn-sm btn-info shadow-sm d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                        style="width: 32px; height: 32px;" data-toggle="tooltip"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    {{-- Finalisasi --}}
                                                    @if ($surat->status == 'Draft')
                                                        <form action="{{ route('surat.finalize', $surat->id) }}"
                                                            method="POST" class="m-0 p-0 flex-shrink-0"
                                                            onsubmit="return confirm('Finalisasi surat ini?')">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-primary shadow-sm d-inline-flex align-items-center justify-content-center"
                                                                style="width: 32px; height: 32px;" data-toggle="tooltip"
                                                                title="Finalisasi">
                                                                <i class="fas fa-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- Placeholder agar layout tetap sejajar --}}
                                                        <span style="width: 32px; height: 32px; display: inline-block; flex-shrink: 0;"></span>
                                                    @endif

                                                    {{-- Scan Surat --}}
                                                    @if ($surat->file_scan)
                                                        <a href="{{ route('surat.view-scan', $surat->id) }}"
                                                            class="btn btn-sm btn-success shadow-sm d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                            target="_blank" style="width: 32px; height: 32px;"
                                                            data-toggle="tooltip" title="Lihat Scan">
                                                            <i class="fas fa-image"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-warning shadow-sm d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                            style="width: 32px; height: 32px;"
                                                            onclick="bukaModalScan({{ $surat->id }}, '{{ addslashes($surat->nomor_surat) }}', true)"
                                                            data-toggle="tooltip" title="Upload Ulang">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @else
                                                        {{-- Placeholder agar layout tetap sejajar --}}
                                                        <span style="width: 32px; height: 32px; display: inline-block; flex-shrink: 0;"></span>
                                                        <button type="button"
                                                            class="btn btn-sm btn-secondary shadow-sm d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                            style="width: 32px; height: 32px;"
                                                            onclick="bukaModalScan({{ $surat->id }}, '{{ addslashes($surat->nomor_surat) }}', false)"
                                                            data-toggle="tooltip" title="Upload Scan">
                                                            <i class="fas fa-upload"></i>
                                                        </button>
                                                    @endif

                                                    {{-- Hapus --}}
                                                    <form action="{{ route('surat.destroy', $surat->id) }}"
                                                        method="POST" class="m-0 p-0 flex-shrink-0"
                                                        onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-danger shadow-sm d-inline-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px;" data-toggle="tooltip"
                                                            title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3 d-block text-light"></i>
                                                    <h6 class="font-weight-bold">Tidak Ada Data</h6>
                                                    <p class="mb-0 small">Belum ada data surat untuk filter yang dipilih</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($surats->hasPages())
                            <div class="card-footer bg-white border-top d-flex justify-content-end py-3">
                                {{ $surats->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            border-radius: 0.5rem;
        }

        .table thead th {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .btn-group-vertical .btn {
            border-radius: 0.25rem !important;
            margin-bottom: 0.25rem;
        }

        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            setTimeout(function() {
                $('.alert-success, .alert-danger').fadeOut('slow');
            }, 5000);
        });

        function bukaModalScan(suratId, nomorSurat, isUlang) {
            $('#modalScanSuratId').val(suratId);
            $('#modalScanAction').attr('action', '/surat/' + suratId + '/upload-scan');
            $('#modalScanTitle').text(isUlang ? 'Upload Ulang Scan Surat' : 'Upload Scan Surat');
            $('#modalScanNomor').text(nomorSurat);
            $('#modalScanBadge').text(isUlang ? 'Ganti File' : 'Upload Baru')
                .removeClass('badge-secondary badge-warning')
                .addClass(isUlang ? 'badge-warning' : 'badge-secondary');
            $('#inputFileScan').val('');
            $('#previewFileScan').addClass('d-none').attr('src', '');
            $('#labelFileScan').text('Pilih file (JPG, PNG, PDF — maks. 5MB)');
            $('#modalUploadScan').modal('show');
        }
    </script>
@endpush

{{-- ══════════════════════════════════════════
     MODAL UPLOAD SCAN SURAT
══════════════════════════════════════════ --}}
<div class="modal fade" id="modalUploadScan" tabindex="-1" role="dialog" aria-labelledby="modalScanLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalScanLabel">
                    <i class="fas fa-upload mr-2"></i>
                    <span id="modalScanTitle">Upload Scan Surat</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="modalScanAction" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="modalScanSuratId">

                <div class="modal-body">
                    <div class="mb-3">
                        <span class="text-muted" style="font-size:.875rem;">Nomor Surat:</span>
                        <span id="modalScanNomor" class="font-weight-bold ml-1"></span>
                        <span id="modalScanBadge" class="badge badge-secondary ml-2"></span>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-600">File Scan <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputFileScan" name="file_scan"
                                accept=".jpg,.jpeg,.png,.pdf" required>
                            <label class="custom-file-label" id="labelFileScan" for="inputFileScan">
                                Pilih file (JPG, PNG, PDF — maks. 5MB)
                            </label>
                        </div>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Format: JPG, JPEG, PNG, PDF &bull; Maks. 5MB
                        </small>
                    </div>

                    {{-- Preview gambar (hanya untuk JPG/PNG) --}}
                    <div class="text-center mt-2">
                        <img id="previewFileScan" src="" alt="Preview" class="img-fluid rounded d-none"
                            style="max-height:200px; border:1px solid #dee2e6;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-1"></i> Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Preview gambar saat file dipilih
    document.getElementById('inputFileScan').addEventListener('change', function() {
        const file = this.files[0];
        const label = document.getElementById('labelFileScan');
        const preview = document.getElementById('previewFileScan');

        if (!file) return;

        label.textContent = file.name;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
            preview.src = '';
        }
    });
</script>
