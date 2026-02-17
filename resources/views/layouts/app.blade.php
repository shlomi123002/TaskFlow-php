<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Taskflow')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { margin-bottom: 16px; }
        a { margin-right: 8px; }
        .flash { padding:8px; background:#e6ffe6; border:1px solid #b7ffb7; margin-bottom:12px; }
        .error { color:#c00; }
    </style>
    @stack('head')
</head>
<body>
    <nav>
        <a href="/">Home</a>
        <a href="/docs">Docs</a>
        @auth
            <a href="/search">Search</a>
            <form method="POST" action="/logout" style="display:inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @else
            <a href="/register">Register</a>
            <a href="/login">Login</a>
        @endauth
    </nav>

    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>
