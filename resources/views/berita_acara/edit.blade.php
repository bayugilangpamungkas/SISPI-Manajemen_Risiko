@extends('layout.app')
@section('title', 'Edit Berita Acara')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Edit Berita Acara</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item"><a href="{{ route('berita-acara.index') }}">Berita Acara</a></div>
                        <div class="breadcrumb-item active">Edit</div>
                    </div>
                </div>
                <div class="section-header-button">
                    <a href="{{ route('berita-acara.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Perbarui Informasi Berita Acara</h2>
                <p class="section-lead">Perbarui detail rapat, tambahkan dokumen baru, atau kelola galeri foto.</p>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('berita-acara.update', $minute) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Judul <span class="text-danger">*</span></label>
                                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $minute->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="meeting_date">Tanggal Rapat</label>
                                        <input type="date" id="meeting_date" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror" value="{{ old('meeting_date', optional($minute->meeting_date)->toDateString()) }}">
                                        @error('meeting_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="location">Lokasi</label>
                                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $minute->location) }}" placeholder="Misal: Ruang Rapat Utama">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="summary">Ringkasan</label>
                                <textarea id="summary" name="summary" class="form-control @error('summary') is-invalid @enderror" rows="4" placeholder="Catat poin penting rapat...">{{ old('summary', $minute->summary) }}</textarea>
                                @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Dokumen PDF baru</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('documents.*') is-invalid @enderror" id="documents" name="documents[]" accept="application/pdf" multiple>
                                    <label class="custom-file-label" for="documents">Pilih dokumen tambahan (PDF, maks 10MB)</label>
                                </div>
                                @error('documents.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Maksimal 10 dokumen per unggahan.</small>
                            </div>

                            @if ($minute->documents->isNotEmpty())
                                <div class="form-group">
                                    <label class="d-block">Dokumen Saat Ini</label>
                                    <ul class="list-group">
                                        @foreach ($minute->documents as $document)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                                                    <a href="{{ $document->download_url }}" target="_blank" rel="noopener">{{ $document->file_name }}</a>
                                                    <small class="text-muted ml-2">{{ number_format(($document->file_size ?? 0) / 1024, 1) }} KB</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-document-button" data-action="{{ route('berita-acara.documents.destroy', $document) }}" data-confirm="Hapus dokumen ini?">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="mb-0">Tambahkan Gambar Baru</label>
                                    <button class="btn btn-sm btn-outline-primary" type="button" id="addImageField">
                                        <i class="fas fa-plus mr-1"></i>Tambah Gambar
                                    </button>
                                </div>
                                <small class="form-text text-muted">Unggah gambar (JPG, PNG, maks 5MB, maks 10 file) dengan caption opsional.</small>

                                <div id="imageCollection" class="mt-3">
                                    <div class="image-item border rounded p-3 mb-3">
                                        <div class="form-group mb-2">
                                            <label class="mb-1">File Gambar</label>
                                            <input type="file" name="images[]" class="form-control @error('images.0') is-invalid @enderror" accept="image/*">
                                            @error('images.0')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="mb-1">Caption (Opsional)</label>
                                            <input type="text" name="image_captions[]" class="form-control @error('image_captions.0') is-invalid @enderror" maxlength="255" placeholder="Deskripsi singkat foto">
                                            @error('image_captions.0')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-3 remove-image">
                                            <i class="fas fa-trash mr-1"></i>Hapus Gambar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if ($minute->images->isNotEmpty())
                                <div class="form-group">
                                    <label class="d-block">Galeri Saat Ini</label>
                                    <div class="row">
                                        @foreach ($minute->images as $image)
                                            <div class="col-md-4 mb-3">
                                                <div class="card shadow-sm h-100">
                                                    <img src="{{ $image->image_url }}" class="card-img-top" alt="{{ $image->caption ?? $minute->title }}">
                                                    <div class="card-body">
                                                        <div class="form-group mb-3">
                                                            <label class="small text-muted">Caption</label>
                                                            <input type="text" name="existing_image_captions[{{ $image->id }}]" class="form-control" value="{{ old('existing_image_captions.' . $image->id, $image->caption) }}" maxlength="255">
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-block delete-image-button" data-action="{{ route('berita-acara.images.destroy', $image) }}" data-confirm="Hapus gambar ini?">
                                                            <i class="fas fa-trash mr-1"></i>Hapus Gambar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const csrfToken = '{{ csrf_token() }}';
            const MAX_DOCUMENT_UPLOADS = 10;
            const MAX_IMAGE_UPLOADS = 10;
            const imageCollection = document.getElementById('imageCollection');
            const addImageFieldButton = document.getElementById('addImageField');
            const documentInput = document.getElementById('documents');
            const deleteDocumentButtons = document.querySelectorAll('.delete-document-button');
            const deleteImageButtons = document.querySelectorAll('.delete-image-button');

            if (!imageCollection || !addImageFieldButton) {
                return;
            }

            const getImageItems = () => Array.from(imageCollection.querySelectorAll('.image-item'));

            const updateRemoveButtons = () => {
                const items = getImageItems();
                items.forEach((item) => {
                    const removeButton = item.querySelector('.remove-image');
                    if (!removeButton) {
                        return;
                    }

                    removeButton.style.display = items.length > 1 ? 'inline-block' : 'none';
                });
            };

            const buildImageField = () => {
                const wrapper = document.createElement('div');
                wrapper.className = 'image-item border rounded p-3 mb-3';
                wrapper.innerHTML = `
                    <div class="form-group mb-2">
                        <label class="mb-1">File Gambar</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group mb-0">
                        <label class="mb-1">Caption (Opsional)</label>
                        <input type="text" name="image_captions[]" class="form-control" maxlength="255" placeholder="Deskripsi singkat foto">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-3 remove-image">
                        <i class="fas fa-trash mr-1"></i>Hapus Gambar
                    </button>
                `;
                return wrapper;
            };

            addImageFieldButton.addEventListener('click', function () {
                if (getImageItems().length >= MAX_IMAGE_UPLOADS) {
                    alert('Maksimal 10 gambar per unggahan. Mohon hapus gambar lain terlebih dahulu.');
                    return;
                }

                const field = buildImageField();
                imageCollection.appendChild(field);
                updateRemoveButtons();
            });

            imageCollection.addEventListener('click', function (event) {
                const button = event.target.closest('.remove-image');
                if (!button) {
                    return;
                }

                const items = getImageItems();
                if (items.length <= 1) {
                    return;
                }

                const item = button.closest('.image-item');
                if (item) {
                    item.remove();
                    updateRemoveButtons();
                }
            });

            const enforceDocumentLimit = (input) => {
                if (!input || input.files.length <= MAX_DOCUMENT_UPLOADS) {
                    return;
                }

                alert('Maksimal 10 dokumen per unggahan. Mohon pilih lebih sedikit file.');
                input.value = '';
            };

            if (documentInput) {
                documentInput.addEventListener('change', function () {
                    enforceDocumentLimit(this);
                });
            }

            const submitDeleteRequest = (action) => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(tokenInput);
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            };

            const wireDeleteButtons = (buttons) => {
                buttons.forEach((button) => {
                    button.addEventListener('click', function () {
                        const action = this.dataset.action;
                        const message = this.dataset.confirm || 'Anda yakin?';

                        if (!action) {
                            return;
                        }

                        if (message && !confirm(message)) {
                            return;
                        }

                        submitDeleteRequest(action);
                    });
                });
            };

            wireDeleteButtons(deleteDocumentButtons);
            wireDeleteButtons(deleteImageButtons);
            updateRemoveButtons();
        })();
    </script>
@endpush
