<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use Illuminate\Http\RedirectResponse;

class PendaftaranController extends Controller
{
    public function pendaftaran_submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:anggotas,email'],
            'nomor_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:5000'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'jurusan' => [
                'required',
                'in:Rekayasa Perangkat Lunak,Teknik Komputer dan Jaringan,Akuntansi,Administrasi Perkantoran',
            ],
            'kelas' => ['required', 'in:X,XI,XII'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $fotoPath = $request->file('foto')->store('anggota', 'public');

        if ($fotoPath === false) {
            abort(500, 'Foto pendaftaran gagal disimpan.');
        }

        $validated['foto'] = $fotoPath;
        Anggota::create($validated);

        return redirect()
            ->route('pendaftaran')
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }
}
