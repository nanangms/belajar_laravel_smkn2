<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title') | SMKN 2 Kota Jambi</title>

	<link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="{{ asset('adminlte') }}/dist/css/adminlte.min.css">
</head>

<body class="hold-transition layout-top-nav">
	<div class="wrapper">
		@include('frontend.layouts.header')
		@yield('content')
		@include('frontend.layouts.footer')
	</div>

	<script src="{{ asset('adminlte') }}/plugins/jquery/jquery.min.js"></script>
	<script src="{{ asset('adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="{{ asset('adminlte') }}/dist/js/adminlte.min.js"></script>
</body>

</html>
