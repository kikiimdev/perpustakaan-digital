<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\RiwayatBaca;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PerpustakaanController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $bulanIni = RiwayatBaca::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $totalHalaman = RiwayatBaca::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('halaman_dibaca');

        $bukuDibacaBulanIni = RiwayatBaca::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->distinct('buku_id')
            ->count('buku_id');

        $rekomendasi = Buku::with(['penulis', 'kategori'])
            ->whereDoesntHave('riwayatBaca', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->limit(6)
            ->get();

        $favorit = BukuFavorit::with('buku.penulis')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        return Inertia::render('App/Dasbor/Index', [
            'statistik' => [
                'bacaan_bulan_ini' => $bulanIni,
                'total_halaman' => $totalHalaman,
                'buku_dibaca' => $bukuDibacaBulanIni,
            ],
            'rekomendasi' => $rekomendasi,
            'favorit' => $favorit,
        ]);
    }
}
