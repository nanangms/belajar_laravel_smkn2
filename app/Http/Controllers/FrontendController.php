<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        $berita = \App\Models\Berita::latest()->take(3)->get();
        return view('frontend.index', compact('berita'));
    }
    public function news()
    {
        return view('frontend.news');
    }
    public function galeri()
    {
        return view('frontend.galeri');
    }
    public function tentang()
    {
        return view('frontend.tentang');
    }

    public function pendaftaran()
    {
        return view('frontend.pendaftaran');
    }
}
