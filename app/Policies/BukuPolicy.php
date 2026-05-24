<?php

namespace App\Policies;

use App\Models\Buku;
use App\Models\User;

class BukuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Buku $buku): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Buku $buku): bool
    {
        return $user->isAdmin();
    }
}
