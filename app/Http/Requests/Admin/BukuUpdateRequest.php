<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BukuUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'penulis_id' => ['required', 'exists:penulis,id'],
            'judul' => ['required', 'string', 'max:255'],
            'sinopsis' => ['required', 'string'],
            'sampul' => ['nullable', 'image', 'max:2048'],
            'file_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
            'jumlah_halaman' => ['required', 'integer', 'min:1'],
            'kategori_ids' => ['required', 'array'],
            'kategori_ids.*' => ['exists:kategori,id'],
        ];
    }
}
