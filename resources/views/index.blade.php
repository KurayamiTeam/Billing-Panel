@extends('layouts.app')

@section('content')
<div class="welcome-container">
    <h1 class="welcome-title">
        {{ \App\Models\Setting::where('key', 'welcome_title')->value('value') ?? '¡Bienvenido a Kurayami!' }}
    </h1>
    <p class="welcome-desc">
        {{ \App\Models\Setting::where('key', 'welcome_description')->value('value') ?? 'Esperamos que disfrutes de este hosting.' }}
    </p>
    <div class="welcome-quote">
        "{{ \App\Models\Setting::where('key', 'welcome_quote')->value('value') ?? 'Puedes mirar los planes ahí abajo.' }}"
    </div>
</div>

<div class="services-section">
    <h2 class="services-title">Servicios</h2>

    @if($packages->isEmpty())
        <div class="no-services">
            No hay servicios disponibles en este momento.
        </div>
    @else
        @foreach($packages as $package)
            <div class="service-card">
                <div class="service-name">{{ $package->name }}</div>
                <div class="service-desc">{{ $package->description ?? 'Servidores de alto rendimiento dedicados.' }}</div>
                <a href="/checkout/{{ $package->id }}" class="btn btn-accent" style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                    Ver todo <span>➔</span>
                </a>
            </div>
        @endforeach
    @endif
</div>
@endsection