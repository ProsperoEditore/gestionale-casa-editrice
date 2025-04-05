<div class="w-64 h-screen bg-gray-100 border-r px-4 pt-6 fixed">
    <h2 class="text-lg font-semibold mb-6">Menu Gestionale</h2>
    <ul class="space-y-3">
        <li><a href="{{ route('anagrafiche.index') }}" class="text-gray-700 hover:text-blue-600">Anagrafiche</a></li>
        <li><a href="{{ route('contratti.index') }}" class="text-gray-700 hover:text-blue-600">Contratti</a></li>
        <li><a href="{{ route('marchi.index') }}" class="text-gray-700 hover:text-blue-600">Marchi Editoriali</a></li>
        <li><a href="{{ route('libri.index') }}" class="text-gray-700 hover:text-blue-600">Libri</a></li>
        <li><a href="{{ route('magazzino.index') }}" class="text-gray-700 hover:text-blue-600">Magazzino</a></li>
        <li><a href="{{ route('ordini.index') }}" class="text-gray-700 hover:text-blue-600">Ordini</a></li>
        <li><a href="{{ route('scarichi.index') }}" class="text-gray-700 hover:text-blue-600">Scarichi</a></li>
        <li><a href="{{ route('registro-vendite.index') }}" class="text-gray-700 hover:text-blue-600">Registro vendite</a></li>
        <li><a href="{{ route('report.index') }}" class="text-gray-700 hover:text-blue-600">Report</a></li>
    </ul>
</div>
