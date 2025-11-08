@extends('layout.app')
@section('title', 'Tambah/Edit RTM')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="mr-3"><i class="fas fa-arrow-left" style="font-size: 1.3rem"></i></a>
                <h1>{{ isset($post) ? 'Edit Rekomendasi' : 'Tambah/Edit RTM' }}</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 shadow rounded">
                            <div class="card-body">
                                <form action="{{ route('rtm.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <!-- Judul -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Judul</label>
                                        <select name="judul" id="judul" class="form-control">
                                            <option value="">Pilih Judul</option>
                                            @foreach ($posts as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ isset($post) && $post->id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->judul }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('judul')
                                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Daftar RTM -->
                                    <div id="rtm-container">
                                        {{-- @if (isset($post) && $post->rtms->isNotEmpty())
                                            @foreach ($post->rtms as $index => $rtm)
                                                @include('partials.rtm-form', [
                                                    'index' => $index,
                                                    'rtm' => $rtm,
                                                ])
                                            @endforeach
                                        @else
                                            @include('partials.rtm-form', ['index' => 0])
                                        @endif --}}
                                    </div>

                                    <button type="submit" class="btn btn-md btn-primary">SUBMIT</button>
                                    <button type="reset" class="btn btn-md btn-warning">RESET</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('style')
        <style>
            .select2-selection--multiple {
                min-height: 38px !important;
                border: 1px solid #ced4da !important;
            }

            .select2-container--default .select2-selection--multiple {
                border-radius: 4px;
            }

            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                padding-left: 2% !important;
                padding-right: 0px !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Handle judul selection change
                $('#judul').on('change', function() {
                    const selectedId = $(this).val();
                    if (selectedId) {
                        $.ajax({
                            url: `/get-rtm/${selectedId}`,
                            method: 'GET',
                            success: function(response) {
                                console.log('Response RTMs:', response.rtms); // Debug log
                                $('#rtm-container').empty();
                                rtmIndex = 0;

                                response.rtms.forEach(rtm => {
                                    const newRtmForm = `
                            <div class="rtm-form mb-4 border rounded p-3 shadow-sm">
                                <input type="hidden" name="rtm[${rtmIndex}][id]" value="${rtm.id || ''}">

                                <div class="form-group">
                                    <label class="font-weight-bold">Temuan</label>
                                    <textarea name="rtm[${rtmIndex}][temuan]" class="form-control" readonly>${rtm.temuan || ''}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Rekomendasi</label>
                                    <textarea name="rtm[${rtmIndex}][rekomendasi]" class="form-control" readonly>${rtm.rekomendasi || ''}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Rencana Tindak Lanjut</label>
                                    <textarea name="rtm[${rtmIndex}][rencanaTinJut]" class="form-control">${rtm.rencanaTinJut || ''}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">PIC</label>
                                    <select name="rtm[${rtmIndex}][pic][]" class="form-control select2-multiple" multiple>
                                        @foreach ($unitKerja as $uk)
                                            <option value="{{ $uk->id }}">{{ $uk->nama_unit_kerja }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Rencana Waktu Tindak Lanjut</label>
                                    <input type="date" name="rtm[${rtmIndex}][rencanaWaktuTinJut]" class="form-control" value="${rtm.rencanaWaktuTinJut || ''}">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Status</label>
                                    <select name="rtm[${rtmIndex}][status_rtm]" class="form-control">
                                        <option value="Open">Open</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        `;
                                    $('#rtm-container').append(newRtmForm);

                                    // Initialize Select2 for PIC
                                    const $selectPic = $(
                                        `select[name="rtm[${rtmIndex}][pic][]"]`);
                                    $selectPic.select2({
                                        placeholder: "Pilih PIC",
                                        allowClear: true
                                    });

                                    // Set selected PICs if any
                                    if (rtm.pic_rtm && rtm.pic_rtm.length > 0) {
                                        const selectedPics = rtm.pic_rtm.map(pic => pic
                                            .id_unit_kerja.toString());
                                        selectedPics.forEach(picId => {
                                            $selectPic.find(
                                                    `option[value="${picId}"]`)
                                                .prop('selected', true);
                                        });
                                        $selectPic.trigger('change');
                                    }

                                    // Initialize Select2 for Status
                                    const $selectStatus = $(
                                        `select[name="rtm[${rtmIndex}][status_rtm]"]`);

                                    // Set Status
                                    if (rtm.status_rtm) {
                                        // If RTM has status, select it
                                        $selectStatus.find(
                                            `option[value="${rtm.status_rtm}"]`).prop(
                                            'selected', true);
                                    } else {
                                        // If no status, default to "Open"
                                        $selectStatus.find('option[value="Open"]').prop(
                                            'selected', true);
                                    }
                                    $selectStatus.trigger('change');

                                    rtmIndex++;
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching RTMs:', error);
                            }
                        });
                    } else {
                        $('#rtm-container').empty();
                    }
                });
            });
        </script>
    @endpush
@endsection
