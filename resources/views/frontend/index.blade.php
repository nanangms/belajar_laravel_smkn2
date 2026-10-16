@extends('frontend.layouts.master')
@section('title', 'Beranda')

@section('content')
    <div class="content-wrapper ml-0">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <h1 class="m-0">Selamat Datang di SMKN 2 Kota Jambi</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="callout callout-primary">
                    <h5><i class="fas fa-school mr-2"></i>Informasi sekolah dalam satu tempat</h5>
                    <p class="mb-0">Temukan berita, kegiatan, dan informasi terbaru dari sekolah dengan mudah.</p>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Berita Terbaru</h3>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    @foreach ($berita as $item)
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <img src="{{ asset('storage/' . $item->gambar) }}" class="card-img-top"
                                                    alt="{{ $item->judul }}">
                                                <div class="card-body">
                                                    <a href="{{ route('berita.show', $item->id) }}"
                                                        class="text-decoration-none">
                                                        <h5 class="card-title mb-3"><strong>{{ $item->judul }}</strong>
                                                        </h5>
                                                    </a>
                                                    <p class="card-text">{{ Str::limit($item->isi, 100) }}</p>
                                                    <a href="{{ route('berita.show', $item->id) }}"
                                                        class="btn btn-primary">Baca Selengkapnya</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
