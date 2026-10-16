<!-- Menghubungkan dengan view template master -->
@extends('layouts.master')

<!-- isi bagian judul halaman -->
<!-- cara penulisan isi section yang pendek -->
@section('title', 'Halaman Profil')


<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('content')

    <p>Ini Adalah Halaman Profil</p>
    <img src="{{ asset('images/foto_alat.png') }}" width="200px" alt="Gambar eskafator">
    <p>
        Nama : Nanang Maulana Syarif <br>
        NISN : 00210001 <br>
        Kelas : XI RPL 2 <br>
        Alamat : Jl. Cempaka No. 123, Jakarta
    </p>

@endsection
