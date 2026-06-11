<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\Peran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        $admins = User::where('peran', Peran::Admin)->latest()->get();

        return Inertia::render('SuperAdmin/KelolaAdmin/Index', [
            'admins' => $admins,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SuperAdmin/KelolaAdmin/Form');
    }

    public function store(AdminStoreRequest $request): RedirectResponse
    {
        User::create([
            'nama_anggota' => $request->nama_anggota,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'peran' => Peran::Admin,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Admin berhasil ditambahkan.',
        ]);

        return redirect()->route('super-admin.kelola-admin.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('SuperAdmin/KelolaAdmin/Form', [
            'admin' => $user,
        ]);
    }

    public function update(AdminUpdateRequest $request, User $user): RedirectResponse
    {
        $data = [
            'nama_anggota' => $request->nama_anggota,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Admin berhasil diperbarui.',
        ]);

        return redirect()->route('super-admin.kelola-admin.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak dapat menghapus akun sendiri.',
            ]);

            return redirect()->back();
        }

        $user->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Admin berhasil dihapus.',
        ]);

        return redirect()->route('super-admin.kelola-admin.index');
    }
}
