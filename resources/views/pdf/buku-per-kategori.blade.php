<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Buku per Kategori</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .periode { color: #666; font-size: 11px; margin-bottom: 8px; }
        .ringkasan { border: 1px solid #ccc; padding: 10px; margin: 12px 0; display: flex; gap: 24px; }
        .ringkasan-item { flex: 1; }
        .ringkasan-angka { font-size: 22px; font-weight: bold; color: #2563eb; }
        .ringkasan-label { font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .kategori-header { background: #e5e7eb; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h1>Laporan Buku per Kategori</h1>
    <p class="periode">Periode: {{ $periode }}</p>

    <div class="ringkasan">
        <div class="ringkasan-item">
            <div class="ringkasan-angka">{{ $total }}</div>
            <div class="ringkasan-label">Total Buku Ditambahkan</div>
        </div>
        <div class="ringkasan-item">
            <div class="ringkasan-angka">{{ $kategori->count() }}</div>
            <div class="ringkasan-label">Kategori Memiliki Buku</div>
        </div>
    </div>

    @forelse ($kategori as $kat)
    <table>
        <thead>
            <tr>
                <th colspan="4" class="kategori-header">Kategori: {{ $kat->nama }} ({{ $kat->buku_count }} Buku)</th>
            </tr>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 45%">Judul</th>
                <th style="width: 30%">Penulis</th>
                <th style="width: 20%">Halaman</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kat->buku as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->penulis?->nama ?? '-' }}</td>
                <td>{{ $item->jumlah_halaman }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @empty
    <table>
        <tr>
            <td style="text-align: center; padding: 20px;">Belum ada buku pada periode ini.</td>
        </tr>
    </table>
    @endforelse

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</div>
</body>
</html>