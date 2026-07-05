<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatBaca;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DasborController extends Controller
{
    public function index(Request $request): Response
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));

        $range = $this->parseBulanRange($bulanAwal, $bulanAkhir);

        $riwayatBacaPerHari = $this->queryRiwayatBacaPerHari($range);
        $bukuTerbanyakDibaca = $this->queryBukuTerbanyakDibaca($range);
        $riwayatBacaPerGender = $this->queryRiwayatBacaPerGender($range);
        $kategoriTerbanyakDibaca = $this->queryKategoriTerbanyakDibaca($range);

        return Inertia::render('Admin/Dasbor/Index', [
            'bulanAwal' => $bulanAwal,
            'bulanAkhir' => $bulanAkhir,
            'riwayatBacaPerHari' => $riwayatBacaPerHari,
            'bukuTerbanyakDibaca' => $bukuTerbanyakDibaca,
            'riwayatBacaPerGender' => $riwayatBacaPerGender,
            'kategoriTerbanyakDibaca' => $kategoriTerbanyakDibaca,
        ]);
    }

    private function parseBulanRange(string $awal, string $akhir): array
    {
        return [
            Carbon::createFromFormat('Y-m', $awal)->startOfMonth(),
            Carbon::createFromFormat('Y-m', $akhir)->endOfMonth(),
        ];
    }

    private function queryRiwayatBacaPerHari(array $range)
    {
        $results = RiwayatBaca::select(
            'tanggal',
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('tanggal', $range)
            ->groupBy('tanggal')
            ->get()
            ->keyBy(fn ($r) => $r->tanggal->format('Y-m-d'));

        $period = CarbonPeriod::create($range[0], $range[1]);
        $filled = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $filled[] = [
                'tanggal' => $dateString,
                'total' => $results[$dateString]->total ?? 0,
            ];
        }

        return $filled;
    }

    private function queryBukuTerbanyakDibaca(array $range, int $limit = 10)
    {
        return RiwayatBaca::select(
            'riwayat_baca.buku_id',
            'buku.judul',
            DB::raw('SUM(riwayat_baca.halaman_dibaca) as total_halaman')
        )
            ->join('buku', 'buku.id', '=', 'riwayat_baca.buku_id')
            ->whereBetween('riwayat_baca.tanggal', $range)
            ->groupBy('riwayat_baca.buku_id', 'buku.judul')
            ->orderByDesc('total_halaman')
            ->limit($limit)
            ->get();
    }

    private function queryRiwayatBacaPerGender(array $range)
    {
        return RiwayatBaca::select(
            DB::raw("COALESCE(users.jenis_kelamin, 'tidak_diketahui') as gender"),
            DB::raw('COUNT(*) as total')
        )
            ->join('users', 'users.id', '=', 'riwayat_baca.user_id')
            ->whereBetween('riwayat_baca.tanggal', $range)
            ->groupBy('gender')
            ->get();
    }

    private function queryKategoriTerbanyakDibaca(array $range, int $limit = 10)
    {
        return RiwayatBaca::select(
            'kategori.nama',
            DB::raw('COUNT(riwayat_baca.id) as total_dibaca')
        )
            ->join('buku', 'buku.id', '=', 'riwayat_baca.buku_id')
            ->join('buku_kategori', 'buku_kategori.buku_id', '=', 'buku.id')
            ->join('kategori', 'kategori.id', '=', 'buku_kategori.kategori_id')
            ->whereBetween('riwayat_baca.tanggal', $range)
            ->groupBy('kategori.id', 'kategori.nama')
            ->orderByDesc('total_dibaca')
            ->limit($limit)
            ->get();
    }
}
