<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\RiwayatBaca;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function index(Request $request): Response
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));

        $range = $this->parseBulanRange($bulanAwal, $bulanAkhir);

        $bukuDitambahkan = Buku::whereBetween('created_at', $range)->count();
        $bukuTerbanyakDibaca = $this->queryBukuTerbanyakDibaca($range);
        $bukuTerfavorit = $this->queryBukuTerfavorit($range);
        $aktivitas = $this->queryAktivitas($range);
        $bukuBaruDaftar = $this->queryBukuDitambahkan($range);

        return Inertia::render('Admin/Laporan/Index', [
            'bulanAwal' => $bulanAwal,
            'bulanAkhir' => $bulanAkhir,
            'bukuDitambahkan' => $bukuDitambahkan,
            'bukuTerbanyakDibaca' => $bukuTerbanyakDibaca,
            'bukuTerfavorit' => $bukuTerfavorit,
            'aktivitas' => $aktivitas,
            'bukuBaruDaftar' => $bukuBaruDaftar,
        ]);
    }

    // --- Individual PDF exports ---

    public function cetakBukuDitambahkan(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $range = $this->parseBulanRange($bulanAwal, $bulanAkhir);
        $buku = $this->queryBukuDitambahkan($range);
        $total = Buku::whereBetween('created_at', $range)->count();

        $pdf = Pdf::loadView('pdf.buku-ditambahkan', compact('periode', 'buku', 'total'));

        return $pdf->download("laporan-buku-ditambahkan-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    public function cetakBukuTerbanyakDibaca(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $range = $this->parseBulanRange($bulanAwal, $bulanAkhir);
        $buku = $this->queryBukuTerbanyakDibaca($range, 25);

        $pdf = Pdf::loadView('pdf.buku-terbanyak-dibaca', compact('periode', 'buku'));

        return $pdf->download("laporan-buku-terbanyak-dibaca-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    public function cetakBukuTerfavorit(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $range = $this->parseBulanRange($bulanAwal, $bulanAkhir);
        $buku = $this->queryBukuTerfavorit($range, 25);

        $pdf = Pdf::loadView('pdf.buku-terfavorit', compact('periode', 'buku'));

        return $pdf->download("laporan-buku-terfavorit-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    public function cetakAktivitasMembaca(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $range = $this->parseBulanRange($bulanAwal, $bulanAkhir);
        $data = $this->queryAktivitas($range);

        $pdf = Pdf::loadView('pdf.aktivitas-membaca', compact('periode', 'data'));

        return $pdf->download("laporan-aktivitas-membaca-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    // --- Helpers ---

    private function parseBulanRange(string $awal, string $akhir): array
    {
        return [
            Carbon::createFromFormat('Y-m', $awal)->startOfMonth(),
            Carbon::createFromFormat('Y-m', $akhir)->endOfMonth(),
        ];
    }

    // --- Shared queries ---

    private function queryBukuDitambahkan(array $range)
    {
        return Buku::with('penulis')
            ->whereBetween('created_at', $range)
            ->latest()
            ->get();
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

    private function queryBukuTerfavorit(array $range, int $limit = 10)
    {
        return BukuFavorit::select(
            'buku_favorit.buku_id',
            'buku.judul',
            DB::raw('COUNT(*) as total_favorit')
        )
            ->join('buku', 'buku.id', '=', 'buku_favorit.buku_id')
            ->whereBetween('buku_favorit.created_at', $range)
            ->groupBy('buku_favorit.buku_id', 'buku.judul')
            ->orderByDesc('total_favorit')
            ->limit($limit)
            ->get();
    }

    private function queryAktivitas(array $range)
    {
        return [
            'total_pembaca' => RiwayatBaca::whereBetween('tanggal', $range)
                ->distinct('user_id')
                ->count('user_id'),
            'total_sesi' => RiwayatBaca::whereBetween('tanggal', $range)->count(),
            'total_halaman' => RiwayatBaca::whereBetween('tanggal', $range)->sum('halaman_dibaca'),
            'buku_dibaca' => RiwayatBaca::whereBetween('tanggal', $range)
                ->distinct('buku_id')
                ->count('buku_id'),
            'total_pengguna' => User::count(),
            'total_buku' => Buku::count(),
        ];
    }
}
