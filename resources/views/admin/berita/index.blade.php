@extends('admin.layouts.master')
@section('title', 'Berita')
@push('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Berita</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Berita</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                {{-- Alert Success --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="icon fas fa-check"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('berita.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add
                            Berita</a>

                    </div>
                    <div class="card-body" style="display: block;">


                        <table class="table table-bordered table-hover" id="datatable">
                            <thead>
                                <tr>
                                    <th style="width: 10px">No</th>

                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Gambar</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($beritas as $berita)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $berita->judul }}</td>
                                        <td>{{ $berita->kategori->nama_kategori }}</td>
                                        <td><img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}"
                                                width="100"></td>
                                        <td>
                                            <a href="{{ route('berita.edit', $berita->id) }}" class="btn btn-sm btn-warning"><i
                                                    class="fas fa-edit"></i></a>
                                            <form action="{{ route('berita.destroy', $berita->id) }}" method="POST"
                                                style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure?')"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#datatable").DataTable();
        });
    </script>
@endpush
