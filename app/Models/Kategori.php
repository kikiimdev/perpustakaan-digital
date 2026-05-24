<?php

namespace App\Models;

use Database\Factories\KategoriFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @use HasFactory<KategoriFactory>
 */
#[Fillable(['nama', 'slug'])]
class Kategori extends Model
{
    /** @use HasFactory<KategoriFactory> */
    use HasFactory;
    protected $table = 'kategori';

    public function buku(): BelongsToMany
    {
        return $this->belongsToMany(Buku::class, 'buku_kategori');
    }
}
