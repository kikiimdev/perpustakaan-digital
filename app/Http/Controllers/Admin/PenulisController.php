<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penulis;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PenulisController extends Controller
{
    public function index(Request $request): Response
    {
        $penulis = Penulis::withCount('buku')
            ->when($request->cari, fn ($q) => $q->where('nama', 'like', "%{$request->cari}%"))
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return Inertia::render('Admin/Penulis/Index', [
            'penulis' => $penulis,
            'cari' => $request->cari ?? '',
        ]);
    }

    public function show(Penulis $penulis): Response
    {
        $buku = $penulis->buku()->with('kategori')->latest()->paginate(12);

        return Inertia::render('Admin/Penulis/Show', [
            'penulis' => $penulis,
            'buku' => $buku,
        ]);
    }
}
