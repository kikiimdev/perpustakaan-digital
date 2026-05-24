<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoritRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'buku_id' => ['required', 'exists:buku,id'],
        ];
    }
}
