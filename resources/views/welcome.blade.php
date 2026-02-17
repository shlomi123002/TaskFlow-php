@extends('layout')

@section('title', 'Welcome to TaskFlow')

@section('content')
    <div class="container">
        <h1>Welcome to TaskFlow</h1>
        <p class="text-center small-text" style="margin-bottom: 2rem; color: #666;">
            Organize your work, manage your projects, and track your tasks efficiently
        </p>

        <div style="text-align: center; margin-bottom: 2rem;">
            <p style="color: #666; margin-bottom: 1.5rem;">
                Login to your account to get started
            </p>
            <a href="/login" class="btn btn-primary" style="display: inline-block; text-decoration: none; max-width: 200px;">
                Login
            </a>
        </div>

        <div style="text-align: center; border-top: 1px solid #e0e0e0; padding-top: 1.5rem;">
            <p class="small-text">
                Don't have an account?
                <a href="/register" class="register-link">Register here</a>
            </p>
        </div>
    </div>
@endsection
