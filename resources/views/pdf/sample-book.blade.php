<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 14px; line-height: 1.8; color: #333; margin: 0; padding: 0; }
        .page { padding: 40px 60px; page-break-inside: avoid; }
        h1 { font-size: 22px; margin-bottom: 24px; text-align: center; }
        p { margin-bottom: 12px; }
        .page-number { text-align: center; font-size: 10px; color: #999; margin-top: 20px; }
    </style>
</head>
<body>
    @for ($i = 1; $i <= $halaman; $i++)
        <div class="page" @if($i > 1) style="page-break-before: always;" @endif>
            <h1>{{ $judul }}</h1>
            <p>
                Ini adalah halaman ke-{{ $i }} dari buku <strong>{{ $judul }}</strong>. 
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor 
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis 
                nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <p>
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore 
                eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt 
                in culpa qui officia deserunt mollit anim id est laborum.
            </p>
            <p>
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium 
                doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore 
                veritatis et quasi architecto beatae vitae dicta sunt explicabo.
            </p>
            <p>
                Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, 
                sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. 
                Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, 
                adipisci velit.
            </p>
            <p>
                At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis 
                praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias 
                excepturi sint occaecati cupiditate non provident.
            </p>
            <div class="page-number">Halaman {{ $i }}</div>
        </div>
    @endfor
</body>
</html>
