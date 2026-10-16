<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beritas = Berita::with('kategori')->latest()->get();
        return view('admin.berita.index', compact('beritas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = Kategori::all();
        return view('admin.berita.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required',
            'judul' => 'required',
            'isi' => 'required',
            'gambar' => 'image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Generate slug unik
        $slug = Str::slug($request->judul);
        $originalSlug = $slug;
        $count = 1;

        while (\App\Models\Berita::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Upload gambar
        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('berita', 'public');
        }

        Berita::create([
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'slug' => $slug,
            'isi' => $request->isi,
            'gambar' => $gambar,
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $berita = Berita::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.berita.edit', compact('berita', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $berita = Berita::findOrFail($id);

        $request->validate([
            'kategori_id' => 'required',
            'judul' => 'required',
            'isi' => 'required',
        ]);

        // Jika judul berubah → generate slug baru
        if ($request->judul != $berita->judul) {
            $slug = Str::slug($request->judul);
            $originalSlug = $slug;
            $count = 1;

            while (Berita::where('slug', $slug)->where('id', '!=', $berita->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        } else {
            $slug = $berita->slug;
        }

        // Upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }

            // Simpan gambar baru
            $berita->gambar = $request->file('gambar')->store('berita', 'public');
        }

        //Update data
        $berita->update([
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'slug' => $slug,
            'isi' => $request->isi,
            'gambar' => $berita->gambar,
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
        $berita->delete();

        return redirect()->route('berita.index')->with('success', 'Berita berhasil dihapus');
    }
}
