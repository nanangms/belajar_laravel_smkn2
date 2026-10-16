<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
     protected $fillable = [
        'kategori_id',
        'judul',
        'slug',
        'isi',
        'gambar'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
