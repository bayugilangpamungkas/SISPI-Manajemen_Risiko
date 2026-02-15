@extends('layout.app')
@section('title', 'Detail Surat')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('surat.index') }}" class="mr-3">
                        <i class="fas fa-arrow-left" style="font-size: 1.3rem"></i>
                    </a>
                    <div>
                        <h1>Detail Surat</h1>
                    </div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            {{-- <div class="card-header">
                                <h5 class="mb-0">Informasi Surat</h5>
                                <div class="card-header-action">
                                    @if ($surat->file_pdf)
                                        <a href="{{ route('surat.download-pdf', $surat->id) }}" class="btn btn-success"
                                            target="_blank">
                                            <i class="fas fa-file-pdf"></i> Download PDF
                                        </a>
                                    @endif
                                    @if ($surat->status == 'Draft')
                                        <a href="{{ route('surat.edit', $surat->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="180">Nomor Surat</th>
                                                <td>: <strong>{{ $surat->nomor_surat }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Jenis Surat</th>
                                                <td>: <span class="badge badge-info">{{ $surat->jenis_surat }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Surat</th>
                                                <td>: {{ $surat->tanggal_surat->format('d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tujuan Surat</th>
                                                <td>: {{ $surat->tujuan_surat }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="180">Status</th>
                                                <td>:
                                                    @if ($surat->status == 'Draft')
                                                        <span class="badge badge-warning"><i class="fas fa-edit"></i>
                                                            Draft</span>
                                                    @else
                                                        <span class="badge badge-success"><i class="fas fa-check"></i>
                                                            Final</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tipe Referensi</th>
                                                <td>: <span
                                                        class="badge badge-secondary">{{ $surat->tipe_referensi }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Dibuat Oleh</th>
                                                <td>: {{ $surat->creator->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dibuat Pada</th>
                                                <td>: {{ $surat->created_at->format('d F Y, H:i') }} WIB</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label class="font-weight-bold">Perihal:</label>
                                    <p class="p-3 bg-light rounded">{{ $surat->perihal }}</p>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Isi Surat:</label>
                                    <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">
                                        {{ $surat->isi_surat }}</div>
                                </div>

                                @if ($surat->tipe_referensi != 'Tanpa Referensi')
                                    <hr>
                                    <h5 class="mb-3"><i class="fas fa-link"></i> Informasi Referensi</h5>
                                    <div class="alert alert-info">
                                        <strong>{{ $surat->tipe_referensi }}:</strong> {{ $surat->referensi_nama }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
