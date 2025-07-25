<!DOCTYPE html>
<html lang="it">
<head>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta charset="UTF-8">
    <title>GHE PENSI MI - Gestionale Prospero Editore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])



    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Open Sans', sans-serif !important;
            background-color: #fefaf3;
        }
        .sidebar {
            width: 240px;
            background-color: #f8f8f8;
            padding: 1.5rem 1rem;
            border-right: 1px solid #e0e0e0;
            max-height: 100vh;
            overflow-y: auto;
        }
        .sidebar a {
            color: #333;
            text-decoration: none;
            display: block;
            padding: 0.5rem 0;
        }
        .sidebar a:hover {
            color: #007bff;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f8f8f8;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 10px;
        }
        .sidebar a.btn {
            padding-left: 1rem !important;
            text-align: left;
        }
        .logo {
            max-height: 200px;
            margin-bottom: 1rem;
        }
        .main-content {
            margin-left: 240px;
            padding: 2rem;
        }
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>


<style>
    .btn-prospero {
        padding-left: 1rem;
        background-color: #000000;
        color: #ffffff !important;
        border: none;
        font-weight: 600;
    }

    .btn-prospero:hover {
        background-color: #333333;
        color: #ffffff;
    }

    .btn-prospero-secondary {
        padding-left: 1rem;
        background-color: #f2f2f2;
        color: #000000;
        border: 1px solid #cccccc;
        font-weight: 600;
    }
    .btn-prospero-secondary:hover {
        background-color: #e6e6e6;
    }
</style>

