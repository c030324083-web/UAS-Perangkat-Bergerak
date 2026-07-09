<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBuku extends Model
{
    protected $fillable = ['nama_jenis'];

    // Relasi One-to-Many: Satu jenis memiliki banyak buku
    public function bukus(): HasMany
    {
        return $this->hasMany(Buku::class, 'jenis_buku_id');
    }
}