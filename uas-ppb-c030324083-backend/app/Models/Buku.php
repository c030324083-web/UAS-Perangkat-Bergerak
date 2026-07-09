<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buku extends Model
{
    protected $fillable = [
        'kode_buku', 'judul', 'jenis_buku_id', 'pengarang', 'penerbit', 'sinopsis'
    ];

    // Relasi Many-to-One: Buku termasuk dalam sebuah Jenis Buku
    public function jenisBuku(): BelongsTo
    {
        return $this->belongsTo(JenisBuku::class, 'jenis_buku_id');
    }
}