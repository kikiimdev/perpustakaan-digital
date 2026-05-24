<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Ditambahkan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .periode { color: #666; font-size: 11px; margin-bottom: 8px; }
        .ringkasan { border: 1px solid #ccc; padding: 10px; margin: 12px 0; display: flex; gap: 24px; }
        .ringkasan-item { flex: 1; }
        .ringkasan-angka { font-size: 22px; font-weight: bold; color: #2563eb; }
        .ringkasan-label { font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h1>Laporan Buku Ditambahkan</h1>
    <p class="periode">Periode: {{ $periode }}</p>

    <div class="ringkasan">
        <div class="ringkasan-item">
            <div class="ringkasan-angka">{{ $total }}</div>
            <div class="ringkasan-label">Total Buku Ditambahkan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Halaman</th>
                <th>Tgl Ditambahkan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($buku as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->penulis?->nama ?? '-' }}</td>
                <td>{{ $item->jumlah_halaman }}</td>
                <td>{{ $item->created_at->translatedFormat('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5">Belum ada buku ditambahkan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</div>
</body>
</html>
