<div class="w-64 h-screen bg-gray-100 border-r px-4 pt-6 fixed">
    <h2 class="text-lg font-semibold mb-6">Menu Gestionale</h2>
    <ul class="space-y-3">

@auth
    @if (auth()->user()->ruolo === 'admin')
        <li><a href="{{ route('utenti.index') }}" class="text-gray-700 hover:text-blue-600">Gestione Utenti</a></li>
    @endif

    @if (auth()->user()->access_anagrafiche)
        <li><a href="{{ route('anagrafiche.index') }}" class="text-gray-700 hover:text-blue-600">Anagrafiche</a></li>
    @endif

    @if (auth()->user()->access_contratti)
        <li><a href="{{ route('contratti.index') }}" class="text-gray-700 hover:text-blue-600">Contratti</a></li>
    @endif

    @if (auth()->user()->access_marchi)
        <li><a href="{{ route('marchi-editoriali.index') }}" class="text-gray-700 hover:text-blue-600">Marchi Editoriali</a></li>
    @endif

    @if (auth()->user()->access_libri)
        <li><a href="{{ route('libri.index') }}" class="text-gray-700 hover:text-blue-600">Libri</a></li>
    @endif

    @if (auth()->user()->access_magazzini)
        <li><a href="{{ route('magazzini.index') }}" class="text-gray-700 hover:text-blue-600">Magazzini e Conti deposito</a></li>
    @endif

    @if (auth()->user()->access_ordini)
        <li><a href="{{ route('ordini.index') }}" class="text-gray-700 hover:text-blue-600">Ordini</a></li>
    @endif

    @if (auth()->user()->access_scarichi)
        <li><a href="{{ route('scarichi.index') }}" class="text-gray-700 hover:text-blue-600">Spedizioni</a></li>
    @endif

    @if (auth()->user()->access_registro_tirature)
        <li><a href="{{ route('registro-tirature.index') }}" class="text-gray-700 hover:text-blue-600">Registro Tirature</a></li>
    @endif

    @if (auth()->user()->access_registro_vendite)
        <li><a href="{{ route('registro-vendite.index') }}" class="text-gray-700 hover:text-blue-600">Registro vendite</a></li>
    @endif

    @if (auth()->user()->access_report)
        <li><a href="{{ route('report.index') }}" class="text-gray-700 hover:text-blue-600">Report</a></li>
    @endif
@endauth

</ul>

</div>
