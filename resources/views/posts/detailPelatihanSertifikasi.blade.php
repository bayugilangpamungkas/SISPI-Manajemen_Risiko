@extends('layout.app')
@section('title', 'Detail Tugas')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Detail Tugas</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">Detail Penugasan</span>
                </h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">

                                <form action="/detailTugas/{{ $posts->id }}" method="GET"
                                    enctype="multipart/form-data">

                                    @csrf
                                    <table class="table table-white table-sm">
                                        <tbody>
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
                                                $dateString = str_replace(
                                                    array_keys($days),
                                                    array_values($days),
                                                    $dateString,
                                                );
                                                $dateString = str_replace(
                                                    array_keys($months),
                                                    array_values($months),
                                                    $dateString,
                                                );

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
                                                <th class="col-2">Surat Tugas : </th>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>{{ isset($posts->suratTugas) && $posts->suratTugas ? $posts->suratTugas : 'Belum diupload oleh admin' }}
                                                                </td>
                                                                <td>
                                                                    <!-- Tambahkan tombol atau tautan untuk membuka dokumen -->
                                                                    @if (isset($posts->suratTugas))
                                                                        <a href="{{ asset('surat_tugas/' . $posts->suratTugas) }}"
                                                                            target="_blank" class="btn btn-info btn-sm"
                                                                            title="Buka Dokumen">
                                                                            <i class="fa-solid fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="col-2">Template Dokumen Tindak Lanjut : </th>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
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
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-right"> </th>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                                <table>
                                    <tbody>
                                        <tr>
                                            <th class="col-2">Peserta Pelatihan / Sertifikasi : </th>
                                            <td>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th scope="col">No</th>
                                                            <th scope="col">Nama Anggota</th>
                                                            <th scope="col">Sertifikat</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $no = 1;
                                                        @endphp
                                                        @foreach ($sertifikat as $item)
                                                            <tr>
                                                                <td class="text-center">{{ $no++ }}</td>
                                                                <td>{{ $item->user->name }}</td>
                                                                <td>
                                                                    @if (Auth::user()->id == $item->id_user || Auth::user()->id_level == 1)
                                                                        @if ($item->sertifikat == null)
                                                                            <form
                                                                                action="{{ route('uploadSertifikat', $item->id) }}"
                                                                                method="POST"
                                                                                enctype="multipart/form-data">
                                                                                @csrf
                                                                                <div class="input-group mb-3">
                                                                                    <input type="file" name="sertifikat"
                                                                                        class="form-control m-2"
                                                                                        id="inputGroupFile"
                                                                                        accept=".doc, .docx, .pdf">
                                                                                    <button type="submit"
                                                                                        class=" m-2 btn btn-md btn-primary">Upload</button>
                                                                                </div>
                                                                            </form>
                                                                        @else
                                                                            {{ $item->sertifikat }}
                                                                            <a href="{{ asset('sertifikat/' . $item->sertifikat) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-sm ml-2"
                                                                                title="Buka Dokumen">
                                                                                <i class="fa-solid fa-eye"></i>
                                                                            </a>
                                                                            <form
                                                                                action="{{ route('uploadSertifikat', $item->id) }}"
                                                                                method="POST"
                                                                                enctype="multipart/form-data">
                                                                                @csrf
                                                                                <div class="input-group mb-3">
                                                                                    <input type="file" name="sertifikat"
                                                                                        class="form-control m-2"
                                                                                        id="inputGroupFile"
                                                                                        accept=".doc, .docx, .pdf">
                                                                                    <button type="submit"
                                                                                        class=" m-2 btn btn-md btn-primary">Upload
                                                                                        Ulang</button>
                                                                                </div>
                                                                            </form>
                                                                        @endif
                                                                    @else
                                                                        {{ $item->sertifikat }}
                                                                        @if ($item->sertifikat != null)
                                                                            <a href="{{ asset('sertifikat/' . $item->sertifikat) }}"
                                                                                target="_blank"
                                                                                class="btn btn-info btn-sm ml-2"
                                                                                title="Buka Dokumen">
                                                                                <i class="fa-solid fa-eye"></i>
                                                                            </a>
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2)
                                    @if ($posts->suratTugas == null)
                                        <tr>
                                            <td>
                                                Upload Surat Tugas (.doc / .docx / .pdf)</br>
                                                <small>
                                                    Ukuran maksimal 10MB *
                                                </small>
                                                <form action="{{ route('uploadSuratTugas', $posts->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- <input type="hidden" name="file_type" value="hasilReviu"> --}}
                                                    <div class="input-group mb-3">
                                                        <input type="file" name="surat_tugas" class="form-control m-2"
                                                            id="inputGroupFile" accept=".doc, .docx, .pdf">
                                                        <button type="submit"
                                                            class=" m-2 btn btn-md btn-primary">Upload</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>
                                                Upload Surat Tugas (.doc / .docx / .pdf)</br>
                                                <small>
                                                    Ukuran maksimal 10MB *
                                                </small>
                                                <form action="{{ route('uploadSuratTugas', $posts->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- <input type="hidden" name="file_type" value="hasilReviu"> --}}
                                                    <div class="input-group mb-3">
                                                        <input type="file" name="surat_tugas" class="form-control m-2"
                                                            id="inputGroupFile" accept=".doc, .docx, .pdf">
                                                        <button type="submit" class=" m-2 btn btn-md btn-primary">Upload
                                                            Ulang</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($posts->dokumen_tindak_lanjut == null)
                                        <tr>
                                            <td>
                                                Upload Dokumen Tindak Lanjut (.doc / .docx / .pdf)</br>
                                                <small>
                                                    Ukuran maksimal 10MB *
                                                </small>
                                                <form action="{{ route('storeTindakLanjut', $posts->id) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- <input type="hidden" name="file_type" value="hasilReviu"> --}}
                                                    <div class="input-group mb-3">
                                                        <input type="file" name="dokumen_tindak_lanjut"
                                                            class="form-control m-2" id="inputGroupFile"
                                                            accept=".doc, .docx, .pdf">
                                                        <button type="submit"
                                                            class=" m-2 btn btn-md btn-primary">Upload</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>
                                                Upload Dokumen Tindak Lanjut (.doc / .docx / .pdf)</br>
                                                <small>
                                                    Ukuran maksimal 10MB *
                                                </small>
                                                <form action="{{ route('storeTindakLanjut', $posts->id) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    {{-- <input type="hidden" name="file_type" value="hasilReviu"> --}}
                                                    <div class="input-group mb-3">
                                                        <input type="file" name="dokumen_tindak_lanjut"
                                                            class="form-control m-2" id="inputGroupFile"
                                                            accept=".doc, .docx, .pdf">
                                                        <button type="submit" class=" m-2 btn btn-md btn-primary">Upload
                                                            Ulang</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endif

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
