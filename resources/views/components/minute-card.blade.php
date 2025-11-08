@php
    $minuteImages = collect($minute->images ?? []);
    $minuteDocuments = collect($minute->documents ?? []);
    $galleryIdentifier = 'minute-gallery-' . ($minute->id ?? uniqid());
    $hasImages = $minuteImages->isNotEmpty();
@endphp

<article class="minute-card" data-minute-card>
    <div class="minute-card-hero">
        @if ($hasImages)
            <div class="minute-card-viewport" data-minute-gallery="{{ $galleryIdentifier }}">
                @foreach ($minuteImages as $index => $image)
                    <img
                        src="{{ $image->image_url }}"
                        alt="{{ $image->caption ?? $minute->title }}"
                        class="minute-card-slide {{ $index === 0 ? 'is-active' : '' }}"
                        loading="lazy"
                    >
                @endforeach

                @if ($minuteImages->count() > 1)
                    <button type="button" class="minute-card-nav" data-minute-prev aria-label="Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="minute-card-nav" data-minute-next aria-label="Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div class="minute-card-dots">
                        @foreach ($minuteImages as $index => $image)
                            <button type="button" class="minute-card-dot {{ $index === 0 ? 'is-active' : '' }}" data-minute-dot data-target="{{ $index }}" aria-label="Pilih gambar {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="minute-card-placeholder">
                <i class="fas fa-images"></i>
                <span>Belum ada foto</span>
            </div>
        @endif
    </div>

    <div class="minute-card-content">
        <div class="minute-card-meta">
            <span class="minute-card-date">{{ $minute->meeting_date?->format('d M Y') ?? 'Tanggal belum ditetapkan' }}</span>
            @if ($minute->location)
                <span class="minute-card-location"><i class="fas fa-map-marker-alt"></i> {{ $minute->location }}</span>
            @endif
        </div>

        <h3 class="minute-card-title">{{ $minute->title }}</h3>

        @if ($minute->summary)
            <p class="minute-card-summary">{{ \Illuminate\Support\Str::limit($minute->summary, 160) }}</p>
        @endif

        @if ($minuteDocuments->isNotEmpty())
            <div class="minute-card-documents">
                <span class="minute-card-section-title">Dokumen PDF</span>
                @foreach ($minuteDocuments as $document)
                    <a href="{{ $document->download_url }}" target="_blank" rel="noopener">
                        <i class="fas fa-file-pdf"></i>
                        <span>{{ $document->file_name }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($hasImages && $minuteImages->count() > 1)
            <div class="minute-card-footer">
                <span class="minute-card-section-title minute-card-section-title-inline">Galeri Foto</span>
                <div class="minute-card-gallery-thumbs">
                    @foreach ($minuteImages as $image)
                        <a href="{{ $image->image_url }}" target="_blank" rel="noopener">
                            <img src="{{ $image->image_url }}" alt="{{ $image->caption ?? $minute->title }}" loading="lazy">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</article>
