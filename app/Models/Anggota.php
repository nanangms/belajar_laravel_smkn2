<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $fillable = [
        'nama_lengkap',
        'email',
        'nomor_hp',
        'alamat',
        'jenis_kelamin',
        'jurusan',
        'kelas',
        'foto',
    ];
}
