<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\Peran;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'nama_anggota' => ['required', 'string', 'max:255'],
            'id_anggota' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'nama_anggota' => $input['nama_anggota'],
            'id_anggota' => strtolower($input['id_anggota']),
            'email' => strtolower($input['id_anggota']).'@perpustakaan.test',
            'password' => $input['password'],
            'peran' => Peran::User,
        ]);
    }
}
