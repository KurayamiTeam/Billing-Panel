<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::where('key', 'app_name')->value('value') ?? 'Kurayami' }}</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-primary); min-height: 100vh; display: flex; flex-direction: column; }
        
        header { 
            background-color: var(--bg-surface); 
            border-bottom: 1px solid var(--border-color); 
            padding: 15px 25px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            position: relative;
            z-index: 100;
        }
        .logo { font-size: 20px; font-weight: 800; color: var(--color-accent); text-transform: uppercase; letter-spacing: 1px; }
        
        /* Botón Hamburguesa */
        .menu-toggle { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; gap: 6px; }
        .menu-toggle span { width: 28px; height: 3px; background-color: var(--color-accent); border-radius: 2px; transition: 0.3s; }
        
        /* Contenedor del Menú Desplegable Lateral/Superior */
        .nav-menu { 
            position: absolute; top: 100%; right: 0; width: 280px; 
            background-color: var(--bg-surface); border-left: 1px solid var(--border-color); 
            border-bottom: 1px solid var(--border-color); display: none; flex-direction: column; padding: 15px; gap: 10px; 
        }
        .nav-menu.active { display: flex; }
        
        /* Estilos de los Botones Tipo Nebula */
        .btn { 
            display: block; width: 100%; padding: 12px; text-align: center; 
            text-decoration: none; font-weight: 600; border-radius: 8px; font-size: 14px; transition: 0.2s; 
        }
        .btn-accent { background-color: var(--color-accent); color: #fff; }
        .btn-accent:hover { background-color: var(--color-accent-hover); }
        .btn-secondary { background-color: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); }
        .btn-secondary:hover { background-color: var(--border-color); }
        .btn-link { color: var(--text-secondary); text-align: left; padding: 10px 15px; }
        .btn-link:hover { color: var(--text-primary); background-color: var(--bg-card); border-radius: 6px; }

        /* Submenú de Perfil Desplegable Interno */
        .submenu { display: none; flex-direction: column; background-color: var(--bg-main); border-radius: 6px; margin-top: 5px; padding-left: 15px; }
        .submenu.active { display: flex; }

        main { flex: 1; padding: 25px; display: flex; flex-direction: column; }
    </style>
</head>
<body>

    <header>
        <div class="logo">{{ \App\Models\Setting::where('key', 'app_name')->value('value') ?? 'Kurayami' }}</div>
        <button class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <div class="nav-menu" id="navMenu">
            @guest
                <a href="/register" class="btn btn-accent">Registrarse</a>
                <a href="/login" class="btn btn-secondary">Iniciar Sesión</a>
                <a href="mailto:{{ \App\Models\Setting::where('key', 'admin_email')->value('value') ?? 'pixelcrewteam@gmail.com' }}" class="btn btn-link">Soporte</a>
            @endguest

            @auth
                <a href="/dashboard" class="btn btn-link">Dash</a>
                <a href="/store" class="btn btn-link">Tienda</a>
                <a href="/tickets" class="btn btn-link">Tickets</a>
                <a href="/settings" class="btn btn-link">Opciones</a>
                <a href="/notifications" class="btn btn-link">Notificaciones</a>
                
                <div>
                    <a href="#" class="btn btn-link" onclick="toggleSubmenu(event)">Perfil 👇</a>
                    <div class="submenu" id="profileSubmenu">
                        <a href="/profile" class="btn btn-link">Ver Perfil</a>
                        @if(Auth::user()->hasPermission('view-admin'))
                            <a href="/admin" class="btn btn-link" style="color: var(--color-accent);">Administración</a>
                        @endif
                        <a href="/logout" class="btn btn-link" style="color: var(--color-danger);">Cerrar Sesión</a>
                    </div>
                </div>
            @endauth
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script>
        function toggleMenu() {
            document.getElementById('navMenu').classList.toggle('active');
        }
        function toggleSubmenu(e) {
            e.preventDefault();
            document.getElementById('profileSubmenu').classList.toggle('active');
        }
    </script>
</body>
</html>