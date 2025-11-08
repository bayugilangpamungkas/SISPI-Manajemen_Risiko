@extends('layout.app')

@section('title', 'MR Anggota')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>MR Anggota</h1>
        </div>
        <div class="section-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @elseif($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                        </ul>
                    </div>
            @endif
            <form method="GET" action="{{ route('entitas.index') }}" class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label for="tahun" class="mr-2">Pilih Tahun:</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Masukkan Tahun" value="{{ old('tahun', $tahun) }}">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                @if($tahun)
                    <a href="{{ route('entitas.index') }}" class="btn btn-secondary ml-2">Reset</a>
                    @endif
            </form>
            @if($elements->isEmpty())
                <p>Tidak ada elemen yang ditemukan.</p>
                @else
                    @foreach($elements as $index => $element)
                    <div class="accordion mb-3" id="accordionExample{{ $index }}">
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-header" style="background-color: #c5c8c3; color: #fff;" id="heading{{ $index }}">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#collapse{{ $index }}" aria-expanded="true" aria-controls="collapse{{ $index }}">
                                        {{ $element->elemen }}
                                    </button>
                                </h5>
                            </div>
                            <div id="collapse{{ $index }}" class="collapse" aria-labelledby="heading{{ $index }}" data-parent="#accordionExample{{ $index }}">
                                <div class="card-body">
                                    @if($element->ManagementSubElement->isEmpty())
                                        <p>Tidak ada sub elemen yang ditemukan.</p>
                                        @else
                                            @foreach($element->ManagementSubElement as $subIndex => $subElement)
                                                <div class="accordion" id="subAccordion{{ $index }}{{ $subIndex }}">
                                                    <div class="card shadow-sm border-0 rounded">
                                                        <div class="card-header" style="background-color: #d6c5b5; color: #000;" id="subHeading{{ $index }}{{ $subIndex }}">
                                                            <h6 class="mb-0">
                                                                <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#subCollapse{{ $index }}{{ $subIndex }}" aria-expanded="true" aria-controls="subCollapse{{ $index }}{{ $subIndex }}">
                                                                    {{ $subElement->sub_elemen }}
                                                                </button>
                                                            </h6>
                                                        </div>
                                                        <div id="subCollapse{{ $index }}{{ $subIndex }}" class="collapse" aria-labelledby="subHeading{{ $index }}{{ $subIndex }}" data-parent="#subAccordion{{ $index }}{{ $subIndex }}">
                                                            <div class="card-body">
                                                                @if($subElement->ManagementTopic->isEmpty())
                                                                    <p>Tidak ada topik yang ditemukan.</p>
                                                                    @else
                                                                    @php
                                                                        $allAnswersTrueForSubElement = true; 
                                                                    @endphp
                                                                        @foreach($subElement->ManagementTopic as $topicIndex => $topic)
                                                                            <div class="accordion" id="topicAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                                <div class="card shadow-sm border-0 rounded">
                                                                                    <div class="card-header" style="background-color: #e4d8cd; color: #000;" id="topicHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                                        <h6 class="mb-0">
                                                                                            <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#topicCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}" aria-expanded="true" aria-controls="topicCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                                                {{ $topic->topik }}
                                                                                            </button>
                                                                                        </h6>
                                                                                    </div>
                                                                                    <div id="topicCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}" class="collapse" aria-labelledby="topicHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}" data-parent="#topicAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                                        <div class="card-body">
                                                                                            @php
                                                                                                $previousLevelsValid = true;
                                                                                                $tahunInput = request('tahun') ?? date('Y');
                                                                                            @endphp
                                                                                            @foreach($topic->Uraian->groupBy('level') as $level => $uraians)
                                                                                                @php
                                                                                                $previousLevelsValid = true;
                                                                                                    $allPreviousLevelsFilled = true;
                                                                                                    $containsFalseAnswer = false; 
                                                                                                    $allAnswersTrue = true;
                                                                                                    $isLastLevel = ($level == $topic->Uraian->max('level')); 
                                                                                                    if ($level > 1) {
                                                                                                        for ($i = 1; $i < $level; $i++) {
                                                                                                            $allPreviousLevelsFilled = \App\Models\Jawaban::where('id_user', auth()->user()->id)
                                                                                                            ->whereIn('id_management_uraian', $topic->Uraian->where('level', $i)->pluck('id'))
                                                                                                            ->where('tahun', $tahunInput)
                                                                                                            ->where('status', 1)
                                                                                                            ->count() === $topic->Uraian->where('level', $i)->count();
                                                                                                            if (!$allPreviousLevelsFilled) {
                                                                                                                break;
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                    foreach ($topic->Uraian as $uraian) {
                                                                                                        $jawaban = \App\Models\Jawaban::where('id_user', auth()->user()->id)
                                                                                                        ->where('id_management_uraian', $uraian->id)
                                                                                                        ->where('tahun', $tahunInput)
                                                                                                        ->first();
                                                                                                        if (!$jawaban || $jawaban->status != 1) {
                                                                                                            $allAnswersTrue = false;
                                                                                                            $allAnswersTrueForSubElement = false; 
                                                                                                        }
                                                                                                        if ($jawaban && $jawaban->status == 0) {
                                                                                                            $containsFalseAnswer = true; 
                                                                                                        }
                                                                                                    }
                                                                                                @endphp
                                                                                                @if($allPreviousLevelsFilled)
                                                                                                    <div class="accordion" id="levelAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                                        <div class="card shadow-sm border-0 rounded">
                                                                                                            <div class="card-header" style="background-color: #f1ebe6; color: #000;" id="levelHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                                                <h6 class="mb-0">
                                                                                                                    <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#levelCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}" aria-expanded="true" aria-controls="levelCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                                                        Level {{ $level }}
                                                                                                                    </button>
                                                                                                                </h6>
                                                                                                            </div>
                                                                                                            <div id="levelCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}" class="collapse" aria-labelledby="levelHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}" data-parent="#levelAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                                                <div class="card-body">
                                                                                                                    <div class="table-responsive">
                                                                                                                        <form action="{{ route('entitas.update', ['id' => $level]) }}" method="POST" enctype="multipart/form-data">
                                                                                                                            @csrf
                                                                                                                            @method('PUT')
                                                                                                                            <input type="hidden" name="tahun" value="{{ old('tahun', $tahunInput) }}">
                                                                                                                            <table class="table table-striped table-hover">
                                                                                                                                <thead class="thead-light">
                                                                                                                                    <tr>
                                                                                                                                        <th class="text-center" style="width: 5%;">No.</th>
                                                                                                                                        <th class="text-center" style="width: 30%;">Uraian</th>
                                                                                                                                        <th class="text-center" style="width: 11%;">Jawaban</th>
                                                                                                                                        <th class="text-center" style="width: 20%;">Berkas</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    @foreach($uraians as $uraianIndex => $uraian)
                                                                                                                                        @php
                                                                                                                                            $jawaban = \App\Models\Jawaban::where('id_user', auth()->user()->id)
                                                                                                                                            ->where('id_management_uraian', $uraian->id)
                                                                                                                                            ->where('tahun', $tahunInput) // Filter by tahun
                                                                                                                                            ->first();
                                                                                                                                            $containsPengawasan = \App\Models\ManagementPengawasan::where('id_management_uraian', $uraian->id)->exists();
                                                                                                                                        @endphp
                                                                                                                                        <tr>
                                                                                                                                            <td style="width: 5%;">{{ $uraianIndex + 1 }}</td>
                                                                                                                                            <td style="width: 30%;">
                                                                                                                                                {{ $uraian->uraian }}
                                                                                                                                                @if($jawaban && $jawaban->validasi_at)
                                                                                                                                                    <p class="mt-3 mb-1">
                                                                                                                                                        <span class="badge badge-success" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: bold;">
                                                                                                                                                            Tervalidasi
                                                                                                                                                        </span>
                                                                                                                                                    </p>
                                                                                                                                                    @endif
                                                                                                                                                </td>
                                                                                                                                                <td style="width: 11%;">
                                                                                                                                                    @if(!$containsPengawasan)
                                                                                                                                                        @if($jawaban && $jawaban->validasi_at)
                                                                                                                                                            <p class="font-weight text-center mt-1 mb-0">
                                                                                                                                                                {{ $jawaban->status == 1 ? 'Y' : 'T' }}
                                                                                                                                                            </p>
                                                                                                                                                            <p class="text-primary font-weight-bold mt-1 mb-0" style="font-size: 0.85rem;">
                                                                                                                                                                {{ $jawaban->tahun }}
                                                                                                                                                            </p>
                                                                                                                                                            @else
                                                                                                                                                                <select name="jawaban[{{ $uraian->id }}]" class="form-control custom-select">
                                                                                                                                                                    <option value="1" {{ ($jawaban && $jawaban->status == 1) ? 'selected' : '' }}>Y</option>
                                                                                                                                                                    <option value="0" {{ ($jawaban && $jawaban->status == 0) ? 'selected' : '' }}>T</option>
                                                                                                                                                                    @if(is_null($jawaban))
                                                                                                                                                                        <option selected disabled>Pilih Jawaban</option>
                                                                                                                                                                    @endif
                                                                                                                                                                </select>
                                                                                                                                                                @if($jawaban)
                                                                                                                                                                    <p class="text-primary font-weight-bold mt-1 mb-0" style="font-size: 0.85rem;">
                                                                                                                                                                        {{ $jawaban->tahun }}
                                                                                                                                                                    </p>
                                                                                                                                                                    @else
                                                                                                                                                                        <p class="text-muted font-weight-bold mt-1 mb-0" style="font-size: 0.85rem;">Belum ada pembaruan</p>
                                                                                                                                                                @endif
                                                                                                                                                        @endif
                                                                                                                                                        @else
                                                                                                                                                            @if($jawaban)
                                                                                                                                                                <p class="font-weight text-center mt-1 mb-0">
                                                                                                                                                                    {{ $jawaban->status == 1 ? 'Y' : 'T' }}
                                                                                                                                                                </p>
                                                                                                                                                                @else
                                                                                                                                                                    <p class="text-muted font-weight mt-1 mb-0">A. Pengawasan</p>
                                                                                                                                                            @endif
                                                                                                                                                    @endif
                                                                                                                                                </td>
                                                                                                                                                <td style="width: 20%;" class="text-center">
                                                                                                                                                    @if(!$containsPengawasan)
                                                                                                                                                        @if($jawaban && $jawaban->validasi_at)
                                                                                                                                                            @if($jawaban->dokumen)
                                                                                                                                                                <a href="{{ asset('storage/' . $jawaban->dokumen) }}" target="_blank" class="btn btn-sm btn-info mb-2">
                                                                                                                                                                    <i class="fas fa-download"></i> Download
                                                                                                                                                                </a>
                                                                                                                                                                @endif
                                                                                                                                                                @else
                                                                                                                                                                    @if($jawaban && $jawaban->dokumen)
                                                                                                                                                                        <a href="{{ asset('storage/' . $jawaban->dokumen) }}" target="_blank" class="btn btn-sm btn-info mb-2">
                                                                                                                                                                            <i class="fas fa-download"></i> Download
                                                                                                                                                                        </a>
                                                                                                                                                                    @endif
                                                                                                                                                                    <input type="file" name="berkas[{{ $uraian->id }}]" class="form-control form-control-sm mt-1" />
                                                                                                                                                            @endif
                                                                                                                                                            @else
                                                                                                                                                                @if($jawaban && $jawaban->dokumen)
                                                                                                                                                                    <a href="{{ asset('storage/' . $jawaban->dokumen) }}" target="_blank" class="btn btn-sm btn-info mb-2">
                                                                                                                                                                        <i class="fas fa-download"></i> Download
                                                                                                                                                                    </a>
                                                                                                                                                                @endif
                                                                                                                                                                <p class="text-muted font-weight mt-1 mb-0">Pengawasan</p>
                                                                                                                                                        @endif
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                    @endforeach
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                            <div class="d-flex justify-content-end mt-3">
                                                                                                                                <button type="submit" class="btn btn-primary btn-icon icon-right">
                                                                                                                                    <i class="fas fa-save"></i> Simpan
                                                                                                                                </button>
                                                                                                                            </div>
                                                                                                                        </form>
                                                                                                                    </div>
                                                                                                                    @php
                                                                                                                        $containsPengawasan = \App\Models\ManagementPengawasan::where('id_management_uraian', $uraian->id)->exists();
                                                                                                                    @endphp
                                                                                                                    @if($containsPengawasan)
                                                                                                                        <div class="mt-3 ">
                                                                                                                            <div class="d-flex justify-content-end">
                                                                                                                                <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#pengawasanTable{{ $uraian->id }}">Lihat Aktivitas Pengawasan</button>
                                                                                                                            </div>
                                                                                                                            
                                                                                                                            <div class="collapse mt-2" id="pengawasanTable{{ $uraian->id }}">
                                                                                                                                <div class="table-responsive">
                                                                                                                                    @php
                                                                                                                                        $pengawasan = \App\Models\ManagementPengawasan::where('id_management_uraian', $uraian->id)
                                                                                                                                        ->get();
                                                                                                                                        $firstPengawasan = $pengawasan->first(); 
                                                                                                                                    @endphp
                                                                                                                                    @if($firstPengawasan)
                                                                                                                                        <p><strong>Kualitas Pengawasan:</strong> {{ $firstPengawasan->kualitas_pengawasan }}</p>
                                                                                                                                        <p><strong>Aktivitas Pengawasan:</strong> {{ $firstPengawasan->aktivitas_pengawasan }}</p>
                                                                                                                                    @endif
                                                                                                                                    <form action="{{ route('jawaban.store') }}" method="POST">
                                                                                                                                        @csrf
                                                                                                                                        <input type="hidden" name="id_management_uraian" value="{{ $uraian->id }}">
                                                                                                                                        <input type="hidden" name="id_user" value="{{ auth()->id() }}">
                                                                                                                                        <input type="hidden" name="tahun" value="{{ $tahun }}"> 
                                                                                                                                        <table class="table table-bordered">
                                                                                                                                            <thead class="thead-light">
                                                                                                                                                <tr>
                                                                                                                                                    <th>No.</th>
                                                                                                                                                    <th>Parameter</th>
                                                                                                                                                    <th>Sub Parameter</th>
                                                                                                                                                    <th>Cara Pengukuran</th>
                                                                                                                                                    <th>Aksi</th>
                                                                                                                                                </tr>
                                                                                                                                            </thead>
                                                                                                                                            <tbody>
                                                                                                                                                @if($pengawasan->isEmpty())
                                                                                                                                                    <tr>
                                                                                                                                                        <td colspan="5">Tidak ada aktivitas pengawasan untuk uraian ini.</td>
                                                                                                                                                    </tr>
                                                                                                                                                    @else
                                                                                                                                                        @foreach($pengawasan as $index => $item)
                                                                                                                                                            @php
                                                                                                                                                                $existingJawaban = \App\Models\JawabanKP::where('id_management_pengawasan', $item->id)
                                                                                                                                                                ->where('tahun', $tahunInput) // Filter jawaban berdasarkan tahun
                                                                                                                                                                ->first();
                                                                                                                                                            @endphp
                                                                                                                                                            <tr>
                                                                                                                                                                <td>{{ $index + 1 }}</td>
                                                                                                                                                                <td>{{ $item->parameter }}</td>
                                                                                                                                                                <td>{{ $item->sub_parameter }}</td>
                                                                                                                                                                <td>{{ $item->cara_pengukuran }}</td>
                                                                                                                                                                <td>
                                                                                                                                                                    <button type="button" class="btn btn-warning" data-toggle="collapse" data-target="#formJawab{{ $item->id }}"><i class="fa fa-edit"></i>
                                                                                                                                                                        Jawab
                                                                                                                                                                    </button>
                                                                                                                                                                </td>
                                                                                                                                                            </tr>
                                                                                                                                                            <tr class="collapse" id="formJawab{{ $item->id }}">
                                                                                                                                                                <td colspan="5">
                                                                                                                                                                    <div class="form-group">
                                                                                                                                                                        <label for="nilai_{{ $item->id }}">Simpulan dan Nilai</label>
                                                                                                                                                                        <textarea name="jawaban[{{ $item->id }}][nilai]" id="nilai_{{ $item->id }}" class="form-control" rows="2">{{ old('jawaban.' . $item->id . '.nilai', $existingJawaban ? $existingJawaban->nilai : '') }}</textarea>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="form-group">
                                                                                                                                                                        <label for="evaluator_{{ $item->id }}">Penilaian Kualitatif Penilai/Evaluator</label>
                                                                                                                                                                        <textarea name="jawaban[{{ $item->id }}][evaluator]" id="evaluator_{{ $item->id }}" class="form-control" rows="2">{{ old('jawaban.' . $item->id . '.evaluator', $existingJawaban ? $existingJawaban->evaluator : '') }}</textarea>
                                                                                                                                                                    </div>
                                                                                                                                                                    <input type="hidden" name="jawaban[{{ $item->id }}][id_management_pengawasan]" value="{{ $item->id }}">
                                                                                                                                                                    <input type="hidden" name="jawaban[{{ $item->id }}][id_user]" value="{{ auth()->id() }}">
                                                                                                                                                                    <div class="d-flex justify-content-end mt-3">
                                                                                                                                                                        <button type="submit" class="btn btn-primary btn-icon icon-right">
                                                                                                                                                                            <i class="fas fa-save"></i> Simpan
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>
                                                                                                                                                                </td>
                                                                                                                                                            </tr>
                                                                                                                                                        @endforeach
                                                                                                                                                @endif
                                                                                                                                            </tbody>
                                                                                                                                        </table>
                                                                                                                                    </form>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    @endif
                                                                                                                    @if($containsFalseAnswer)
                                                                                                                        <div class="mt-4">
                                                                                                                            <h5>Formulir Simpulan</h5>
                                                                                                                            <form action="{{ route('simpulan.update') }}" method="POST">
                                                                                                                                @csrf
                                                                                                                                @php
                                                                                                                                    $tahunInput = request('tahun') ?? date('Y'); // Ambil tahun dari input user
                                                                                                                                    $existingSimpulan = \App\Models\Simpulan::where('id_management_topic', $topic->id)
                                                                                                                                    ->where('tahun', $tahunInput) // Gunakan tahun dari input user
                                                                                                                                    ->first();
                                                                                                                                @endphp
                                                                                                                                <input type="hidden" name="id_management_topic" value="{{ $topic->id }}">
                                                                                                                                <input type="hidden" name="tahun" value="{{ $tahunInput }}"> {{-- Tambahkan tahun input user ke dalam form --}}
                                                                                                                                <div class="form-group">
                                                                                                                                    <label for="simpulan">Simpulan Pemenuhan Topik</label>
                                                                                                                                    <textarea name="simpulan" id="simpulan" class="form-control" rows="4" required>{{ old('simpulan', $existingSimpulan ? $existingSimpulan->simpulan : '') }}</textarea>
                                                                                                                                </div>
                                                                                                                                <div class="form-group">
                                                                                                                                    <label for="improvement">Area of Improvement Topic</label>
                                                                                                                                    <textarea name="improvement" id="improvement" class="form-control" rows="4" required>{{ old('improvement', $existingSimpulan ? $existingSimpulan->improvement : '') }}</textarea>
                                                                                                                                </div>
                                                                                                                                <div class="d-flex justify-content-end mt-1">
                                                                                                                                    <button type="submit" class="btn btn-primary btn-icon icon-right"><i class="fas fa-save"></i> Simpan Simpulan</button>
                                                                                                                                </div>
                                                                                                                            </form>
                                                                                                                        </div>
                                                                                                                    @endif
                                                                                                                    @if($allAnswersTrue)
                                                                                                                        <div class="mt-4">
                                                                                                                            <form action="{{ route('simpulan.update') }}" method="POST">
                                                                                                                                @csrf
                                                                                                                                @php
                                                                                                                                    $existingSimpulan = \App\Models\Simpulan::where('id_management_topic', $topic->id)->first();
                                                                                                                                @endphp
                                                                                                                                <input type="hidden" name="id_management_topic" value="{{ $topic->id }}">
                                                                                                                                <div class="form-group">
                                                                                                                                    <label for="simpulan">Simpulan Pemenuhan Topik</label>
                                                                                                                                    <textarea name="simpulan" id="simpulan" class="form-control" rows="4" required>{{ old('simpulan', $existingSimpulan ? $existingSimpulan->simpulan : '') }}</textarea>
                                                                                                                                </div>
                                                                                                                                <div class="form-group">
                                                                                                                                    <label for="improvement">Area of Improvement Topic</label>
                                                                                                                                    <textarea name="improvement" id="improvement" class="form-control" rows="4" required>{{ old('improvement', $existingSimpulan ? $existingSimpulan->improvement : '') }}</textarea>
                                                                                                                                </div>
                                                                                                                                <div class="d-flex justify-content-end mt-1">
                                                                                                                                    <button type="submit" class="btn btn-primary btn-icon icon-right"><i class="fas fa-save"></i> Simpan Improvement</button>
                                                                                                                                    @if($isLastLevel) {{-- Only show export button at the last level --}}
                                                                                                                                        <a href="{{ route('export.excel', ['topic_id' => $topic->id]) }}" class="btn btn-success btn-icon icon-right ml-2">
                                                                                                                                            <i class="fas fa-file-excel"></i> Export Excel
                                                                                                                                        </a>
                                                                                                                                    @endif
                                                                                                                                </div>
                                                                                                                            </form>
                                                                                                                        </div>
                                                                                                                    @endif
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                        @if($allAnswersTrueForSubElement) 
                                                                            <div class="d-flex justify-content-end mt-4">
                                                                                <a href="{{ route('exportSubElemen.excel', ['sub_element_id' => $subElement->id]) }}" class="btn btn-success btn-icon icon-right">
                                                                                    <i class="fas fa-file-excel"></i> Export Excel
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>
</div>
<script>
    setTimeout(function() {
        let alertElement = document.querySelector('.alert');
        if (alertElement) {
            alertElement.style.transition = 'opacity 1s';
            alertElement.style.opacity = '0';
            setTimeout(() => alertElement.remove(), 1000);  
        }
    }, 2000);
    document.addEventListener('DOMContentLoaded', function () {
        const openedAccordions = JSON.parse(localStorage.getItem('openedAccordions')) || [];
        openedAccordions.forEach(id => {
            const accordion = document.getElementById(id);
            if (accordion) {
                new bootstrap.Collapse(accordion, {
                    toggle: false
                }).show();
            }
        });

        document.querySelectorAll('[data-toggle="collapse"]').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target').substring(1); 
                const accordion = document.getElementById(targetId);
                const openedAccordions = JSON.parse(localStorage.getItem('openedAccordions')) || [];

                if (accordion.classList.contains('show')) {
                    const index = openedAccordions.indexOf(targetId);
                    if (index > -1) {
                        openedAccordions.splice(index, 1);
                    }
                } else {
                    if (!openedAccordions.includes(targetId)) {
                        openedAccordions.push(targetId);
                    }
                }

                localStorage.setItem('openedAccordions', JSON.stringify(openedAccordions));
            });
        });
    });
    </script>
@endsection