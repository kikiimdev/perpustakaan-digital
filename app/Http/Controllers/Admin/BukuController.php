<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BukuStoreRequest;
use App\Http\Requests\Admin\BukuUpdateRequest;
use App\Http\Requests\Admin\KategoriCepatRequest;
use App\Http\Requests\Admin\PenulisCepatRequest;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penulis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BukuController extends Controller
{
    public function index(Request $request): Response
    {
        $buku = Buku::with(['penulis', 'kategori'])
            ->when($request->cari, fn ($q) => $q->where('judul', 'like', "%{$request->cari}%"))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return Inertia::render('Admin/Buku/Index', [
            'buku' => $buku,
            'cari' => $request->cari ?? '',
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Buku/Form', [
            'penulis' => Penulis::orderBy('nama')->get(),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function show(Buku $buku): Response
    {
        $buku->load(['penulis', 'kategori']);

        return Inertia::render('Admin/Buku/Show', [
            'buku' => $buku,
        ]);
    }

    public function store(BukuStoreRequest $request): RedirectResponse
    {
        $sampulPath = null;
        if ($request->hasFile('sampul')) {
            $sampulPath = $request->file('sampul')->store('sampul', 'public');
        }

        $pdfPath = $request->file('file_pdf')->store('buku_pdf', 'public');

        $buku = Buku::create([
            'penulis_id' => $request->penulis_id,
            'judul' => $request->judul,
            'sinopsis' => $request->sinopsis,
            'sampul' => $sampulPath,
            'file_pdf' => $pdfPath,
            'jumlah_halaman' => $request->jumlah_halaman,
        ]);

        $buku->kategori()->sync($request->kategori_ids);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Buku berhasil ditambahkan.',
        ]);

        return redirect()->route('admin.buku.index');
    }

    public function edit(Buku $buku): Response
    {
        $buku->load(['penulis', 'kategori']);

        return Inertia::render('Admin/Buku/Form', [
            'buku' => $buku,
            'penulis' => Penulis::orderBy('nama')->get(),
            'kategori' => Kategori::orderBy('nama')->get(),
            'selectedKategori' => $buku->kategori->pluck('id'),
        ]);
    }

    public function update(BukuUpdateRequest $request, Buku $buku): RedirectResponse
    {
        $data = [
            'penulis_id' => $request->penulis_id,
            'judul' => $request->judul,
            'sinopsis' => $request->sinopsis,
            'jumlah_halaman' => $request->jumlah_halaman,
        ];

        if ($request->hasFile('sampul')) {
            if ($buku->sampul) {
                Storage::disk('public')->delete($buku->sampul);
            }
            $data['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        if ($request->hasFile('file_pdf')) {
            Storage::disk('public')->delete($buku->file_pdf);
            $data['file_pdf'] = $request->file('file_pdf')->store('buku_pdf', 'public');
        }

        $buku->update($data);
        $buku->kategori()->sync($request->kategori_ids);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Buku berhasil diperbarui.',
        ]);

        return redirect()->route('admin.buku.index');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->sampul) {
            Storage::disk('public')->delete($buku->sampul);
        }
        Storage::disk('public')->delete($buku->file_pdf);
        $buku->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Buku berhasil dihapus.',
        ]);

        return redirect()->route('admin.buku.index');
    }

    public function tambahPenulisCepat(PenulisCepatRequest $request): JsonResponse
    {
        $penulis = Penulis::create([
            'nama' => $request->nama,
        ]);

        return response()->json($penulis);
    }

    public function tambahKategoriCepat(KategoriCepatRequest $request): JsonResponse
    {
        $kategori = Kategori::create([
            'nama' => $request->nama,
            'slug' => \Illuminate\Support\Str::slug($request->nama),
        ]);

        return response()->json($kategori);
    }
}
