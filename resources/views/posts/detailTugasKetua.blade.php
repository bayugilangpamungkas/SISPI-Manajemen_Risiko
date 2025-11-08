@extends('layout.app')
@section('title', 'Detail Tugas Ketua')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Detail Tugas Ketua</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">Detail Penugasan</span>
                </h4>
                {{-- <div class=" mb-2 ">
            <a href="/detailTugas/print/{{ $posts->id }}" target="_blank" class="btn fa-solid fa-print bg-primary p-2 text-white" data-toggle="tooltip" title="PRINT"></a>
        </div> --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">

                                <table class="table table-white table-sm table-responsive">
                                    <tr>
                                        <th class="col-2">Unit Kerja :</th>
                                        <td>{{ $posts->unitKerja->nama_unit_kerja }}</td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Waktu : </th>
                                        <td>
                                            <i class="fa-regular fa-calendar-days mr-1" style="color: #0050db;"></i>
                                            {{ $posts->waktu }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Tempat : </th>
                                        <td>
                                            <i class="fa-regular fa-building mr-1" style="color: #0050db;"></i>
                                            {{ $posts->tempat }}
                                        </td>
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
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right"> </th>
                                        <td></td>
                                    </tr>
                                    </form>

                                    {{-- @php
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

                        @endphp --}}

                                    {{-- <div class="alert {{ $isPastDue ? 'alert-danger' : 'alert-warning' }}"
                                        style="{{ $isPastDue ? 'background-color: red !important;' : '' }}">
                                        <strong>{{ $isPastDue ? 'Peringatan!' : 'Informasi' }}</strong>
                                        Tenggat waktu tugas ini adalah 2 minggu sejak ditugaskan.
                                        @if ($isPastDue)
                                            <br> <em>Tenggat waktu sudah terlewati.</em>
                                        @endif
                                    </div> --}}
                                    <tr>
                                        <th class="text-left bg-success p-1" style="color:white">Dokumen yang telah
                                            dikumpulkan : </th>
                                        <td class="text-right bg-success p-1"></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2">Dokumen Pengumpulan : </th>
                                        <td>
                                            @php
                                                $documents = [
                                                    [
                                                        'name' => $posts->hasilReviu,
                                                        'path' => 'hasil_reviu',
                                                        'label' => 'Dokumen Reviu',
                                                        'approval' => $posts->approvalReviu,
                                                        'type' => 'reviu',
                                                        'approval_at' => $posts->approvalReviu_at,
                                                        'uploaded_at' => $posts->hasilReviu_uploaded_at,
                                                        'approvalReviuPIC' => $posts->approvalReviuPIC, // Tambahkan field ini
                                                    ],
                                                    [
                                                        'name' => $posts->hasilBerita,
                                                        'path' => 'hasil_berita',
                                                        'label' => 'Berita Acara',
                                                        'approval' => $posts->approvalBerita,
                                                        'type' => 'berita',
                                                        'approval_at' => $posts->approvalBerita_at,
                                                        'uploaded_at' => $posts->hasilBerita_uploaded_at,
                                                    ],
                                                    [
                                                        'name' => $posts->hasilPengesahan,
                                                        'path' => 'hasil_pengesahan',
                                                        'label' => 'Lembar Pengesahan',
                                                        'approval' => $posts->approvalPengesahan,
                                                        'type' => 'pengesahan',
                                                        'approval_at' => $posts->approvalPengesahan_at,
                                                        'uploaded_at' => $posts->hasilPengesahan_uploaded_at,
                                                    ],
                                                    [
                                                        'name' => $posts->hasilRubrik,
                                                        'path' => 'hasil_rubrik',
                                                        'label' => 'Kertas Kerja',
                                                        'approval' => $posts->approvalRubrik,
                                                        'type' => 'rubrik',
                                                        'approval_at' => $posts->approvalRubrik_at,
                                                        'uploaded_at' => $posts->hasilRubrik_uploaded_at,
                                                    ],
                                                ];

                                                $filteredDocuments = array_filter($documents, function ($document) {
                                                    // Tambahkan pengecekan approveReviuPIC untuk tipe reviu
                                                    return !is_null($document['name']) &&
                                                        ($document['type'] != 'reviu' ||
                                                            $document['approvalReviuPIC'] == 'approved');
                                                });
                                            @endphp

                                            @if (count($filteredDocuments) > 0)
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th scope="col">No</th>
                                                            <th colspan="2">Nama Berkas</th>
                                                            <th scope="col">Keterangan</th>
                                                            <th scope="col">Waktu Pengumpulan</th>
                                                            <th colspan="3" scope="col">Approving</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $no = 1; @endphp
                                                        @foreach ($filteredDocuments as $document)
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
                                                                <td class="text-center">
                                                                    {{ \Carbon\Carbon::parse($document['uploaded_at'])->format('d F Y') }}
                                                                </td>
                                                                <td>
                                                                    @if ($document['approval'] == 'approved')
                                                                       Disetujui pada tanggal {{ \Carbon\Carbon::parse($document['approval_at'])->format('d F Y') }}
                                                                    @endif
                                                                    @if ((Auth::user()->id_level == 1 || Auth::user()->id_level == 3) && $document['approval'] != 'approved' && $document['approval'] != 'rejected')
                                                                        <form
                                                                            action="{{ route('posts.approve', ['id' => $posts->id, 'type' => $document['type']]) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-success">Approve</button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($document['approval'] == 'rejected')
                                                                       Ditolak pada tanggal {{ \Carbon\Carbon::parse($document['approval_at'])->format('d F Y') }}
                                                                    @endif
                                                                    @if ((Auth::user()->id_level == 1 || Auth::user()->id_level == 3) && $document['approval'] != 'rejected' && $document['approval'] != 'approved')
                                                                        <form
                                                                            action="{{ route('posts.disapprove', ['id' => $posts->id, 'type' => $document['type']]) }}"
                                                                            method="POST" style="display:inline;">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Reject</button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-danger">
                                                    Dokumen belum disubmit oleh PIC.
                                                </div>
                                            @endif
                                        </td>

                                    </tr>

                                    @if (
                                        $posts->approvalReviu != 'approved' &&
                                            $posts->approvalBerita != 'approved' &&
                                            $posts->approvalPengesahan != 'approved' &&
                                            $posts->approvalRubrik != 'approved')
                                        <th class="text-center bg-warning p-1" style="color:white">Perbaikan : </th>
                                        <td class="text-right bg-warning p-1"></td>
                                    @endif

                                    <td>
                                        @if ($posts->approvalReviu != 'approved')
                                            <tr>
                                                <th class="col-2">Upload Perbaikan Reviu: </th>
                                                <td>
                                                    Upload Perbaikan Reviu wajib berformat word (.doc / .docx) maks 10MB
                                                    <form action="/detailTugasKetua/{{ $posts->id }}/koreksi_ketua"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="file_type" value="koreksiReviu">
                                                        <div class="input-group mb-3">
                                                            <input type="file" name="koreksiReviu"
                                                                class="form-control m-2" id="inputGroupFile">
                                                            <button type="submit"
                                                                class="m-2 btn btn-md btn-primary">Upload</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($posts->approvalBerita != 'approved')
                                            <tr>
                                                <th class="col-2">Upload Perbaikan Berita Acara: </th>
                                                <td>
                                                    Upload Perbaikan Berita Acara wajib berformat word (.doc / .docx) maks
                                                    10MB
                                                    <form action="/detailTugasKetua/{{ $posts->id }}/koreksi_ketua"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="file_type" value="koreksiBerita">
                                                        <div class="input-group mb-3">
                                                            <input type="file" name="koreksiBerita"
                                                                class="form-control m-2" id="inputGroupFile">
                                                            <button type="submit"
                                                                class="m-2 btn btn-md btn-primary">Upload</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($posts->approvalPengesahan != 'approved')
                                            <tr>
                                                <th class="col-2">Upload Perbaikan Lembar Pengesahan: </th>
                                                <td>
                                                    Upload Perbaikan Lembar Pengesahan wajib berformat word (.doc / .docx)
                                                    maks 10MB
                                                    <form action="/detailTugasKetua/{{ $posts->id }}/koreksi_ketua"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="file_type" value="koreksiPengesahan">
                                                        <div class="input-group mb-3">
                                                            <input type="file" name="koreksiPengesahan"
                                                                class="form-control m-2" id="inputGroupFile">
                                                            <button type="submit"
                                                                class="m-2 btn btn-md btn-primary">Upload</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($posts->approvalRubrik != 'approved')
                                            <tr>
                                                <th class="col-2">Upload Perbaikan Kertas Kerja: </th>
                                                <td>
                                                    Upload Perbaikan Kertas Kerja wajib berformat excel (.xls / .xlsx) maks
                                                    10MB
                                                    <form action="/detailTugasKetua/{{ $posts->id }}/koreksi_ketua"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="file_type" value="koreksiRubrik">
                                                        <div class="input-group mb-3">
                                                            <input type="file" name="koreksiRubrik"
                                                                class="form-control m-2" id="inputGroupFile">
                                                            <button type="submit"
                                                                class="m-2 btn btn-md btn-primary">Upload</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    </td>

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
                                    @endphp
                                    <tr>
                                        <th class="col-2">Dokumen Perbaikan: </th>
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
                                        </td>
                                    </tr>
                                    </tr>
                                    </td>
                                    <th class="col-2">Komentar : </th>
                                    <td>
                                        @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 3)
                                            <form
                                                action="{{ route('posts.comment.store', ['id' => $posts->id, 'type' => 'reviu']) }}"
                                                method="POST">
                                                @csrf
                                                <div class="input-group mb-2">
                                                    <textarea name="comment" rows="3" cols="50" placeholder="Masukkan komentar"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm mt-0 mb-2">Kirim
                                                    Komentar</button>
                                            </form>
                                        @endif
                                    </td>
                                    <tr>
                                        <th class="col-2">Laporan Akhir : </th>
                                        <td>
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

                                                {{-- <button type="submit" class="ml-2 mb-2 btn btn-md btn-primary">SIMPAN</button> --}}
                                            @else
                                                <div class="alert alert-danger">
                                                    Dokumen laporan akhir belum disubmit oleh Admin.
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
