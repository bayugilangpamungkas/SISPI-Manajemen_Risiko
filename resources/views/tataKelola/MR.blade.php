@extends('layout.app')

@section('title', 'MR')

@section('main')
@foreach($elements as $element)
    @foreach($element->ManagementSubElement as $subElement)
        @foreach($subElement->ManagementTopic as $topic)
            @foreach($topic->Uraian as $uraian)
                <div class="modal fade" id="editUraianModal{{ $uraian->id }}" tabindex="-1" aria-labelledby="editUraianModalLabel{{ $uraian->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="background-color: #fff; color: #000;">
                            <div class="modal-header" style="border-bottom: 1px solid #000;">
                                <h5 class="modal-title" id="editUraianModalLabel{{ $uraian->id }}">Edit Uraian</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: #000;"></button>
                            </div>
                            <form action="{{ route('uraian.update', $uraian->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body" style="background-color: #fff; color: #000;">
                                    <div class="form-group mt-3">
                                        <label for="level" style="color: #000;">Level</label>
                                        <input type="number" class="form-control" id="level" name="level" min="1" value="{{ $uraian->level }}" required style="background-color: #fff; color: #000;">
                                    </div> 
                                    <div class="form-group">
                                        <label for="uraian" style="color: #000;">Uraian</label>
                                        <textarea class="form-control" id="uraian" name="uraian" rows="7" required style="background-color: #fff; color: #000;">{{ $uraian->uraian }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer" style="background-color: #fff;">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                                    <button type="submit" class="btn btn-primary" >Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    @endforeach
@endforeach

<div class="modal fade" id="addSubElementModal" tabindex="-1" role="dialog" aria-labelledby="addSubElementModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #fff; color: #000;">
                    <h5 class="modal-title" id="addSubElementModalLabel">Tambah Sub Elemen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('subElemen.store') }}" method="POST">
                    @csrf
                    <div class="modal-body" style="background-color: #fff;">
                        <div class="form-group">
                            <label for="element">Pilih Elemen:</label>
                            <select class="form-control" id="element" name="id_management_element" required>
                                <option value="" disabled selected>Pilih Elemen</option>
                                @foreach($elements as $element)
                                    <option value="{{ $element->id }}">{{ $element->elemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sub_element">Sub Elemen:</label>
                            <textarea class="form-control" id="sub_element" name="sub_elemen" rows="5" required></textarea></div>
                        </div>
                        <div class="modal-footer" style="background-color: #fff;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                            <button type="submit" class="btn btn-primary" >Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="addTopicModal" tabindex="-1" role="dialog" aria-labelledby="addTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fff; color: #000;">
                <h5 class="modal-title" id="addTopicModalLabel">Tambah Topik</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('topic.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="background-color: #fff;">
                    <div class="form-group">
                        <label for="elementSelect">Pilih Elemen:</label>
                        <select class="form-control" id="elementSelect" name="id_management_element" required>
                            <option value="" disabled selected>Pilih Elemen</option>
                            @foreach($elements as $element)
                                <option value="{{ $element->id }}">{{ $element->elemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subElementSelect">Pilih Sub Elemen:</label>
                        <select class="form-control" id="subElementSelect" name="id_management_sub_element" required>
                            @foreach($subElements as $subElement)
                                <option value="{{ $subElement->id }}" data-element-id="{{ $subElement->id_management_element }}">
                                    {{ $subElement->sub_elemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="topic">Topik:</label>
                        <textarea class="form-control" id="topic" name="topik" rows="5" required></textarea>

                    </div>
                </div>
                <div class="modal-footer" style="background-color: #fff;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                    <button type="submit" class="btn btn-primary" >Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addUraianModal" tabindex="-1" role="dialog" aria-labelledby="addUraianModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fff; color: #000;">
                <h5 class="modal-title" id="addUraianModalLabel">Tambah Uraian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('uraian.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="background-color: #f8f9fa;">
                    <div class="form-group">
                        <label for="elementUraian">Pilih Elemen:</label>
                        <select class="form-control" id="elementUraian" name="id_management_element" required>
                            <option value="" disabled selected>Pilih Elemen</option>
                            @foreach($elements as $element)
                                <option value="{{ $element->id }}">{{ $element->elemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subElementUraian">Pilih Sub Elemen:</label>
                        <select class="form-control" id="subElementUraian" name="id_management_sub_element" required>
                            @foreach($subElements as $subElement)
                                <option value="{{ $subElement->id }}" data-element-id="{{ $subElement->id_management_element }}">
                                    {{ $subElement->sub_elemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="topicUraian">Pilih Topik:</label>
                        <select class="form-control" id="topicUraian" name="id_management_topic" required>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" data-sub-element-id="{{ $topic->id_management_sub_element }}">
                                    {{ $topic->topik }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="level">Level:</label>
                        <input type="number" class="form-control" id="level" name="level" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="uraian">Uraian:</label>
                        <textarea class="form-control" id="uraian" name="uraian" rows="5" required></textarea>

                    </div>
                </div>
                <div class="modal-footer" style="background-color: #fff;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                    <button type="submit" class="btn btn-primary" >Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addElementModal" tabindex="-1" aria-labelledby="addElementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #fff; color: #000;">
            <div class="modal-header" style="border-bottom: 1px solid #000;">
                <h5 class="modal-title" id="addElementModalLabel">Tambah Elemen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #000;">&times;</span>
                </button>
            </div>
            <form action="{{ route('MR.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="background-color: #fff; color: #000;">
                    <div class="form-group">
                        <label for="elemen" style="color: #000;">Elemen</label>
                        <textarea name="elemen" class="form-control" rows="5" required style="background-color: #fff; color: #000;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #fff;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                    <button type="submit" class="btn btn-primary" >Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($elements as $element)
<div class="modal fade" id="editElementModal{{ $element->id }}" tabindex="-1" aria-labelledby="editElementModalLabel{{ $element->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #fff; color: #000;">
            <div class="modal-header" style="border-bottom: 1px solid #000;">
                <h5 class="modal-title" id="editElementModalLabel{{ $element->id }}">Edit Elemen</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('MR.update', $element->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="background-color: #fff; color: #fff;">
                    <div class="form-group">
                        <label for="elemen" style="color: #000;">Elemen</label>
                        <textarea class="form-control" id="elemen" name="elemen" rows="5" required style="background-color: #fff; color: #000;">{{ $element->elemen }}</textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #fff;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                    <button type="submit" class="btn btn-primary" >Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@foreach($elements as $element)
    @foreach($element->ManagementSubElement as $subElement)
        <div class="modal fade" id="editSubElementModal{{ $subElement->id }}" tabindex="-1" aria-labelledby="editSubElementModalLabel{{ $subElement->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="background-color: #fff; color: #000;">
                    <div class="modal-header" style="border-bottom: 1px solid #000;">
                        <h5 class="modal-title" id="editSubElementModalLabel{{ $subElement->id }}">Edit Sub Elemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: #fff;"></button>
                    </div>
                    <form action="{{ route('subElemen.update', $subElement->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body" style="background-color: #fff; color: #fff;">
                            <div class="form-group">
                                <label for="sub_elemen" style="color: #000;">Sub Elemen</label>
                                <textarea class="form-control" id="sub_elemen" name="sub_elemen" rows="5" required style="background-color: #fff; color: #000;">{{ $subElement->sub_elemen }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer" style="background-color: #fff;">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                            <button type="submit" class="btn btn-primary" >Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach
@foreach($elements as $index => $element)
@foreach($element->ManagementSubElement as $subIndex => $subElement)
@foreach($subElement->ManagementTopic as $topicIndex => $topic)
<div class="modal fade" id="editTopicModal{{ $topic->id }}" tabindex="-1" role="dialog" aria-labelledby="editTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #fff; color: #000;">
            <div class="modal-header" style="border-bottom: 1px solid #000;">
                <h5 class="modal-title" id="editTopicModalLabel">Edit Topic</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #000;">&times;</span>
                </button>
            </div>
            <form action="{{ route('topic.update', $topic->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="background-color: #fff; color: #fff;">
                    <div class="form-group">
                        <label for="topik" style="color: #000;">Topic</label>
                        <textarea class="form-control" id="topik" name="topik" rows="7" style="background-color: #fff; color: #000;">{{ $topic->topik }}</textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #fff;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tutup</button>
                    <button type="submit" class="btn btn-primary" >Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endforeach 
@endforeach

<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>MR</h1>
        </div>
        <div class="section-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @elseif($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
            @endif
            <div class="d-flex justify-content-between mb-3">
                <div>
                @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2 || auth()->user()->id_level == 6)
                <a href="{{ route('entitas.index') }}" class="btn btn-md btn-outline-primary mb-3"
                    style="font-size: 0.85rem !important;">ENTITAS</a>
                <a href="{{ route('bobot.index') }}" class="btn btn-md btn-outline-primary mb-3"
                    style="font-size: 0.85rem !important;">BOBOT</a>
                <a href="{{ route('verif.index') }}" class="btn btn-md btn-outline-primary mb-3"
                    style="font-size: 0.85rem !important;">VALIDASI</a>
                <a href="{{ route('kesimpulan.index') }}" class="btn btn-md btn-outline-primary mb-3"
                    style="font-size: 0.85rem !important;">KESIMPULAN</a>
                </div>
                @endif
                <div>
                @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                <button type="button" class="btn btn-success mr-2" style="min-width: 120px;" data-toggle="modal" data-target="#addElementModal">
                    <i class="fas fa-plus"></i> Elemen 
                </button>
                <button type="button" class="btn btn-success mr-2" style="min-width: 120px;" data-toggle="modal" data-target="#addSubElementModal">
                    <i class="fas fa-plus"></i> Sub Elemen 
                </button>
                <button type="button" class="btn btn-success mr-2" style="min-width: 120px;" data-toggle="modal" data-target="#addTopicModal">
                    <i class="fas fa-plus"></i> Topik 
                </button>
                <button type="button" class="btn btn-success mr-2" style="min-width: 120px;" data-toggle="modal" data-target="#addUraianModal">
                    <i class="fas fa-plus"></i> Uraian 
                </button>
                @endif
                </div>
            </div>
        
        @if($elements->isEmpty())
            <p>Tidak ada elemen yang ditemukan.</p>
        @else
            @foreach($elements as $index => $element)
            <div class="accordion mb-3" id="accordionExample{{ $index }}">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #c5c8c3; color: #fff;" id="heading{{ $index }}">
                        <h5 class="mb-0">
                            <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#collapse{{ $index }}" aria-expanded="true" aria-controls="collapse{{ $index }}">
                                {{ $element->elemen }}
                            </button>
                        </h5>
                        @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                        <div class="ml-auto d-flex">
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editElementModal{{ $element->id }}" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-right: 0.5rem;">
                                <i class="fa fa-edit"></i>
                            </button>
                            <form action="{{ route('MR.destroy', ['id' => $element->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus elemen ini?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    <div id="collapse{{ $index }}" class="collapse" aria-labelledby="heading{{ $index }}" data-parent="#accordionExample{{ $index }}">
                        <div class="card-body">
                            @if($element->ManagementSubElement->isEmpty())
                                <p>Tidak ada sub elemen yang ditemukan.</p>
                            @else
                                @foreach($element->ManagementSubElement as $subIndex => $subElement)
                                <div class="accordion" id="subAccordion{{ $index }}{{ $subIndex }}">
                                    <div class="card shadow-sm border-0 rounded">
                                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #d6c5b5; color: #000;" id="subHeading{{ $index }}{{ $subIndex }}">
                                            <h6 class="mb-0">
                                                <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#subCollapse{{ $index }}{{ $subIndex }}" aria-expanded="true" aria-controls="subCollapse{{ $index }}{{ $subIndex }}">
                                                    {{ $subElement->sub_elemen }}
                                                </button>
                                            </h6>
                                            @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                            <div class="ml-auto d-flex">
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editSubElementModal{{ $subElement->id }}" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-right: 0.5rem;">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form action="{{ route('subElemen.destroy', $subElement->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sub elemen ini?');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                        <div id="subCollapse{{ $index }}{{ $subIndex }}" class="collapse" aria-labelledby="subHeading{{ $index }}{{ $subIndex }}" data-parent="#subAccordion{{ $index }}{{ $subIndex }}">
                                            <div class="card-body">
                                                @if($subElement->ManagementTopic->isEmpty())
                                                    <p>Tidak ada topik yang ditemukan.</p>
                                                @else
                                                    @foreach($subElement->ManagementTopic as $topicIndex => $topic)
                                                    <div class="accordion" id="topicAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                        <div class="card shadow-sm border-0 rounded">
                                                            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #e4d8cd; color: #000;" id="topicHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                <h6 class="mb-0">
                                                                    <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#topicCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}" aria-expanded="true" aria-controls="topicCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                        {{ $topic->topik }}
                                                                    </button>
                                                                </h6>
                                                                @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                                <div class="ml-auto d-flex">
                                                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editTopicModal{{ $topic->id }}" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-right: 0.5rem;">
                                                                        <i class="fa fa-edit"></i>
                                                                    </button>
                                                                    <form action="{{ route('topic.destroy', $topic->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus topik ini?');" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            <div id="topicCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}" class="collapse" aria-labelledby="topicHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}" data-parent="#topicAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}">
                                                                <div class="card-body">
                                                                    @foreach($topic->Uraian->groupBy('level') as $level => $uraians)
                                                                    <div class="accordion" id="levelAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                        <div class="card shadow-sm border-0 rounded">
                                                                            <div class="card-header text-dark d-flex justify-content-between align-items-center" style="background-color: #f1ebe6; color: #000;" id="levelHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                <h6 class="mb-0">
                                                                                    <button class="btn btn-link" style="color: #003366; font-weight: bold;" type="button" data-toggle="collapse" data-target="#levelCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}" aria-expanded="true" aria-controls="levelCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                        Level {{ $level }}
                                                                                    </button>
                                                                                </h6>
                                                                            </div>
                                                                            <div id="levelCollapse{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}" class="collapse" aria-labelledby="levelHeading{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}" data-parent="#levelAccordion{{ $index }}{{ $subIndex }}{{ $topicIndex }}{{ $level }}">
                                                                                <div class="card-body">
                                                                                    <div class="table-responsive">
                                                                                        <form action="{{ route('MR.update', ['id' => $level]) }}" method="POST" enctype="multipart/form-data">
                                                                                            @csrf
                                                                                            @method('PUT')
                                                                                            <table class="table table-bordered">
                                                                                                <thead class="thead-light">
                                                                                                    <tr>
                                                                                                        <th>No.</th>
                                                                                                        <th style="width: 80%;">Uraian</th> 
                                                                                                        @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                                                                        <th>Aksi</th>
                                                                                                        @endif
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    @foreach($topic->Uraian as $uraianIndex => $uraian)
                                                                                                        <tr>
                                                                                                            <td>{{ $uraianIndex + 1 }}</td>
                                                                                                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                                                                                {{ $uraian->uraian }}
                                                                                                            </td>
                                                                                                            @if(auth()->user()->id_level == 1 || auth()->user()->id_level == 2)
                                                                                                            <td>
                                                                                                                <div class="row">
                                                                                                                    <div class="col-6">
                                                                                                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editUraianModal{{ $uraian->id }}" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; margin-right: 0.5rem;">
                                                                                                                            <i class="fa fa-edit"></i>
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                    <div class="col-6">
                                                                                                                        <form action="{{ route('uraian.destroy', $uraian->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus uraian ini?');" style="display:inline;">
                                                                                                                            @csrf
                                                                                                                            @method('DELETE')
                                                                                                                            <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                                                                                                                                <i class="fa fa-trash"></i>
                                                                                                                            </button>
                                                                                                                        </form>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                            @endif
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
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

document.addEventListener('DOMContentLoaded', function () {
    const elementSelect = document.getElementById('elementSelect');
    const subElementSelect = document.getElementById('subElementSelect');

    elementSelect.addEventListener('change', function () {
        const selectedElementId = this.value;

        Array.from(subElementSelect.options).forEach(option => {
            option.style.display = option.getAttribute('data-element-id') === selectedElementId ? 'block' : 'none';
        });

        const firstVisibleOption = Array.from(subElementSelect.options).find(option => option.style.display === 'block');
        if (firstVisibleOption) {
            subElementSelect.value = firstVisibleOption.value;
        } else {
            subElementSelect.value = '';
        }
    });

    elementSelect.dispatchEvent(new Event('change'));
});

document.addEventListener('DOMContentLoaded', function () {
    const elementUraianSelect = document.getElementById('elementUraian');
    const subElementUraianSelect = document.getElementById('subElementUraian');
    const topicUraianSelect = document.getElementById('topicUraian');

    elementUraianSelect.addEventListener('change', function () {
        const selectedElementId = this.value;

        Array.from(subElementUraianSelect.options).forEach(option => {
            option.style.display = option.getAttribute('data-element-id') === selectedElementId ? 'block' : 'none';
        });

        subElementUraianSelect.dispatchEvent(new Event('change'));
    });

    subElementUraianSelect.addEventListener('change', function () {
        const selectedSubElementId = this.value;

        Array.from(topicUraianSelect.options).forEach(option => {
            option.style.display = option.getAttribute('data-sub-element-id') === selectedSubElementId ? 'block' : 'none';
        });

        const firstVisibleOption = Array.from(topicUraianSelect.options).find(option => option.style.display === 'block');
        topicUraianSelect.value = firstVisibleOption ? firstVisibleOption.value : '';
    });

    elementUraianSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection
    
