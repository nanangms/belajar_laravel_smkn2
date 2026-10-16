@extends('admin.layouts.master')
@section('title', 'Tambah Berita')
@push('style')
@endpush
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Tambah Berita</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Berita</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
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

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Tambah</h3>
                    </div>
<form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-body">

                {{-- Kategori --}}
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Judul --}}
                <div class="form-group">
                    <label>Judul Berita</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                {{-- Isi --}}
                <div class="form-group">
                    <label>Isi Berita</label>
                    <textarea name="isi" rows="6" class="form-control" required></textarea>
                </div>

                {{-- Gambar --}}
                <div class="form-group">
                    <label>Upload Gambar</label>
                    <input type="file" name="gambar" class="form-control" onchange="previewImage(event)">
                </div>

                {{-- Preview --}}
                <img id="preview" width="200" style="display:none;" class="img-thumbnail">

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('berita.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>

                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@push('script')
<script>
function previewImage(event){
    const reader = new FileReader();
    reader.onload = function(){
        const preview = document.getElementById('preview');
        preview.src = reader.result;
        preview.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
