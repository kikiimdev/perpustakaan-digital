<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BukuPreviewController extends Controller
{
    public function show(Buku $buku)
    {
        $buku->load(['penulis', 'kategori']);

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $readsThisMonth = DB::table('riwayat_baca')
            ->where('buku_id', $buku->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->count();

        $favoritesThisMonth = DB::table('buku_favorit')
            ->where('buku_id', $buku->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $totalReads = DB::table('riwayat_baca')
            ->where('buku_id', $buku->id)
            ->count();

        $totalFavorites = DB::table('buku_favorit')
            ->where('buku_id', $buku->id)
            ->count();

        return inertia('Buku/Preview', [
            'buku' => $buku,
            'stats' => [
                'readsThisMonth' => $readsThisMonth,
                'favoritesThisMonth' => $favoritesThisMonth,
                'totalReads' => $totalReads,
                'totalFavorites' => $totalFavorites,
            ],
        ]);
    }
}
