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
</head>
<body>

    {{-- Sidebar Desktop --}}
    <div class="d-none d-lg-block position-fixed h-100 sidebar">
        <div class="text-center">
            <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo-prospero.png') }}" alt="Logo Prospero Editore" class="logo img-fluid">
            </a>
        </div>

        <h2 class="fs-5 fw-bold text-center">Menu Gestionale</h2>
        <ul class="list-unstyled mt-3">

            @auth         
                @if (auth()->user()->access_anagrafiche)
                    <li><a href="{{ route('anagrafiche.index') }}">Anagrafiche</a></li>
                @endif
                @if (auth()->user()->access_contratti)
                    <li><a href="{{ route('contratti.index') }}">Contratti</a></li>
                @endif
                @if (auth()->user()->access_marchi)
                    <li><a href="{{ route('marchi-editoriali.index') }}">Marchi editoriali</a></li>
                @endif
                @if (auth()->user()->access_libri)
                    <li><a href="{{ route('libri.index') }}">Libri</a></li>
                @endif

                {{--
                    @if (auth()->user()->access_schede_libro)
                        <li><a href="{{ route('schede-libro.index') }}">Schede libro</a></li>
                    @endif
                    {{-- [VOCE NASCOSTA TEMPORANEAMENTE] Per riattivare il link alle Schede Libro, rimuovi questo commento e assicurati che l'utente abbia access_schede_libro abilitato. --}}
                --}}

                @if (auth()->user()->access_magazzini)
                    <li><a href="{{ route('magazzini.index') }}">Magazzini e Conti deposito</a></li>
                @endif
                @if (auth()->user()->access_ordini)
                    <li><a href="{{ route('ordini.index') }}">Ordini</a></li>
                @endif
                @if (auth()->user()->access_scarichi)
                    <li><a href="{{ route('scarichi.index') }}">Spedizioni</a></li>
                @endif
                @if (auth()->user()->access_registro_tirature)
                    <li><a href="{{ route('registro-tirature.index') }}">Registro tirature</a></li>
                @endif
                @if (auth()->user()->access_registro_vendite)
                    <li><a href="{{ route('registro-vendite.index') }}">Registro vendite</a></li>
                @endif
                @if (auth()->user()->access_report)
                    <li><a href="{{ route('report.index') }}">Report</a></li>
                @endif
                @if (auth()->user()->ruolo === 'admin')
                    <li><a href="{{ route('utenti.index') }}">Gestione utenti</a></li>
                @endif
                @if (auth()->user()->ruolo === 'admin')
                    <li><a href="{{ route('backup.index') }}">Backup database</a></li>
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
            <h5 class="offcanvas-title">Menu Gestionale</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
        <div class="text-center">
            <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo-prospero.png') }}" alt="Logo Prospero Editore" class="logo img-fluid">
            </a>
        </div>
            <ul class="list-unstyled">
            @auth         
                @if (auth()->user()->access_anagrafiche)
                    <li><a href="{{ route('anagrafiche.index') }}">Anagrafiche</a></li>
                @endif
                @if (auth()->user()->access_contratti)
                    <li><a href="{{ route('contratti.index') }}">Contratti</a></li>
                @endif
                @if (auth()->user()->access_marchi)
                    <li><a href="{{ route('marchi-editoriali.index') }}">Marchi editoriali</a></li>
                @endif
                @if (auth()->user()->access_libri)
                    <li><a href="{{ route('libri.index') }}">Libri</a></li>
                @endif

                {{--
                    @if (auth()->user()->access_schede_libro)
                        <li><a href="{{ route('schede-libro.index') }}">Schede libro</a></li>
                    @endif
                    {{-- [VOCE NASCOSTA TEMPORANEAMENTE] Per riattivare il link alle Schede Libro, rimuovi questo commento e assicurati che l'utente abbia access_schede_libro abilitato. --}}
                --}}

                @if (auth()->user()->access_magazzini)
                    <li><a href="{{ route('magazzini.index') }}">Magazzini e Conti deposito</a></li>
                @endif
                @if (auth()->user()->access_ordini)
                    <li><a href="{{ route('ordini.index') }}">Ordini</a></li>
                @endif
                @if (auth()->user()->access_scarichi)
                    <li><a href="{{ route('scarichi.index') }}">Spedizioni</a></li>
                @endif
                @if (auth()->user()->access_registro_tirature)
                    <li><a href="{{ route('registro-tirature.index') }}">Registro tirature</a></li>
                @endif
                @if (auth()->user()->access_registro_vendite)
                    <li><a href="{{ route('registro-vendite.index') }}">Registro vendite</a></li>
                @endif
                @if (auth()->user()->access_report)
                    <li><a href="{{ route('report.index') }}">Report</a></li>
                @endif
                @if (auth()->user()->ruolo === 'admin')
                    <li><a href="{{ route('utenti.index') }}">Gestione Utenti</a></li>
                @endif
                @if (auth()->user()->ruolo === 'admin')
                    <li><a href="{{ route('backup.index') }}">Backup database</a></li>
                @endif


            @endauth
            </ul>
        </div>
    </div>

    {{-- Contenuto principale --}}
    <div class="main-content">
        <header class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 fw-bold mb-0">GHE PENSI MI <span class="fw-normal">- gestionale della casa editrice</span></h1>

            @auth
                <div>
                    <span class="me-3">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                    </form>
                </div>
            @endauth
        </header>

        {{-- Slot contenuto dinamico --}}
        @yield('content')
    </div>
    @stack('scripts')

</body>




</html>
