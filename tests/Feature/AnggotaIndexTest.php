<?php

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows registered anggota to authenticated users', function () {
    Anggota::create([
        'nama_lengkap' => 'Siswa Contoh',
        'email' => 'siswa@example.com',
        'nomor_hp' => '08123456789',
        'alamat' => 'Jl. Pendidikan',
        'jenis_kelamin' => 'Laki-laki',
        'jurusan' => 'Rekayasa Perangkat Lunak',
        'kelas' => 'XII',
        'foto' => 'anggota/siswa.jpg',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('anggota.index'))
        ->assertOk()
        ->assertViewIs('admin.anggota.index')
        ->assertSee('Siswa Contoh')
        ->assertSee('siswa@example.com');
});

it('requires authentication to view anggota', function () {
    $this->get(route('anggota.index'))
        ->assertRedirect(route('login'));
});
