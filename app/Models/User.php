<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Peran;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'username', 'email', 'password', 'peran'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'peran' => Peran::class,
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->peran === Peran::SuperAdmin;
    }

    public function isAdmin(): bool
    {
        return $this->peran === Peran::Admin || $this->isSuperAdmin();
    }

    public function isUser(): bool
    {
        return $this->peran === Peran::User;
    }

    public function bukuFavorit(): HasMany
    {
        return $this->hasMany(BukuFavorit::class);
    }

    public function markahBuku(): HasMany
    {
        return $this->hasMany(MarkahBuku::class);
    }

    public function riwayatBaca(): HasMany
    {
        return $this->hasMany(RiwayatBaca::class);
    }
}
