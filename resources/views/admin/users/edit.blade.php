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

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Nama --}}
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}">
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}">
                        </div>

                        {{-- Password --}}
                        <div class="form-group">
                            <label>Password</label>
                            <small class="text-muted">(Kosongkan jika tidak diganti)</small>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror">
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update
                        </button>

                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </section>
</div>
@endsection
