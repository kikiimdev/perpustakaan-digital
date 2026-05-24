<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Terfavorit</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .periode { color: #666; font-size: 11px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .rank-1, .rank-2, .rank-3 { font-weight: bold; }
        .rank-1 { color: #b45309; }
        .rank-2 { color: #64748b; }
        .rank-3 { color: #92400e; }
        .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h1>Laporan Buku Terfavorit</h1>
    <p class="periode">Periode: {{ $periode }}</p>

    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Judul</th>
                <th class="text-right">Total Favorit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($buku as $i => $item)
            <tr>
                <td class="{{ $i < 3 ? 'rank-'.($i+1) : '' }}">{{ $i + 1 }}</td>
                <td>{{ $item->judul }}</td>
                <td class="text-right">{{ number_format($item->total_favorit) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Belum ada favorit pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</div>
</body>
</html>
