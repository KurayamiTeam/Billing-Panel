<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurayami Panel</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--color-accent);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        nav a {
            color: var(--text-secondary);
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }
        nav a:hover {
            color: var(--color-accent);
        }
        main {
            flex: 1;
            padding: 40px;
        }
        footer {
            background-color: var(--bg-surface);
            border-top: 1px solid var(--border-color);
            padding: 20px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 14px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">Kurayami</div>
        <nav>
            <a href="/">{{ __('Home') }}</a>
            <a href="/login">{{ __('Login') }}</a>
            <a href="/register">{{ __('Register') }}</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} Kurayami Team. All rights reserved.
    </footer>

</body>
</html>