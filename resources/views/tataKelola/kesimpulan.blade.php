@extends('layout.app')

@section('title', 'Kesimpulan')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
            <h1>Kesimpulan</h1>
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
                    <form method="GET" action="{{ route('kesimpulan.index') }}" class="form-inline mb-3">
                        <div class="form-group mr-2">
                            <label for="tahun" class="mr-2">Pilih Tahun:</label>
                            <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Masukkan Tahun" value="{{ old('tahun', $tahun) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        @if($tahun)
                            <a href="{{ route('kesimpulan.index') }}" class="btn btn-secondary ml-2">Reset</a>
                        @endif
                    </form>
                    
                    <button id="exportExcelButton" class="btn btn-success mb-3 float-right">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
        
                    <div class="table-responsive">
                        <table id="tableKesimpulan" class="table table-striped table-rounded-outer table-custom-bg">
                            <thead>
                                <tr>
                                    <th>Elemen / Sub Elemen</th>
                                    @foreach(range(1, $maxLevels) as $i)
                                        <th class="text-center" style="width: 80px;">Level {{ $i }}</th>
                                    @endforeach
                                    <th class="text-center">Skor Topik</th>
                                    <th class="text-center">Simpulan Level Elemen</th>
                                    <th class="text-center">Skor Elemen</th>
                                    <th class="text-center">Simpulan</th>
                                    <th class="text-center">Improvement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedElements as $element)
                                    <tr class="custom-bg">
                                        <td class="font-weight-bold">{{ $element['elemen'] }}</td>
                                        @foreach(range(1, $maxLevels) as $i)
                                            <td></td> 
                                        @endforeach
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">{{ number_format($element['total_skor_elemen'], 2) }}</td>
                                    </tr>
                                    @foreach($element['sub_elements'] as $subElement)
                                        <tr>
                                            <td class="pl-4">{{ $subElement['sub_elemen'] }}</td>
                                            @foreach(range(1, $maxLevels) as $i)
                                                <td></td>
                                            @endforeach
                                            <td></td>
                                            <td class="text-center">{{ number_format($subElement['simpulan_level'], 2) }}</td>
                                            <td class="text-center">{{ number_format($subElement['skor_sub_elemen'], 2) }}</td>
                                        </tr>
                                        @foreach($subElement['topics'] as $topic)
                                            <tr>
                                                <td class="pl-5">{{ $topic['topik'] }}</td>
                                                @foreach($topic['levels'] as $levelStatus)
                                                    <td class="text-center">{{ $levelStatus }}</td>
                                                @endforeach
                                                <td class="text-center">{{ number_format($topic['skor'], 2) }}</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">{{ $topic['simpulan'] }}</td>
                                                <td class="text-center">{{ $topic['improvement'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
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
    function exportTableToExcel(tableId, filename = 'Kesimpulan.xlsx') {
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.table_to_sheet(document.getElementById(tableId));
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, filename);
    }

    document.getElementById('exportExcelButton').addEventListener('click', function() {
        exportTableToExcel('tableKesimpulan');
    });
</script>
@endsection
