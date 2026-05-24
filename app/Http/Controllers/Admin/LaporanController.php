<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\BukuFavorit;
use App\Models\RiwayatBaca;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function index(Request $request): Response
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));

        $bukuDitambahkan = Buku::whereRaw("strftime('%Y-%m', created_at) BETWEEN ? AND ?", [$bulanAwal, $bulanAkhir])->count();
        $bukuTerbanyakDibaca = $this->queryBukuTerbanyakDibaca($bulanAwal, $bulanAkhir);
        $bukuTerfavorit = $this->queryBukuTerfavorit($bulanAwal, $bulanAkhir);
        $aktivitas = $this->queryAktivitas($bulanAwal, $bulanAkhir);
        $bukuBaruDaftar = $this->queryBukuDitambahkan($bulanAwal, $bulanAkhir);

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

        $buku = $this->queryBukuDitambahkan($bulanAwal, $bulanAkhir);
        $total = Buku::whereRaw("strftime('%Y-%m', created_at) BETWEEN ? AND ?", [$bulanAwal, $bulanAkhir])->count();

        $pdf = Pdf::loadView('pdf.buku-ditambahkan', compact('periode', 'buku', 'total'));

        return $pdf->download("laporan-buku-ditambahkan-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    public function cetakBukuTerbanyakDibaca(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $buku = $this->queryBukuTerbanyakDibaca($bulanAwal, $bulanAkhir, 25);

        $pdf = Pdf::loadView('pdf.buku-terbanyak-dibaca', compact('periode', 'buku'));

        return $pdf->download("laporan-buku-terbanyak-dibaca-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    public function cetakBukuTerfavorit(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $buku = $this->queryBukuTerfavorit($bulanAwal, $bulanAkhir, 25);

        $pdf = Pdf::loadView('pdf.buku-terfavorit', compact('periode', 'buku'));

        return $pdf->download("laporan-buku-terfavorit-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    public function cetakAktivitasMembaca(Request $request)
    {
        $bulanAwal = $request->input('bulan_awal', now()->format('Y-m'));
        $bulanAkhir = $request->input('bulan_akhir', now()->format('Y-m'));
        $periode = "$bulanAwal s/d $bulanAkhir";

        $data = $this->queryAktivitas($bulanAwal, $bulanAkhir);

        $pdf = Pdf::loadView('pdf.aktivitas-membaca', compact('periode', 'data'));

        return $pdf->download("laporan-aktivitas-membaca-{$bulanAwal}-{$bulanAkhir}.pdf");
    }

    // --- Shared queries ---

    private function queryBukuDitambahkan(string $awal, string $akhir)
    {
        return Buku::with('penulis')
            ->whereRaw("strftime('%Y-%m', created_at) BETWEEN ? AND ?", [$awal, $akhir])
            ->latest()
            ->get();
    }

    private function queryBukuTerbanyakDibaca(string $awal, string $akhir, int $limit = 10)
    {
        return RiwayatBaca::select(
            'riwayat_baca.buku_id',
            'buku.judul',
            DB::raw('SUM(riwayat_baca.halaman_dibaca) as total_halaman')
        )
            ->join('buku', 'buku.id', '=', 'riwayat_baca.buku_id')
            ->whereRaw("strftime('%Y-%m', riwayat_baca.tanggal) BETWEEN ? AND ?", [$awal, $akhir])
            ->groupBy('riwayat_baca.buku_id', 'buku.judul')
            ->orderByDesc('total_halaman')
            ->limit($limit)
            ->get();
    }

    private function queryBukuTerfavorit(string $awal, string $akhir, int $limit = 10)
    {
        return BukuFavorit::select(
            'buku_favorit.buku_id',
            'buku.judul',
            DB::raw('COUNT(*) as total_favorit')
        )
            ->join('buku', 'buku.id', '=', 'buku_favorit.buku_id')
            ->whereRaw("strftime('%Y-%m', buku_favorit.created_at) BETWEEN ? AND ?", [$awal, $akhir])
            ->groupBy('buku_favorit.buku_id', 'buku.judul')
            ->orderByDesc('total_favorit')
            ->limit($limit)
            ->get();
    }

    private function queryAktivitas(string $awal, string $akhir)
    {
        return [
            'total_pembaca' => RiwayatBaca::whereRaw("strftime('%Y-%m', tanggal) BETWEEN ? AND ?", [$awal, $akhir])
                ->distinct('user_id')
                ->count('user_id'),
            'total_sesi' => RiwayatBaca::whereRaw("strftime('%Y-%m', tanggal) BETWEEN ? AND ?", [$awal, $akhir])->count(),
            'total_halaman' => RiwayatBaca::whereRaw("strftime('%Y-%m', tanggal) BETWEEN ? AND ?", [$awal, $akhir])->sum('halaman_dibaca'),
            'buku_dibaca' => RiwayatBaca::whereRaw("strftime('%Y-%m', tanggal) BETWEEN ? AND ?", [$awal, $akhir])
                ->distinct('buku_id')
                ->count('buku_id'),
            'total_pengguna' => User::count(),
            'total_buku' => Buku::count(),
        ];
    }
}