<style>
    .offcanvas-body {
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }

    .offcanvas .d-grid {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>


</head>
<body>

    {{-- Sidebar Desktop --}}
    <div class="d-none d-lg-block position-fixed sidebar">
        <div class="text-center">
            <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo-prospero.png') }}" alt="Logo Prospero Editore" class="logo img-fluid">
            </a>
        </div>

        <ul class="list-unstyled mt-3">

                    @auth         
            @if (auth()->user()->access_marchi)
                <li class="mb-2">
                    <a href="{{ route('marchi-editoriali.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Marchi editoriali</a>
                </li>
            @endif
            @if (auth()->user()->access_anagrafiche)
                <li class="mb-2">
                    <a href="{{ route('anagrafiche.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Anagrafiche</a>
                </li>
            @endif
            @if (auth()->user()->access_libri)
                <li class="mb-2">
                    <a href="{{ route('libri.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Libri</a>
                </li>
            @endif

            {{--
                @if (auth()->user()->access_schede_libro)
                    <li class="mb-2">
                        <a href="{{ route('schede-libro.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Schede libro</a>
                    </li>
                @endif
            --}}
            {{-- [VOCE NASCOSTA TEMPORANEAMENTE] Per riattivare il link alle Schede Libro, rimuovi il commento sopra e assicurati che l'utente abbia access_schede_libro abilitato. --}}

            @if (auth()->user()->access_magazzini)
                <li class="mb-2">
                    <a href="{{ route('magazzini.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Magazzini e Depositi</a>
                </li>
            @endif
            @if (auth()->user()->access_ordini)
                <li class="mb-2">
                    <a href="{{ route('ordini.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Ordini</a>
                </li>
            @endif
            @if (auth()->user()->access_scarichi)
                <li class="mb-2">
                    <a href="{{ route('scarichi.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Spedizioni</a>
                </li>
            @endif
            @if (auth()->user()->access_registro_vendite)
                <li class="mb-2">
                    <a href="{{ route('registro-vendite.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Registro vendite</a>
                </li>
            @endif
            @if (auth()->user()->access_contratti)
                <li class="mb-2">
                    <a href="{{ route('contratti.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Contratti</a>
                </li>
            @endif
            @if (auth()->user()->access_report)
                <li class="mb-2">
                    <a href="{{ route('report.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Report</a>
                </li>
            @endif
            @if (auth()->user()->access_ritenute)
                <li class="mb-2">
                    <a href="{{ route('ritenute.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Ritenute DA</a>
                </li>
            @endif
            @if (auth()->user()->access_registro_tirature)
                <li class="mb-2">
                    <a href="{{ route('registro-tirature.index') }}" class="btn btn-prospero-secondary w-100 text-start"> Registro tirature</a>
                </li>
            @endif
        @endauth

        </ul>
    </div>

    {{-- Navbar Mobile --}}
    <nav class="navbar navbar-light bg-light d-lg-none px-3">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
            ☰
        </button>
        <span class="fw-bold">GHE PENSI MI</span>
    </nav>

{{-- Offcanvas Menu Mobile --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-prospero.png') }}" alt="Logo Prospero Editore" class="img-fluid" style="max-width: 120px;">
            </a>
        </div>
        <div class="d-grid gap-2">
            @auth
                @if (auth()->user()->access_marchi)
                    <a href="{{ route('marchi-editoriali.index') }}" class="btn btn-prospero-secondary">Marchi editoriali</a>
                @endif         
                @if (auth()->user()->access_anagrafiche)
                    <a href="{{ route('anagrafiche.index') }}" class="btn btn-prospero-secondary">Anagrafiche</a>
                @endif
                @if (auth()->user()->access_libri)
                    <a href="{{ route('libri.index') }}" class="btn btn-prospero-secondary">Libri</a>
                @endif

                {{-- 
                    @if (auth()->user()->access_schede_libro)
                        <a href="{{ route('schede-libro.index') }}" class="btn btn-prospero-secondary">Schede libro</a>
                    @endif
                --}}
                {{-- [VOCE NASCOSTA TEMPORANEAMENTE] Per riattivare il link alle Schede Libro, rimuovi il commento sopra e assicurati che l'utente abbia access_schede_libro abilitato. --}}

                @if (auth()->user()->access_magazzini)
                    <a href="{{ route('magazzini.index') }}" class="btn btn-prospero-secondary">Magazzini e Depositi</a>
                @endif
                @if (auth()->user()->access_ordini)
                    <a href="{{ route('ordini.index') }}" class="btn btn-prospero-secondary">Ordini</a>
                @endif
                @if (auth()->user()->access_scarichi)
                    <a href="{{ route('scarichi.index') }}" class="btn btn-prospero-secondary">Spedizioni</a>
                @endif
                @if (auth()->user()->access_registro_vendite)
                    <a href="{{ route('registro-vendite.index') }}" class="btn btn-prospero-secondary">Registro vendite</a>
                @endif
                @if (auth()->user()->access_contratti)
                    <a href="{{ route('contratti.index') }}" class="btn btn-prospero-secondary">Contratti</a>
                @endif
                @if (auth()->user()->access_report)
                    <a href="{{ route('report.index') }}" class="btn btn-prospero-secondary">Report</a>
                @endif
                @if (auth()->user()->access_ritenute)
                    <a href="{{ route('ritenute.index') }}" class="btn btn-prospero-secondary">Ritenute DA</a>
                @endif
                @if (auth()->user()->access_registro_tirature)
                    <a href="{{ route('registro-tirature.index') }}" class="btn btn-prospero-secondary">Registro tirature</a>
                @endif
            @endauth
        </div>
    </div>
</div>



    {{-- Contenuto principale --}}
    <div class="main-content">
        <header class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 fw-bold mb-0">GHE PENSI MI <span class="fw-normal">- gestionale della casa editrice</span></h1>

            @auth
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-5 me-2"></i> {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('profilo.index') }}">Profilo</a></li>
                        @if(auth()->user()->ruolo === 'admin')
                            <li><a class="dropdown-item" href="{{ route('utenti.index') }}">Gestione utenti</a></li>
                            <li><a class="dropdown-item" href="{{ route('backup.index') }}">Backup database</a></li>
                        @endif
                        <li><a class="dropdown-item" href="#">Guida</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </header>

        {{-- Slot contenuto dinamico --}}
        @yield('content')
    </div>
    @stack('scripts')

</body>

</html>