@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- 🔔 AVVISO INFORMATIVO --}}
    <div class="alert alert-warning">
        <strong>⚠️ Attenzione:</strong> Questa sezione va compilata nel caso in cui si desideri esportare i file XML degli ordini per l'integrazione con un sistema di fatturazione elettronica.
    </div>

    <h2 class="mb-4">Profilo fiscale e legale</h2>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profilo.store') }}">
        @csrf

        {{-- Titolo con asterisco rosso --}}
        <h5 class="fw-bold text-decoration-underline mb-2 mt-4">Fatturazione <span class="text-danger">*</span></h5>
        @include('profilo.partials.fatturazione')

        {{-- Titolo con asterisco rosso --}}
        <h5 class="fw-bold text-decoration-underline mb-2 mt-4">Sede Amministrativa <span class="text-danger">*</span></h5>
        @include('profilo.partials.sedi')

        <h5 class="fw-bold text-decoration-underline mb-2 mt-4">Contatti</h5>
        @include('profilo.partials.contatti')

        <h5 class="fw-bold text-decoration-underline mb-2 mt-4">Dati iscrizione REA</h5>
        @include('profilo.partials.rea')

        <h5 class="fw-bold text-decoration-underline mb-2 mt-4">Rappresentante legale</h5>
        @include('profilo.partials.rappresentante')

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success">Salva Profilo</button>
        </div>
    </form>
</div>

{{-- JS gestione sede operativa --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sedeUnicaCheckbox = document.querySelector('[name="sede_unica"]');
    const campiOperativa = document.querySelectorAll('[data-operativa]');

    function copiaCampiAmministrativiSuOperativi() {
        const mappa = {
            'indirizzo_amministrativa': 'indirizzo_operativa',
            'numero_civico_amministrativa': 'numero_civico_operativa',
            'cap_amministrativa': 'cap_operativa',
            'comune_amministrativa': 'comune_operativa',
            'provincia_amministrativa': 'provincia_operativa',
            'nazione_amministrativa': 'nazione_operativa'
        };

        Object.entries(mappa).forEach(([campoA, campoO]) => {
            const val = document.querySelector(`[name="${campoA}"]`)?.value ?? '';
            const inputOperativa = document.querySelector(`[name="${campoO}"]`);
            if (inputOperativa) inputOperativa.value = sedeUnicaCheckbox.checked ? val : '';
        });
    }

    function aggiornaStatoCampiOperativi() {
        const disabilita = sedeUnicaCheckbox.checked;
        campiOperativa.forEach(el => el.disabled = disabilita);
        copiaCampiAmministrativiSuOperativi();
    }

    sedeUnicaCheckbox.addEventListener('change', aggiornaStatoCampiOperativi);

    // Aggiorna i campi anche quando cambia un campo amministrativo, se il flag è attivo
    const campiAmministrativi = [
        'indirizzo_amministrativa',
        'numero_civico_amministrativa',
        'cap_amministrativa',
        'comune_amministrativa',
        'provincia_amministrativa',
        'nazione_amministrativa'
    ];

    campiAmministrativi.forEach(campo => {
        const input = document.querySelector(`[name="${campo}"]`);
        if (input) {
            input.addEventListener('input', () => {
                if (sedeUnicaCheckbox.checked) copiaCampiAmministrativiSuOperativi();
            });
        }
    });

    aggiornaStatoCampiOperativi(); // inizializzazione
});
</script>



@endsection