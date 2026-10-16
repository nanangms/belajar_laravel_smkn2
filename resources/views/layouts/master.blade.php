<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blog')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #0c6af3;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin: 0 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #0c6af3;
            color: #fff;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    @include('layouts.header')

    <div class="container">
        @yield('content')
    </div>

    @include('layouts.footer')
</body>
</html>
