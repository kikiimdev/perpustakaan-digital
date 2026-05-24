<?php

namespace App\Http\Responses;

use App\Enums\Peran;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $home = match ($user->peran) {
            Peran::SuperAdmin => '/super-admin/kelola-admin',
            Peran::Admin => '/admin/buku',
            Peran::User => '/app/dasbor',
        };

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended($home);
    }
}
