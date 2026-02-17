@extends('layout')

@section('title', 'Login - TaskFlow')

@section('content')
    <div class="container">
        <h1>Login to TaskFlow</h1>

        @if($errors->any())
            <div class="alert alert-error">
                <ul class="error-list">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <small style="color: #721c24;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <small style="color: #721c24;">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <div class="text-center mt-2">
            <p class="small-text">
                Don't have an account?
                <a href="/register" class="register-link">Register here</a>
            </p>
        </div>
    </div>
@endsection
