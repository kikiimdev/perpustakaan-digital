<?php

namespace App\Models;

use Database\Factories\BukuFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @use HasFactory<BukuFactory>
 */
#[Fillable(['penulis_id', 'judul', 'sinopsis', 'sampul', 'file_pdf', 'jumlah_halaman'])]
class Buku extends Model
{
    /** @use HasFactory<BukuFactory> */
    use HasFactory;

    protected $table = 'buku';

    protected function sampul(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }
                if (str_starts_with($value, 'http')) {
                    return $value;
                }
                $path = ltrim($value, '/');
                if (str_starts_with($path, 'storage/')) {
                    return asset($path);
                }

                return asset(Storage::url($value));
            },
        );
    }

    protected function filePdf(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }
                if (str_starts_with($value, 'http')) {
                    return $value;
                }
                $path = ltrim($value, '/');
                if (str_starts_with($path, 'storage/')) {
                    return asset($path);
                }

                return asset(Storage::url($value));
            },
        );
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(Penulis::class);
    }

    public function kategori(): BelongsToMany
    {
        return $this->belongsToMany(Kategori::class, 'buku_kategori');
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
