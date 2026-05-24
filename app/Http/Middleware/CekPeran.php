<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekPeran
{
    public function handle(Request $request, Closure $next, string ...$perans): mixed
    {
        if (! $request->user()) {
            abort(401);
        }

        $peranPengguna = $request->user()->peran->value;

        if (! in_array($peranPengguna, $perans, true)) {
            abort(403);
        }

        return $next($request);
    }
}
