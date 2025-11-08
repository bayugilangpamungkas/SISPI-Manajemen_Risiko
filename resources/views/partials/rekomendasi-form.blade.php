<div class="rtm-form mb-4 border rounded p-3 shadow-sm">
    <div class="form-group">
        <label class="font-weight-bold">Temuan</label>
        <textarea name="rtm[{{ $index }}][temuan]" class="form-control">{{ $rtm->temuan ?? '' }}</textarea>
    </div>
    
    <div class="form-group">
        <label class="font-weight-bold">Rekomendasi</label>
        <textarea name="rtm[{{ $index }}][rekomendasi]" class="form-control">{{ $rtm->rekomendasi ?? '' }}</textarea>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-sm btn-danger remove-rtm">Hapus</button>
    </div>
</div>


{{-- <div class="rtm-form mb-4 border rounded p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>RTM</h5>
        <button type="button" class="btn btn-sm btn-danger remove-rtm">Hapus</button>
    </div>

    <div class="form-group">
        <label>Temuan</label>
        <input type="text" name="rtms[__INDEX__][temuan]" value="{{ old('rtms.__INDEX__.temuan', isset($rtm) ? $rtm->temuan : '') }}" class="form-control">
    </div>

    <div class="form-group">
        <label>Rekomendasi</label>
        <input type="text" name="rtms[__INDEX__][rekomendasi]" value="{{ old('rtms.__INDEX__.rekomendasi', isset($rtm) ? $rtm->rekomendasi : '') }}" class="form-control">
    </div>
</div> --}}