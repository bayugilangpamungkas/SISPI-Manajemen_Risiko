@extends('layout.app')
@section('title', 'Dashboard')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
            <div class="section-body">

                <!-- Progress bar untuk data yang di-approve -->
                <div class="col-xl-7 col-xxl-7 mt-3 mb-3 pl-0">
                    <div class="card flex-fill w-100">
                        {{-- <div class="card-body">
                            <h5>Persentase Data yang Di-approve oleh Ketua</h5>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $approvalRate }}%;"
                                    aria-valuenow="{{ $approvalRate }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ round($approvalRate, 2) }}%</div>
                            </div>
                        </div> --}}
                        @if (Auth::user()->id_level == 1 || Auth::user()->id_level == 2)
                            <div class="card-body">
                                <h5>Persentase Laporan Akhir yang Telah Dikumpulkan (Semua Data)</h5>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $laporanAkhirRate }}%;"
                                        aria-valuenow="{{ $laporanAkhirRate }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ round($laporanAkhirRate, 2) }}%
                                    </div>
                                </div>
                            </div>
                        @endif
        
                        @if (count($assignedPosts) > 0)
                            <div class="card-body">
                                <h5>Persentase Laporan Akhir yang Telah Dikumpulkan (Tugas Anda)</h5>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $laporanAkhirRateAssigned }}%;"
                                        aria-valuenow="{{ $laporanAkhirRateAssigned }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ round($laporanAkhirRateAssigned, 2) }}%
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
        
                <div class="row">
                    <!-- Chart Data Penugasan -->
                    <div class="col-xl-7 col-xxl-7 mt-3 mb-3">
                        <div class="card flex-fill w-100">
                            <div class="card-body">
                                {!! $tugasLaporChart->container() !!}
                            </div>
                        </div>
                    </div>
        
                    <!-- Card untuk Bidang -->
                    <div class="col-xl-5 col-xxl-5 mt-3 mb-3">
                        @foreach ($bidangCounts as $bidang => $count)
                            @php
                                $cardColors = ['bg-info', 'bg-success', 'bg-warning', 'bg-primary'];
                                $color = $cardColors[$loop->index % count($cardColors)];
                            @endphp
                            <div class="card {{ $color }} ml-3 mb-5 ">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fa-solid fa-list-check mr-2" style="color: #e6e6e6;"></i>
                                    </div>
                                    <h5 class="card-title">{{ ucfirst($bidang) }}</h5>
                                    <div class="display-4">{{ $count }}</div>
                                    @if (auth()->user()->id_level == 1 ||
                                            auth()->user()->id_level == 2 ||
                                            auth()->user()->id_level == 3 ||
                                            auth()->user()->id_level == 4 ||
                                            auth()->user()->id_level == 6)
                                        <a href="/posts">
                                            <p class="card-text text-white">Lihat Detail <i
                                                    class="fa fa-angle-double-right ml-2"></i></p>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
    </section>
</div>

    <script src="{{ $tugasLaporChart->cdn() }}"></script>
    {{ $tugasLaporChart->script() }}
@endsection
