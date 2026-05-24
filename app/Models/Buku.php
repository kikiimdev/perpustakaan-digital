<?php

namespace App\Models;

use Database\Factories\BukuFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use HasFactory<BukuFactory>
 */
#[Fillable(['penulis_id', 'judul', 'sinopsis', 'sampul', 'file_pdf', 'jumlah_halaman'])]
class Buku extends Model
{
    /** @use HasFactory<BukuFactory> */
    use HasFactory;
    protected $table = 'buku';

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
