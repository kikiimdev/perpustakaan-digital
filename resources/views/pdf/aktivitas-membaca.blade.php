<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Aktivitas Membaca</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .periode { color: #666; font-size: 11px; margin-bottom: 16px; }
        .cards { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
        .card { border: 1px solid #ccc; padding: 12px; flex: 1 1 130px; }
        .card-angka { font-size: 24px; font-weight: bold; color: #2563eb; }
        .card-label { font-size: 10px; color: #666; margin-top: 2px; }
        h2 { font-size: 14px; margin: 20px 0 8px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h1>Laporan Aktivitas Membaca</h1>
    <p class="periode">Periode: {{ $periode }}</p>

    <div class="cards">
        <div class="card">
            <div class="card-angka">{{ $data['total_pembaca'] }}</div>
            <div class="card-label">Pembaca Aktif</div>
        </div>
        <div class="card">
            <div class="card-angka">{{ $data['total_sesi'] }}</div>
            <div class="card-label">Total Sesi Baca</div>
        </div>
        <div class="card">
            <div class="card-angka">{{ number_format($data['total_halaman']) }}</div>
            <div class="card-label">Total Halaman Dibaca</div>
        </div>
        <div class="card">
            <div class="card-angka">{{ $data['buku_dibaca'] }}</div>
            <div class="card-label">Buku Dibaca</div>
        </div>
    </div>

    <h2>Ringkasan Keseluruhan</h2>
    <table>
        <tr><td>Total Pengguna Terdaftar</td><td class="text-right">{{ $data['total_pengguna'] }}</td></tr>
        <tr><td>Total Koleksi Buku</td><td class="text-right">{{ $data['total_buku'] }}</td></tr>
        <tr><td>Pembaca Aktif Periode Ini</td><td class="text-right">{{ $data['total_pembaca'] }}</td></tr>
        <tr><td>Total Sesi Membaca</td><td class="text-right">{{ $data['total_sesi'] }}</td></tr>
        <tr><td>Total Halaman Dibaca</td><td class="text-right">{{ number_format($data['total_halaman']) }}</td></tr>
        <tr><td>Buku yang Dibaca</td><td class="text-right">{{ $data['buku_dibaca'] }}</td></tr>
    </table>

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</div>
</body>
</html>
