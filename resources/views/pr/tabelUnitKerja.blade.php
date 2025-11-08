@extends('layout.app')
@section('title', 'Tabel Matrik')
@section('main')
    <style>
        td {
            color: white
        }
    </style>
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>Tabel Matrik</h1>
            </div>
            <div class="section-body">
                <h4 class="tittle-1">
                    <span class="span0">Matrik</span>
                    <span class="span1">Analisis Risiko</span>
                </h4>

                <!-- Filter Tahun -->
                <form method="GET" action="{{ route('petas.tabelUnitKerja', ['unitKerja' => $unitKerja]) }}"
                    class="mb-3 row d-flex align-item-center">
                    <div class="form-group col-md-4">
                        <select name="tahun" id="tahun" class="form-control">
                            <option value="">Semua Tahun</option>
                            @for ($i = date('Y'); $i >= 2000; $i--)
                                <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <table class="table table-bordered table-responsive">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Sangat Jarang</th>
                                                    <th>Jarang</th>
                                                    <th>Kadang</th>
                                                    <th>Sering</th>
                                                    <th>Pasti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>Sangat Berpengaruh (5)</th>
                                                    <td id="R-5-1" class="bg-success">Rendah-5</td>
                                                    <td id="R-5-2" class="bg-yellow">Sedang-10</td>
                                                    <td id="R-5-3" class="bg-warning">Tinggi-15</td>
                                                    <td id="R-5-4" class="bg-danger">Sangat Tinggi-20</td>
                                                    <td id="R-5-5" class="bg-danger">Sangat Tinggi-25</td>
                                                </tr>
                                                <tr>
                                                    <th>Berpengaruh (4)</th>
                                                    <td id="R-4-1" class="bg-warning-blue">Sangat Rendah-4</td>
                                                    <td id="R-4-2" class="bg-success">Rendah-8</td>
                                                    <td id="R-4-3" class="bg-yellow">Sedang-12</td>
                                                    <td id="R-4-4" class="bg-warning">Tinggi-16</td>
                                                    <td id="R-4-5" class="bg-danger">Sangat Tinggi-20</td>
                                                </tr>
                                                <tr>
                                                    <th>Cukup Berpengaruh (3)</th>
                                                    <td id="R-3-1" class="bg-warning-blue">Sangat Rendah-3</td>
                                                    <td id="R-3-2" class="bg-success">Rendah-6</td>
                                                    <td id="R-3-3" class="bg-yellow">Sedang-9</td>
                                                    <td id="R-3-4" class="bg-yellow">Sedang-12</td>
                                                    <td id="R-3-5" class="bg-warning">Tinggi-15</td>
                                                </tr>
                                                <tr>
                                                    <th>Kurang Berpengaruh (2)</th>
                                                    <td id="R-2-1" class="bg-warning-blue">Sangat Rendah-2</td>
                                                    <td id="R-2-2" class="bg-warning-blue">Sangat Rendah-4</td>
                                                    <td id="R-2-3" class="bg-success">Rendah-6</td>
                                                    <td id="R-2-4" class="bg-success">Rendah-8</td>
                                                    <td id="R-2-5" class="bg-yellow">Sedang-10</td>
                                                </tr>
                                                <tr>
                                                    <th>Tidak Berpengaruh (1)</th>
                                                    <td id="R-1-1" class="bg-warning-blue">Sangat Rendah-1</td>
                                                    <td id="R-1-2" class="bg-warning-blue">Sangat Rendah-2</td>
                                                    <td id="R-1-3" class="bg-warning-blue">Sangat Rendah-3</td>
                                                    <td id="R-1-4" class="bg-warning-blue">Sangat Rendah-4</td>
                                                    <td id="R-1-5" class="bg-success">Rendah-5</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <canvas id="riskPieChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    @foreach ($matrix as $key => $codes)
        <script>
            document.getElementById("{{ $key }}").innerHTML += `</br>
            @foreach ($codes as $code)
                <span class="badge badge-primary">{{ $code }}</span><br/>
            @endforeach
        `;
        </script>
    @endforeach

    <style>
        table {
            width: 100%;
            text-align: center;
        }

        .badge {
            display: inline-block;
            margin: 2px;
            padding: 5px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('riskPieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Sangat Rendah', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'],
                    datasets: [{
                        data: [
                            {!! $riskDistribution['Sangat Rendah']['total'] !!},
                            {!! $riskDistribution['Rendah']['total'] !!},
                            {!! $riskDistribution['Sedang']['total'] !!},
                            {!! $riskDistribution['Tinggi']['total'] !!},
                            {!! $riskDistribution['Sangat Tinggi']['total'] !!}
                        ],
                        backgroundColor: [
                            '#3498DB', // Biru
                            '#2ECC71', // Hijau  
                            '#F1C40F', // Kuning
                            '#E67E22', // Orange
                            '#E74C3C'  // Merah
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const dataset = context.dataset.data;
                                    const telaah = {!! json_encode($riskDistribution) !!}[label]['telaah'];
                                    const belumTelaah = {!! json_encode($riskDistribution) !!}[label]['belum_telaah'];
                                    return [
                                        `Total: ${value}`,
                                        `Telaah: ${telaah}`,
                                        `Belum Telaah: ${belumTelaah}`
                                    ];
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Risk Level'
                        }
                    }
                }
            });
        });
    </script>
@endsection
