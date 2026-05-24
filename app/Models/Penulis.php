<?php

namespace App\Models;

use Database\Factories\PenulisFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @use HasFactory<PenulisFactory>
 */
#[Fillable(['nama', 'biografi'])]
class Penulis extends Model
{
    /** @use HasFactory<PenulisFactory> */
    use HasFactory;
    protected $table = 'penulis';

    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class);
    }
}
