<nav class="main-header navbar navbar-expand-md navbar-light">
    <div class="container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="{{ asset('images') }}/foto_alat.png" alt="Logo" class="brand-image img-circle elevation-2">
            <span class="brand-text font-weight-light">SMKN 2 Kota Jambi</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarMain">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link active">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/news') }}" class="nav-link">Berita</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/galeri') }}" class="nav-link">Galeri</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/tentang') }}" class="nav-link">Tentang</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pendaftaran') }}" class="nav-link">Pendaftaran</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/login') }}" class="nav-link text-primary"><i class="fas fa-sign-in-alt mr-1"></i> Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
