<div class="rtm-form mb-4 border rounded p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>RTM</h5>
        <button type="button" class="btn btn-sm btn-danger remove-rtm">Hapus</button>
    </div>

    <div class="form-group">
        <label class="font-weight-bold">Temuan</label>
        <textarea name="rtm[{{ $index }}][temuan]" class="form-control" disabled>{{ $rtm->temuan ?? '' }}</textarea>
    </div>

    <div class="form-group">
        <label class="font-weight-bold">Rekomendasi</label>
        <textarea name="rtm[{{ $index }}][rekomendasi]" class="form-control" disabled>{{ $rtm->rekomendasi ?? '' }}</textarea>
    </div>

    <div class="form-group">
        <label class="font-weight-bold">Rencana Tindak Lanjut</label>
        <textarea name="rtm[{{ $index }}][rencanaTinJut]" class="form-control">{{ $rtm->rencanaTinJut ?? '' }}</textarea>
    </div>

    <div class="form-group" id="picField" style="display: none;">
        <label class="font-weight-bold">PIC</label>
        <select id="pic" name="rtm[{{ $index }}][pic[]]"
            class="js-example-basic-multiple form-control" multiple="multiple">
            {{-- <option value="">- Pilih pic -</option> --}}
            @foreach ($unitKerja as $uk)
                <option value="{{ $uk->id }}">{{ $uk->nama_unit_kerja }}</option>
            @endforeach
        </select>
        @error('pic')
            <div class="alert alert-danger mt-2">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="form-group">
        <label class="font-weight-bold">Rencana Waktu Tindak Lanjut</label>
        <input type="date" name="rtm[{{ $index }}][rencanaWaktuTinJut]" class="form-control datepicker" value="{{ $rtm->rencanaWaktuTinJut ?? '' }}">
    </div>

    {{-- <div class="form-group">
        <label class="font-weight-bold">Status</label>
        <select id="status_rtm" name="rtm[{{ $index }}][status_rtm]"
            class="js-example-basic-multiple form-control" multiple="multiple">
                <option value="Open" selected>Open</option>
                <option value="In Progress">In Progress</option>
                <option value="Closed">Closed</option>
        </select>
        @error('status_rtm')
            <div class="alert alert-danger mt-2">
                {{ $message }}
            </div>
        @enderror
    </div> --}}
</div>