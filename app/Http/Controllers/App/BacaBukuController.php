<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\CatatStatistikRequest;
use App\Http\Requests\App\SimpanMarkahRequest;
use App\Http\Requests\App\ToggleFavoritRequest;
use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\Kategori;
use App\Models\MarkahBuku;
use App\Models\Penulis;
use App\Models\RiwayatBaca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BacaBukuController extends Controller
{
    public function lihat(Buku $buku): Response
    {
        $buku->load(['penulis', 'kategori']);

        $isFavorit = BukuFavorit::where('user_id', auth()->id())
            ->where('buku_id', $buku->id)
            ->exists();

        $markah = MarkahBuku::where('user_id', auth()->id())
            ->where('buku_id', $buku->id)
            ->get();

        return Inertia::render('App/Baca/Detail', [
            'buku' => $buku,
            'isFavorit' => $isFavorit,
            'markah' => $markah,
        ]);
    }

    public function bacaPdf(Buku $buku): Response
    {
        $buku->load(['penulis']);

        $markah = MarkahBuku::where('user_id', auth()->id())
            ->where('buku_id', $buku->id)
            ->get();

        return Inertia::render('App/Baca/Viewer', [
            'buku' => $buku,
            'markah' => $markah,
        ]);
    }

    public function jelajahi(Request $request): Response
    {
        $buku = Buku::with(['penulis', 'kategori'])
            ->when($request->cari, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('judul', 'like', "%{$request->cari}%")
                        ->orWhereHas('penulis', fn ($q3) => $q3->where('nama', 'like', "%{$request->cari}%"));
                });
            })
            ->when($request->kategori, fn ($q) => $q->whereHas('kategori', fn ($q2) => $q2->where('kategori.id', $request->kategori)))
            ->when($request->penulis, fn ($q) => $q->where('penulis_id', $request->penulis))
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        return Inertia::render('App/Jelajahi/Index', [
            'buku' => $buku,
            'kategori' => Kategori::orderBy('nama')->get(),
            'penulis' => Penulis::orderBy('nama')->get(),
            'cari' => $request->cari ?? '',
            'kategoriTerpilih' => $request->kategori ? (int) $request->kategori : null,
            'penulisTerpilih' => $request->penulis ? (int) $request->penulis : null,
        ]);
    }

    public function daftarFavorit(): Response
    {
        $favorit = BukuFavorit::with('buku.penulis')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return Inertia::render('App/Favorit/Index', [
            'favorit' => $favorit,
        ]);
    }

    public function daftarMarkah(): Response
    {
        $markah = MarkahBuku::with('buku.penulis')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return Inertia::render('App/Markah/Index', [
            'markah' => $markah,
        ]);
    }

    public function toggleFavorit(ToggleFavoritRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $bukuId = $request->buku_id;

        $existing = BukuFavorit::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['status' => 'removed']);
        }

        BukuFavorit::create([
            'user_id' => $userId,
            'buku_id' => $bukuId,
        ]);

        return response()->json(['status' => 'added']);
    }

    public function simpanMarkah(SimpanMarkahRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $bukuId = $request->buku_id;
        $halaman = $request->halaman;

        $existing = MarkahBuku::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->where('halaman', $halaman)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['status' => 'removed']);
        }

        MarkahBuku::create([
            'user_id' => $userId,
            'buku_id' => $bukuId,
            'halaman' => $halaman,
            'catatan' => $request->catatan,
        ]);

        return response()->json([
            'status' => 'added',
            'halaman' => $request->halaman,
            'catatan' => $request->catatan,
        ]);
    }

    public function catatStatistik(CatatStatistikRequest $request): JsonResponse
    {
        RiwayatBaca::create([
            'user_id' => auth()->id(),
            'buku_id' => $request->buku_id,
            'halaman_dibaca' => $request->halaman_dibaca,
            'tanggal' => $request->tanggal,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
