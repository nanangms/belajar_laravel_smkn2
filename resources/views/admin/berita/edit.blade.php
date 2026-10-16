@extends('admin.layouts.master')
@section('title', 'Edit User')
@push('style')
@endpush
@section('content')
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit User</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="icon fas fa-ban"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit</h3>
                    </div>

                    <form action="{{ route('berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            {{-- Kategori --}}
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori_id" class="form-control" required>
                                    @foreach ($kategoris as $k)
                                        <option value="{{ $k->id }}"
                                            {{ $k->id == $berita->kategori_id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Judul --}}
                            <div class="form-group">
                                <label>Judul Berita</label>
                                <input type="text" name="judul" value="{{ $berita->judul }}" class="form-control"
                                    required>
                            </div>

                            {{-- Isi --}}
                            <div class="form-group">
                                <label>Isi Berita</label>
                                <textarea name="isi" rows="6" class="form-control" required>{{ $berita->isi }}</textarea>
                            </div>

                            {{-- Gambar Lama --}}
                            @if ($berita->gambar)
                                <div class="mb-3">
                                    <label>Gambar Saat Ini</label><br>
                                    <img src="{{ asset('storage/' . $berita->gambar) }}" width="200" class="img-thumbnail">
                                </div>
                            @endif

                            {{-- Upload Baru --}}
                            <div class="form-group">
                                <label>Ganti Gambar (opsional)</label>
                                <input type="file" name="gambar" class="form-control" onchange="previewImage(event)">
                            </div>

                            {{-- Preview --}}
                            <img id="preview" width="200" style="display:none;" class="img-thumbnail">

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                Update
                            </button>

                            <a href="{{ route('berita.index') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection
@push('script')
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('preview');
                preview.src = reader.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endpush
