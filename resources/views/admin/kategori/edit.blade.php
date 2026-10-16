@extends('admin.layouts.master')
@section('title', 'Edit Kategori')
@push('style')
@endpush
@section('content')
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Kategori</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
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
                        <h3 class="card-title">Form Edit Kategori</h3>
                    </div>

                    <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <div class="form-group">
                                <label>Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control"
                                    value="{{ old('nama_kategori', $kategori->nama_kategori) }}">
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection
