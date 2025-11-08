@extends('layout.app')
@section('title', 'Detail Tugas')
@section('main')
    <!-- Modal Popup -->
    <div class="modal fade" id="disapprovePICModal" tabindex="-1" role="dialog" aria-labelledby="disapprovePICModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disapprovePICModalLabel">
                        Tolak Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('posts.disapprovePIC', ['id' => $posts->id, 'type' => 'pic']) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf

                        {{-- Tambahkan error handling --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="koreksiReviuPIC">Pilih dokumen Perbaikan:</label>
                            <input type="file" name="koreksiReviuPIC"
                                class="form-control @error('koreksiReviuPIC') is-invalid @enderror" id="koreksiReviuPIC"
                                accept=".doc,.docx">
                            @error('koreksiReviuPIC')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-danger ml-1 mb-2" style="font-style: italic;">
                            *dokumen harus berformat doc, docx, ukuran maksimal 10MB
                        </small>

                        <div class="form-group">
                            <label for="commentPIC">Komentar</label>
                            <textarea class="form-control @error('commentPIC') is-invalid @enderror" name="commentPIC" id="commentPIC"
                                rows="3" required>{{ old('commentPIC') }}</textarea>
                            @error('commentPIC')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left"
                        style="font-size: 1.3rem"></i></a>
                <h1>Detail Tugas</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">Detail Penugasan</span>
                </h4>
                {{-- <div class=" mb-2 ">
            <a href="/detailTugas/print/{id}" target="_blank" class="btn fa-solid fa-print bg-primary p-2 text-white" data-toggle="tooltip" title="PRINT"></a>
        </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">

                                <form action="/detailTugas/{{ $posts->id }}" method="GET"
                                    enctype="multipart/form-data">

                                    @csrf
                                    <table class="table table-white table-sm">
                                        <tbody>
                                            <tr>
                                                <th class="col-2">Unit Kerja :</th>
                                                <td>{{ $posts->unitKerja->nama_unit_kerja }}</td>
                                            </tr>
                                            <tr>
                                                <th class="col-2">Waktu :</th>
                                                <td><i class="fa-regular fa-calendar-days mr-1" style="color: #0050db;"></i>
                                                    {{ $posts->waktu ? $posts->waktu : '' }}</td>
                                            </tr>

                                            <tr>
                                                <th class="col-2">Tempat :</th>
                                                <td><i class="fa-regular fa-building mr-1"
                                                        style="color: #0050db;"></i>{{ $posts->tempat }}</td>
                                            </tr>
                                            <tr>
                                                <th class="col-2">PIC : </th>
                                                <td>{{ $posts->tanggungjawab }}</td>
                                            </tr>
                                            {{-- <tr>
                                                <th class="col-2">Anggota : </th>
                                                <td>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th scope="col">No</th>
                                                                <th scope="col">Nama Anggota</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center">1</td>
                                                                <td>{{ $posts->anggota }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr> --}}
                                            <tr>
                                                <th class="col-2">Jenis : </th>
                                                <td><span class="badge badge-primary">{{ $jenisKegiatan->jenis }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="col-2">Judul : </th>
                                                <td>{{ $posts->judul }}</td>
                                            </tr>
                                            <tr>
                                                <th class="col-2">Deskripsi Tugas : </th>
                                                <td>{{ $posts->deskripsi }}</td>
                                            </tr>
                                            <tr>
                                                <th class="col-2">Bidang : </th>
                                                <td><span class="badge badge-info">{{ $posts->bidang }}</span></td>
                                            </tr>


                                            <tr>
                                                <th class="col-2">Dokumen : </th>
                                                <td>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th scope="col">No</th>
                                                                <th colspan="2">Nama Berkas</th>
                                                                <th scope="col">Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center">1</td>
                                                                <td>{{ isset($templateA) && $templateA->dokumen ? $templateA->dokumen : 'Belum diupload oleh admin' }}
                                                                </td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    @if (isset($templateA->dokumen))
                                                                        <a href="{{ asset('template_dokumen/' . $templateA->dokumen) }}"
                                                                            target="_blank" class="btn btn-info btn-sm"
                                                                            title="Buka Dokumen">
                                                                            <i class="fa-solid fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>

                                                                <td>Template Dokumen Reviu</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">2</td>
                                                                <td>{{ isset($templateB) && $templateB->dokumen ? $templateB->dokumen : 'Belum diupload oleh admin' }}
                                                                </td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    @if (isset($templateB->dokumen))
                                                                        <a href="{{ asset('template_dokumen/' . $templateB->dokumen) }}"
                                                                            target="_blank" class="btn btn-info btn-sm"
                                                                            title="Buka Dokumen">
                                                                            <i class="fa-solid fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>

                                                                <td>Template Berita Acara</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">3</td>
                                                                <td>{{ isset($templateC) && $templateC->dokumen ? $templateC->dokumen : 'Belum diupload oleh admin' }}
                                                                </td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    @if (isset($templateC->dokumen))
                                                                        <a href="{{ asset('template_dokumen/' . $templateC->dokumen) }}"
                                                                            target="_blank" class="btn btn-info btn-sm"
                                                                            title="Buka Dokumen">
                                                                            <i class="fa-solid fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>

                                                                <td>Template Lembar Pengesahan</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">4</td>
                                                                <td>{{ isset($templateD) && $templateD->dokumen ? $templateD->dokumen : 'Belum diupload oleh admin' }}
                                                                </td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    @if (isset($templateD->dokumen))
                                                                        <a href="{{ asset('template_dokumen/' . $templateD->dokumen) }}"
                                                                            target="_blank" class="btn btn-info btn-sm"
                                                                            title="Buka Dokumen">
                                                                            <i class="fa-solid fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>

                                                                <td>Template Kertas Kerja</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">5</td>
                                                                <td>{{ isset($templateE) && $templateE->dokumen ? $templateE->dokumen : 'Belum diupload oleh admin' }}
                                                                </td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    @if (isset($templateE->dokumen))
                                                                        <a href="{{ asset('template_dokumen/' . $templateE->dokumen) }}"
                                                                            target="_blank" class="btn btn-info btn-sm"
                                                                            title="Buka Dokumen">
                                                                            <i class="fa-solid fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>

                                                                <td>Template Dokumen Tindak Lanjut</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-right"> </th>
                                                <td></td>
                                            </tr>
                                </form>

                                @php
                                    use Carbon\Carbon;

                                    // Original date in Indonesian format
                                    $dateString = $posts->waktu; // e.g., 'Senin, 1 Juli 2024'

                                    // Define mappings from Indonesian to English for days and months
                                    $days = [
                                        'Senin' => 'Monday',
                                        'Selasa' => 'Tuesday',
                                        'Rabu' => 'Wednesday',
                                        'Kamis' => 'Thursday',
                                        'Jumat' => 'Friday',
                                        'Sabtu' => 'Saturday',
                                        'Minggu' => 'Sunday',
                                    ];

                                    $months = [
                                        'Januari' => 'January',
                                        'Februari' => 'February',
                                        'Maret' => 'March',
                                        'April' => 'April',
                                        'Mei' => 'May',
                                        'Juni' => 'June',
                                        'Juli' => 'July',
                                        'Agustus' => 'August',
                                        'September' => 'September',
                                        'Oktober' => 'October',
                                        'November' => 'November',
                                        'Desember' => 'December',
                                    ];

                                    // Replace Indonesian day and month names with English equivalents
                                    $dateString = str_replace(array_keys($days), array_values($days), $dateString);
                                    $dateString = str_replace(array_keys($months), array_values($months), $dateString);

                                    // Parse the modified date string
                                    $dueDate = Carbon::createFromFormat(
                                        'l, j F Y',
                                        $dateString,
                                        'Asia/Jakarta',
                                    )->addDays(14);
                                    $isPastDue = now()->greaterThan($dueDate);

                                @endphp

                                <div class="alert {{ $isPastDue ? 'alert-danger' : 'alert-warning' }}"
                                    style="{{ $isPastDue ? 'background-color: red !important;' : '' }}">
                                    <strong>{{ $isPastDue ? 'Peringatan!' : 'Informasi' }}</strong>
                                    Tenggat waktu tugas ini adalah 2 minggu sejak ditugaskan.
                                    @if ($isPastDue)
                                        <br> <em>Tenggat waktu sudah terlewati.</em>
                                    @endif
                                </div>


                                <tr>
                                    <th class="text-left bg-success p-1" style="color:white">Pengumpulan : </th>
                                    <td class="text-right bg-success p-1 "></td>
                                </tr>
                                <!-- Form POST untuk pengumpulan dokumen -->
                                {{-- <tr>
                                    <th class="col-2">Dokumen Reviu : </th>
                                    <td>
                                        Upload Dokumen Reviu harus berformat word (.doc / .docx)
                                        <form action="/detailTugas/{{ $posts->id }}/submit" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="file_type" value="hasilReviu">
                                            <div class="input-group mb-3">
                                                <input type="file" name="hasilReviu" class="form-control m-2"
                                                    id="inputGroupFile" accept=".doc, .docx">
                                                <button type="submit" class=" m-2 btn btn-md btn-primary">Upload</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr> --}}
                                <tr>
                                    <th class="col-2">Berita Acara : </th>
                                    <td>
                                        Upload Berita Acara harus berformat word (.doc / .docx)
                                        <form action="/detailTugas/{{ $posts->id }}/submit" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="file_type" value="hasilBerita">
                                            <div class="input-group mb-3">
                                                <input type="file" name="hasilBerita" class="form-control m-2"
                                                    id="inputGroupFile" accept=".doc, .docx">
                                                <button type="submit" class=" m-2 btn btn-md btn-primary">Upload</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="col-2">Lembar Pengesahan : </th>
                                    <td>
                                        Upload Lembar Pengesahan harus berformat word (.doc / .docx)
                                        <form action="/detailTugas/{{ $posts->id }}/submit" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="file_type" value="hasilPengesahan">
                                            <div class="input-group mb-3">
                                                <input type="file" name="hasilPengesahan" class="form-control m-2"
                                                    id="inputGroupFile" accept=".doc, .docx">
                                                <button type="submit" class=" m-2 btn btn-md btn-primary">Upload</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="col-2">Kertas Kerja : </th>
                                    <td>
                                        Upload Kertas Kerja harus berformat excel (.xls / .xlsx)
                                        <form action="/detailTugas/{{ $posts->id }}/submit" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="file_type" value="hasilRubrik">
                                            <div class="input-group mb-3">
                                                <input type="file" name="hasilRubrik" class="form-control m-2"
                                                    id="inputGroupFile" accept=".xls, .xlsx">
                                                <button type="submit" class=" m-2 btn btn-md btn-primary">Upload</button>
                                            </div>
                                        </form>
                                        {{-- <button type="submit" class="ml-2 mb-2 btn btn-md btn-primary">SIMPAN</button> --}}
                                    </td>
                                </tr>

                                <tr>
                                    <th class="col-2">Dokumen Pengumpulan : </th>
                                    <td>
                                        @php
                                            $files = [
                                                [
                                                    'name' => $posts->hasilReviu,
                                                    'path' => 'hasil_reviu/',
                                                    'label' => 'Dokumen Reviu',
                                                    'approvalReviu' => $posts->approvalReviu,
                                                    'approvalReviuPIC' => $posts->approvalReviuPIC,
                                                    'commentReviu' => $posts->commentReviu,
                                                    'approval' => $posts->approvalReviu,
                                                    'approval_at' => $posts->approvalReviu_at,
                                                    'uploaded_at' => $posts->hasilReviu_uploaded_at,
                                                    'type' => 'reviu', // Add this type identifier
                                                ],
                                                [
                                                    'name' => $posts->hasilBerita,
                                                    'path' => 'hasil_berita/',
                                                    'label' => 'Berita Acara',
                                                    'approval' => $posts->approvalBerita,
                                                    'approval_at' => $posts->approvalBerita_at,
                                                    'uploaded_at' => $posts->hasilBerita_uploaded_at,
                                                ],
                                                [
                                                    'name' => $posts->hasilPengesahan,
                                                    'path' => 'hasil_pengesahan/',
                                                    'label' => 'Lembar Pengesahan',
                                                    'approval' => $posts->approvalPengesahan,
                                                    'approval_at' => $posts->approvalPengesahan_at,
                                                    'uploaded_at' => $posts->hasilPengesahan_uploaded_at,
                                                ],
                                                [
                                                    'name' => $posts->hasilRubrik,
                                                    'path' => 'hasil_rubrik/',
                                                    'label' => 'Kertas Kerja',
                                                    'approval' => $posts->approvalRubrik,
                                                    'approval_at' => $posts->approvalRubrik_at,
                                                    'uploaded_at' => $posts->hasilRubrik_uploaded_at,
                                                ],
                                            ];

                                            $filteredFiles = array_filter($files, function ($file) {
                                                return !is_null($file['name']);
                                            });
                                        @endphp

                                        @if (count($filteredFiles) > 0)
                                            <table class="table table-bordered table-responsive">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th scope="col">No</th>
                                                        <th colspan="2">Nama Berkas</th>
                                                        <th scope="col">Keterangan</th>
                                                        <th scope="col">Status</th>
                                                        {{-- <th scope="col">Tanggal Approval</th> --}}
                                                        <th scope="col" colspan="2">Aksi</th>
                                                        <th scope="col">Waktu Pengumpulan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @foreach ($filteredFiles as $document)
                                                        <tr>
                                                            <td class="text-center">{{ $no++ }}</td>
                                                            <td>{{ $document['name'] }}</td>
                                                            <td>
                                                                <a href="{{ asset($document['path'] . '/' . $document['name']) }}"
                                                                    target="_blank" class="btn btn-info btn-sm"
                                                                    title="Buka Dokumen">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                            </td>
                                                            <td>{{ $document['label'] }}</td>
                                                            <td>
                                                                @if ($document['approval'] == 'approved')
                                                                    <span class="badge badge-success">Disetujui</span>
                                                                @elseif($document['approval'] == 'rejected')
                                                                    <span class="badge badge-danger">Ditolak</span>
                                                                @else
                                                                    <span class="badge badge-warning">Pending</span>
                                                                @endif
                                                            </td>
                                                            {{-- <td>
                                                                @if ($document['approval'] == 'approved')
                                                                    {{ \Carbon\Carbon::parse($document['approval_at'])->format('d F Y') }}
                                                                @endif
                                                            </td> --}}
                                                            <td>
                                                                @if (isset($document['type']) && $document['type'] == 'reviu')
                                                                    @if ($document['approvalReviuPIC'] == 'approved')
                                                                        @if ($document['approvalReviu'] == 'rejected')
                                                                            <small>Dokumen reviu ditolak oleh ketua</small>
                                                                        @else
                                                                            <span
                                                                                class="badge badge-success">Disetujui</span>
                                                                        @endif
                                                                    @elseif ($document['approvalReviuPIC'] == 'rejected')
                                                                        {{-- <span class="text-danger">Rejected</span> --}}
                                                                        {{-- <form action="{{ route('posts.disapprovePIC', ['id' => $posts->id, 'type' => $document['type']]) }}" method="POST" style="display:inline;">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                                        </form> --}}
                                                                    @else
                                                                        <form
                                                                            action="{{ route('posts.approvePIC', ['id' => $posts->id]) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-success">Approve</button>
                                                                        </form>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (isset($document['type']) && $document['type'] == 'reviu')
                                                                    @if ($document['approvalReviuPIC'] == 'approved')
                                                                        @if ($document['approvalReviu'] == 'rejected')
                                                                            <button data-toggle="modal"
                                                                                data-target="#disapprovePICModal"
                                                                                class="btn btn-danger"
                                                                                type="button">Reject</button>
                                                                        @endif
                                                                        {{-- <span class="text-success">Approved</span> --}}
                                                                    @elseif ($document['approvalReviuPIC'] == 'rejected')
                                                                        <span class="badge badge-danger">Ditolak</span>
                                                                        {{-- <form action="{{ route('posts.disapprovePIC', ['id' => $posts->id, 'type' => $document['type']]) }}" method="POST" style="display:inline;">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                                        </form> --}}
                                                                    @else
                                                                        <button data-toggle="modal"
                                                                            data-target="#disapprovePICModal"
                                                                            class="btn btn-danger"
                                                                            type="button">Reject</button>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                {{ \Carbon\Carbon::parse($document['uploaded_at'])->format('d F Y') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="alert alert-danger">
                                                Data belum Tersedia.
                                            </div>
                                        @endif
                                        @php
                                            $files = [
                                                [
                                                    'name' => $posts->koreksiReviu,
                                                    'path' => 'koreksi_reviu/',
                                                    'label' => 'Dokumen Reviu',
                                                ],
                                                [
                                                    'name' => $posts->koreksiBerita,
                                                    'path' => 'koreksi_berita/',
                                                    'label' => 'Berita Acara',
                                                ],
                                                [
                                                    'name' => $posts->koreksiPengesahan,
                                                    'path' => 'koreksi_pengesahan/',
                                                    'label' => 'Lembar Pengesahan',
                                                ],
                                                [
                                                    'name' => $posts->koreksiRubrik,
                                                    'path' => 'koreksi_rubrik/',
                                                    'label' => 'Kertas Kerja',
                                                ],
                                            ];
                                            $no = 1;
                                            $filteredFiles = array_filter($files, function ($file) {
                                                return !is_null($file['name']);
                                            });
                                        @endphp
                                        @if (count($filteredFiles) > 0)
                                            Perbaikan dari Ketua :
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th scope="col">No</th>
                                                        <th colspan="2">Nama Berkas</th>
                                                        <th scope="col">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($files as $file)
                                                        @if ($file['name'])
                                                            <tr>
                                                                <td class="text-center">{{ $no++ }}</td>
                                                                <td>{{ $file['name'] }}</td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    <a href="{{ asset($file['path'] . '/' . $file['name']) }}"
                                                                        target="_blank" class="btn btn-info btn-sm"
                                                                        title="Buka Dokumen">
                                                                        <i class="fa-solid fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                                <td>{{ $file['label'] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (
                                            $posts->approvalReviu == 'approved' &&
                                                $posts->approvalBerita == 'approved' &&
                                                $posts->approvalPengesahan == 'approved' &&
                                                $posts->approvalRubrik == 'approved')
                                            @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2)
                                <tr>
                                    <th class="col-2">Print Dokumen : </th>
                                    <td>
                                        <a href="{{ route('printDetailTugas', ['id' => $posts->id]) }}" target="_blank"
                                            class="btn fa-solid fa-print bg-primary ml-2 p-2 text-white"
                                            data-toggle="tooltip" title="PRINT"></a>
                                    </td>
                                    @endif
                                    @endif
                                    </td>
                                </tr>
                                <form action="/detailTugas/{{ $posts->id }}/submit_akhir" method="POST"
                                    enctype="multipart/form-data"
                                    onsubmit="return confirm('Dokumen wajib sudah diTTD dan stempel');">
                                    @csrf
                                    <tr>
                                        <th class="col-2">Upload Laporan <p>Akhir : </th>
                                        <td>
                                            @if (
                                                $posts->approvalReviu == 'approved' &&
                                                    $posts->approvalBerita == 'approved' &&
                                                    $posts->approvalPengesahan == 'approved' &&
                                                    $posts->approvalRubrik == 'approved')
                                                @if (empty($posts->laporan_akhir))
                                                    Upload Laporan Akhir harus berformat pdf (.pdf) dan ukuran maximal 10MB
                                                    <div class="input-group mb-3">
                                                        <input type="file" name="laporan_akhir"
                                                            class="form-control m-2" id="inputGroupFile" accept=".pdf">
                                                        <button type ="submit"
                                                            class=" m-2 btn btn-md btn-primary">Upload</button>
                                                    </div>
                                                @endif
                                                @if (!empty($posts->laporan_akhir))
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th scope="col">No</th>
                                                                <th colspan="2">Nama Berkas</th>
                                                                <th scope="col">Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center">1</td>
                                                                <td>{{ $posts->laporan_akhir }}</td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    <a href="{{ asset('hasil_akhir/' . $posts->laporan_akhir) }}"
                                                                        target="_blank" class="btn btn-info btn-sm"
                                                                        title="Buka Dokumen">
                                                                        <i class="fa-solid fa-eye"></i>
                                                                    </a>
                                                                </td>

                                                                <td>Laporan Akhir</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @endif
                                            @else
                                                <div class="alert alert-danger">
                                                    Dokumen belum diaprove oleh ketua.
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </form>
                                <tr>
                                    <th class="col-2">Komentar PIC : </th>
                                    <td>
                                        <ul>
                                            @foreach ($comments as $comment)
                                                @if ($comment->type === 'pic')
                                                    <li>{{ $comment->comment }}</li>
                                                    <!-- Tambahkan informasi tambahan seperti waktu komentar atau penulis jika perlu -->
                                                @endif
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="col-2">Komentar Ketua : </th>
                                    <td>
                                        <ul>
                                            @foreach ($comments as $comment)
                                                @if ($comment->type === 'reviu')
                                                    <li>{{ $comment->comment }}</li>
                                                    <!-- Tambahkan informasi tambahan seperti waktu komentar atau penulis jika perlu -->
                                                @endif
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <script>
        document.getElementById('uploadLaporanAkhirForm').addEventListener('submit', function(event) {
            event.preventDefault();
            if (confirm("Dokumen wajib sudah distempel. Apakah Anda yakin ingin mengunggah?")) {
                this.submit();
            }
        });
    </script> --}}
            {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
        </section>
    </div>

@endsection
