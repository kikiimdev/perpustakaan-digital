<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Peran;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KartuAnggotaController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::where('peran', Peran::User)
            ->when($request->cari, fn ($q) => $q->where(fn ($sub) => $sub
                ->where('nama_anggota', 'like', "%{$request->cari}%")
                ->orWhere('email', 'like', "%{$request->cari}%")
            ))
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return Inertia::render('Admin/KartuAnggota/Index', [
            'users' => $users,
            'cari' => $request->cari ?? '',
        ]);
    }

    public function cetak(User $user): Response
    {
        return Inertia::render('Admin/KartuAnggota/Cetak', [
            'anggota' => $user,
        ]);
    }
}
