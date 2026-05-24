<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;

class CatatStatistikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buku_id' => ['required', 'exists:buku,id'],
            'halaman_dibaca' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
        ];
    }
}
