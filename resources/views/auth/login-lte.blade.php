@extends('auth.master')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Admin</b>LTE</a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    {{-- EMAIL --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email" value="{{ old('email') }}" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>

                        @error('email')
                            <span class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @error('password')
                            <span class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                Sign In
                            </button>
                        </div>
                    </div>
                </form>

                {{-- FORGOT PASSWORD --}}
                @if (Route::has('password.request'))
                    <p class="mb-1">
                        <a href="{{ route('password.request') }}">
                            I forgot my password
                        </a>
                    </p>
                @endif

                {{-- REGISTER --}}
                @if (Route::has('register'))
                    <p class="mb-0">
                        <a href="{{ route('register') }}" class="text-center">
                            Register a new membership
                        </a>
                    </p>
                @endif

            </div>
        </div>
    </div>
@endsection
