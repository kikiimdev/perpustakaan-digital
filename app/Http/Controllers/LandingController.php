<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\RiwayatBaca;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Most recent added books this month
        $recentBooks = Buku::with(['penulis', 'kategori'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->latest()
            ->limit(4)
            ->get();

        // Most read books this month
        $mostReadBookIds = RiwayatBaca::select('buku_id', DB::raw('count(*) as total'))
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->groupBy('buku_id')
            ->orderByDesc('total')
            ->limit(4)
            ->pluck('buku_id');

        $mostReadBooks = Buku::with(['penulis', 'kategori'])
            ->whereIn('id', $mostReadBookIds)
            ->get()
            ->sortBy(fn ($buku) => $mostReadBookIds->search($buku->id));

        // Most favorite books this month
        $mostFavBookIds = BukuFavorit::select('buku_id', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('buku_id')
            ->orderByDesc('total')
            ->limit(4)
            ->pluck('buku_id');

        $mostFavoriteBooks = Buku::with(['penulis', 'kategori'])
            ->whereIn('id', $mostFavBookIds)
            ->get()
            ->sortBy(fn ($buku) => $mostFavBookIds->search($buku->id));

        return inertia('Welcome', [
            'recentBooks' => $recentBooks,
            'mostReadBooks' => $mostReadBooks,
            'mostFavoriteBooks' => $mostFavoriteBooks,
        ]);
    }
}
