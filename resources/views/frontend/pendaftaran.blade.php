@extends('frontend.layouts.master')
@section('title', 'Pendaftaran')

@section('content')
    <div class="content-wrapper ml-0">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <h1 class="m-0">Pendaftaran</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulir Pendaftaran Anggota STC 2027</h3>
                    </div>
                    <form method="POST" action="{{ route('pendaftaran.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <p class="mb-1">Pendaftaran belum dapat dikirim:</p>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                    placeholder="Masukkan nama lengkap" value="{{ old('nama_lengkap') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="nama@email.com" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="nomor_hp">Nomor HP/WA</label>
                                <input type="tel" class="form-control" id="nomor_hp" name="nomor_hp"
                                    placeholder="Contoh: 081234567890" value="{{ old('nomor_hp') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap"
                                    required>{{ old('alamat') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="jenis_kelamin_laki_laki" name="jenis_kelamin"
                                            class="custom-control-input" value="Laki-laki"
                                            {{ old('jenis_kelamin') === 'Laki-laki' ? 'checked' : '' }} required>
                                        <label class="custom-control-label" for="jenis_kelamin_laki_laki">Laki-laki</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="jenis_kelamin_perempuan" name="jenis_kelamin"
                                            class="custom-control-input" value="Perempuan"
                                            {{ old('jenis_kelamin') === 'Perempuan' ? 'checked' : '' }} required>
                                        <label class="custom-control-label" for="jenis_kelamin_perempuan">Perempuan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jurusan">Pilihan Jurusan</label>
                                <select class="form-control" id="jurusan" name="jurusan" required>
                                    <option value="">Pilih jurusan</option>
                                    <option value="Rekayasa Perangkat Lunak"
                                        {{ old('jurusan') === 'Rekayasa Perangkat Lunak' ? 'selected' : '' }}>
                                        Rekayasa Perangkat Lunak</option>
                                    <option value="Teknik Komputer dan Jaringan"
                                        {{ old('jurusan') === 'Teknik Komputer dan Jaringan' ? 'selected' : '' }}>
                                        Teknik Komputer dan Jaringan</option>
                                    <option value="Akuntansi" {{ old('jurusan') === 'Akuntansi' ? 'selected' : '' }}>
                                        Akuntansi</option>
                                    <option value="Administrasi Perkantoran"
                                        {{ old('jurusan') === 'Administrasi Perkantoran' ? 'selected' : '' }}>
                                        Administrasi Perkantoran</option>
                                </select>

                            </div>

                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="form-control" id="kelas" name="kelas" required>
                                    <option value="">Pilih kelas</option>
                                    <option value="X" {{ old('kelas') === 'X' ? 'selected' : '' }}>X</option>
                                    <option value="XI" {{ old('kelas') === 'XI' ? 'selected' : '' }}>XI
                                    </option>
                                    <option value="XII" {{ old('kelas') === 'XII' ? 'selected' : '' }}>XII
                                    </option>
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label for="foto">Upload Foto</label>
                                <input type="file" class="form-control-file" id="foto" name="foto"
                                    accept="image/*" required>
                                <small class="form-text text-muted">Format gambar: JPG, PNG, atau WEBP.</small>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="reset" class="btn btn-default">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection
