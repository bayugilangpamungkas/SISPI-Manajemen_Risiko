<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use App\Models\BeritaAcaraDocument;
use App\Models\BeritaAcaraImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraController extends Controller
{
    private const PANEL_ACTIVE_MENU_ID = 20;

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $minutes = BeritaAcara::query()
            ->withCount(['documents', 'images'])
            ->when($search, function ($query, $term) {
                $query->where(function ($innerQuery) use ($term) {
                    $innerQuery->where('title', 'like', "%{$term}%")
                        ->orWhere('summary', 'like', "%{$term}%")
                        ->orWhere('location', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('meeting_date')
            ->orderByDesc('created_at')
            ->paginate(10);

        $minutes->appends($request->query());

        return view('berita_acara.index', [
            'minutes' => $minutes,
            'search' => $search,
            'active' => self::PANEL_ACTIVE_MENU_ID,
        ]);
    }

    public function create(): View
    {
        return view('berita_acara.create', [
            'active' => self::PANEL_ACTIVE_MENU_ID,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf', 'max:10240'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'image', 'max:5120'],
            'image_captions' => ['nullable', 'array', 'max:10'],
            'image_captions.*' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $minute = BeritaAcara::create([
                'title' => $data['title'],
                'meeting_date' => $data['meeting_date'] ?? null,
                'location' => $data['location'] ?? null,
                'summary' => $data['summary'] ?? null,
            ]);

            $this->storeDocuments($request, $minute);
            $this->storeImages($request, $minute);
        });

        return redirect()
            ->route('berita-acara.index')
            ->with('success', 'Berita acara berhasil dibuat.');
    }

    public function edit(BeritaAcara $berita_acara): View
    {
        $berita_acara->load(['documents', 'images']);

        return view('berita_acara.edit', [
            'minute' => $berita_acara,
            'active' => self::PANEL_ACTIVE_MENU_ID,
        ]);
    }

    public function update(Request $request, BeritaAcara $berita_acara): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf', 'max:10240'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'image', 'max:5120'],
            'image_captions' => ['nullable', 'array', 'max:10'],
            'image_captions.*' => ['nullable', 'string', 'max:255'],
            'existing_image_captions' => ['nullable', 'array'],
            'existing_image_captions.*' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $request, $berita_acara) {
            $berita_acara->update([
                'title' => $data['title'],
                'meeting_date' => $data['meeting_date'] ?? null,
                'location' => $data['location'] ?? null,
                'summary' => $data['summary'] ?? null,
            ]);

            $this->storeDocuments($request, $berita_acara);
            $this->storeImages($request, $berita_acara);

            $this->updateExistingImageCaptions($request, $berita_acara);
        });

        return redirect()
            ->route('berita-acara.edit', $berita_acara)
            ->with('success', 'Berita acara berhasil diperbarui.');
    }

    public function destroy(BeritaAcara $berita_acara): RedirectResponse
    {
        DB::transaction(function () use ($berita_acara) {
            $berita_acara->load(['documents', 'images']);

            foreach ($berita_acara->documents as $document) {
                Storage::disk('public')->delete($document->file_path);
                $document->delete();
            }

            foreach ($berita_acara->images as $image) {
                Storage::disk('public')->delete($image->file_path);
                $image->delete();
            }

            $berita_acara->delete();
        });

        return redirect()
            ->route('berita-acara.index')
            ->with('success', 'Berita acara berhasil dihapus.');
    }

    public function destroyDocument(BeritaAcaraDocument $document): RedirectResponse
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function destroyImage(BeritaAcaraImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    private function storeDocuments(Request $request, BeritaAcara $minute): void
    {
        $files = $request->file('documents', []);

        foreach ($files as $file) {
            $path = $file->store('berita_acara/documents', 'public');

            $minute->documents()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    private function storeImages(Request $request, BeritaAcara $minute): void
    {
        $files = $request->file('images', []);
        $captions = $request->input('image_captions', []);

        $nextOrder = ($minute->images()->max('display_order') ?? 0);

        foreach ($files as $index => $file) {
            $path = $file->store('berita_acara/images', 'public');

            $minute->images()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'caption' => $captions[$index] ?? null,
                'display_order' => ++$nextOrder,
            ]);
        }
    }

    private function updateExistingImageCaptions(Request $request, BeritaAcara $minute): void
    {
        $existingCaptions = $request->input('existing_image_captions', []);

        if (empty($existingCaptions)) {
            return;
        }

        foreach ($existingCaptions as $imageId => $caption) {
            $image = $minute->images()->whereKey($imageId)->first();

            if (! $image) {
                continue;
            }

            $image->update(['caption' => $caption ?: null]);
        }
    }
}
