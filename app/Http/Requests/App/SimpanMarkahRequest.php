<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;

class SimpanMarkahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buku_id' => ['required', 'exists:buku,id'],
            'halaman' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
