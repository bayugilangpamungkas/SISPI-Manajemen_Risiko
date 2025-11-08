@extends('layout.app')

@section('title', 'Verif')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Validasi</h1>
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
        
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('verif.index') }}" class="form-inline mb-3">
                        <div class="form-group mr-2">
                            <label for="tahun" class="mr-2">Pilih Tahun:</label>
                            <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Masukkan Tahun" value="{{ old('tahun', $tahun) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if($tahun)
                            <a href="{{ route('verif.index') }}" class="btn btn-secondary ml-2">Reset</a>
                        @endif
                    </form>
        
                    <div class="table-responsive">
                        <table class="table table-rounded-outer table-custom-bg">
                            <thead>
                                <tr>
                                    <th style="width: 50%; text-align: center;">Elemen</th>
                                    <th style="width: 50%; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elements as $element)
                                <tr id="elementRow{{ $element->id }}" class="custom-bg">
                                    <td class="font-weight-bold elemen-col" style="width: 50%; text-align: left;">
                                        <span class="element-toggle" data-id="{{ $element->id }}" style="cursor: pointer;">
                                            {{ $element->elemen }}
                                        </span>
                                    </td>
                                    <td class="aksi-col" style="width: 50%; text-align: center;"></td>
                                </tr>
        
                                <tr id="subElementContainer{{ $element->id }}" class="sub-element-container custom-bg" style="display: none;">
                                    <td colspan="2">
                                        <table class="table table-borderless table-custom-bg">
                                            @forelse($element->ManagementSubElement as $subElement)
                                            <tr>
                                                <td class="pl-4 elemen-col" style="width: 50%; text-align: left;">
                                                    <span class="sub-element-toggle" data-id="{{ $subElement->id }}" style="cursor: pointer;">
                                                        {{ $subElement->sub_elemen }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr id="topicContainer{{ $subElement->id }}" class="topic-container custom-bg" style="display: none;">
                                                <td colspan="2">
                                                    <table class="table table-borderless table-custom-bg">
                                                        @forelse($subElement->ManagementTopic as $topic)
                                                        <tr>
                                                            <td class="pl-5 elemen-col" style="width: 50%; text-align: left;">
                                                                <span class="topic-text" data-target="topic{{ $topic->id }}" style="cursor: pointer;">
                                                                    {{ $topic->topik }}
                                                                </span>
                                                            </td>
                                                            <td class="aksi-col" style="width: 50%; text-align: center;">
                                                                <div class="btn-group" role="group" aria-label="Level Buttons">
                                                                    @foreach($topic->Uraian->groupBy('level') as $level => $uraians)
                                                                    <button type="button" class="btn btn-secondary mx-1 level-btn" data-level="{{ $level }}" data-topic="{{ $topic->id }}">
                                                                        Level {{ $level }}
                                                                    </button>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="uraianTableRow{{ $topic->id }}" class="uraian-table-row custom-bg" style="display: none;">
                                                            <td colspan="2">
                                                                <table class="table table-bordered table-custom-bg">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 5%;">No</th> 
                                                                            <th style="width: 60%;">Uraian</th>
                                                                            <th style="width: 10%; text-align: center;">Jawaban</th>
                                                                            <th style="width: 10%; text-align: center;">Dokumen</th>
                                                                            <th style="width: 10%; text-align: center;">Verifikasi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($topic->Uraian->groupBy('level') as $level => $uraiansByLevel)
                                                                        @foreach($uraiansByLevel as $index => $uraian)
                                                                        <tr class="uraian-row" data-level="{{ $uraian->level }}">
                                                                            <td style="text-align: center;">{{ $index + 1 }}</td>
                                                                            <td>{{ $uraian->uraian }}</td>
                                                                            <td style="text-align: center;">
                                                                                @php
                                                                                $jawaban = $uraian->Jawaban->first();
                                                                                $status = $jawaban ? ($jawaban->status ? 'Y' : 'T') : '';
                                                                                @endphp
                                                                                {{ $status }}
                                                                            </td>
                                                                            <td style="text-align: center;">
                                                                                @if($jawaban && $jawaban->dokumen)
                                                                                <a href="{{ asset('storage/' . $jawaban->dokumen) }}" class="btn btn-primary btn-sm" target="_blank">Lihat Dokumen</a>
                                                                                @endif
                                                                            </td>
                                                                            <td style="text-align: center;">
                                                                                @if($jawaban)
                                                                                    @if($jawaban->validasi_at)
                                                                                        <span class="text-black-bold">Selesai</span>
                                                                                    @else
                                                                                        <form action="{{ route('jawaban.verify', $jawaban->id) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <button type="submit" class="btn btn-success btn-sm">Verifikasi</button>
                                                                                        </form>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td class="pl-5">Tidak ada topik yang ditemukan.</td>
                                                        </tr>
                                                        @endforelse
                                                    </table>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td class="pl-4">Tidak ada sub elemen yang ditemukan.</td>
                                            </tr>
                                            @endforelse
                                        </table>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

    document.querySelectorAll('.element-toggle').forEach(element => {
        element.addEventListener('click', function() {
            let elementId = this.getAttribute('data-id');
            let subElementContainer = document.getElementById(`subElementContainer${elementId}`);
            let isVisible = subElementContainer.style.display !== 'none';
            subElementContainer.style.display = isVisible ? 'none' : '';

            updateElementState(elementId, !isVisible);
        });
    });

    document.querySelectorAll('.sub-element-toggle').forEach(subElement => {
        subElement.addEventListener('click', function() {
            let subElementId = this.getAttribute('data-id');
            let topicContainer = document.getElementById(`topicContainer${subElementId}`);
            let isVisible = topicContainer.style.display !== 'none';
            topicContainer.style.display = isVisible ? 'none' : '';
            
            this.classList.toggle('underline', !isVisible);
            updateSubElementState(subElementId, !isVisible);
        });
    });
    
    document.querySelectorAll('.level-btn').forEach(button => {
        button.addEventListener('click', function() {
            let level = this.getAttribute('data-level');
            let topicId = this.getAttribute('data-topic');
            let uraianTableRow = document.querySelector(`#uraianTableRow${topicId}`);
            let isVisible = uraianTableRow.style.display !== 'none';
            
            document.querySelectorAll(`#uraianTableRow${topicId} .uraian-row`).forEach(row => {
                let rowLevel = row.getAttribute('data-level');
                row.style.display = (rowLevel == level) ? '' : 'none';
            });
            
            updateLevelState(topicId, level, !isVisible);
            uraianTableRow.style.display = isVisible ? 'none' : ''; 
            
            document.querySelectorAll(`.level-btn[data-topic="${topicId}"]`).forEach(btn => {
                btn.classList.remove('active-level-btn');
            });
            this.classList.add('active-level-btn');
        });
    });

    function updateElementState(elementId, state) {
        localStorage.setItem(`element_${elementId}`, state);
    }

    function updateSubElementState(subElementId, state) {
        localStorage.setItem(`sub_element_${subElementId}`, state);
    }

    function updateLevelState(topicId, level, state) {
        localStorage.setItem(`level_${topicId}`, JSON.stringify({ level, state }));
    }

    window.onload = function() {
        document.querySelectorAll('tr[id^="subElementContainer"]').forEach(container => {
            let elementId = container.id.replace('subElementContainer', '');
            let state = localStorage.getItem(`element_${elementId}`);
            container.style.display = state === 'true' ? '' : 'none';
        });

        document.querySelectorAll('tr[id^="topicContainer"]').forEach(container => {
            let subElementId = container.id.replace('topicContainer', '');
            let state = localStorage.getItem(`sub_element_${subElementId}`);
            container.style.display = state === 'true' ? '' : 'none';
        });

        document.querySelectorAll('tr[id^="uraianTableRow"]').forEach(row => {
            let topicId = row.id.replace('uraianTableRow', '');
            let levelState = localStorage.getItem(`level_${topicId}`);
            if (levelState) {
                let { level, state } = JSON.parse(levelState);
                row.style.display = state === 'true' ? '' : 'none';
                document.querySelectorAll(`#uraianTableRow${topicId} .uraian-row`).forEach(row => {
                    let rowLevel = row.getAttribute('data-level');
                    row.style.display = (rowLevel == level) ? '' : 'none';
                });
            }
        });
    };
</script>

@endsection
