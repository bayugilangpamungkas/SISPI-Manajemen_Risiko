@extends('layout.app')
@section('title', 'Tambah Berita Acara')
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Tambah Berita Acara</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item"><a href="{{ route('berita-acara.index') }}">Berita Acara</a></div>
                        <div class="breadcrumb-item active">Tambah</div>
                    </div>
                </div>
                <div class="section-header-button">
                    <a href="{{ route('berita-acara.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Form Berita Acara</h2>
                <p class="section-lead">Lengkapi detail rapat dan unggah dokumen atau foto pendukung.</p>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('berita-acara.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Judul <span class="text-danger">*</span></label>
                                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="meeting_date">Tanggal Rapat</label>
                                        <input type="date" id="meeting_date" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror" value="{{ old('meeting_date') }}">
                                        @error('meeting_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="location">Lokasi</label>
                                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="Misal: Ruang Rapat Utama">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="summary">Ringkasan</label>
                                <textarea id="summary" name="summary" class="form-control @error('summary') is-invalid @enderror" rows="4" placeholder="Catat poin penting rapat...">{{ old('summary') }}</textarea>
                                @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Dokumen PDF</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('documents.*') is-invalid @enderror" id="documents" name="documents[]" accept="application/pdf" multiple>
                                    <label class="custom-file-label" for="documents">Pilih satu atau lebih dokumen (PDF, maks 10MB)</label>
                                    @error('documents.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Maksimal 10 dokumen per unggahan.</small>
                            </div>

                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="mb-0">Galeri Foto</label>
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

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Simpan</button>
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
            const MAX_DOCUMENT_UPLOADS = 10;
            const MAX_IMAGE_UPLOADS = 10;
            const imageCollection = document.getElementById('imageCollection');
            const addImageFieldButton = document.getElementById('addImageField');
            const documentInput = document.getElementById('documents');

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

            updateRemoveButtons();
        })();
    </script>
@endpush
