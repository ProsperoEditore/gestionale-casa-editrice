@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Modifica Report</h3>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('report.update', $report->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Data Creazione</label>
                        <input type="date" name="data_creazione" class="form-control" value="{{ old('data_creazione', $report->data_creazione) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Libro</label>
                        <input type="text" id="libro_autocomplete" class="form-control titolo-autocomplete" value="{{ $report->libro->titolo }} [{{ $report->libro->isbn }}]" required>
                        <input type="hidden" name="libro_id" class="libro-id" id="libro_id" value="{{ $report->libro_id }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contratto</label>
                        <select name="contratto_id" class="form-control" required>
                            <option value="">Seleziona un contratto</option>
                            @foreach($contratti as $contratto)
                                <option value="{{ $contratto->id }}" {{ $report->contratto_id == $contratto->id ? 'selected' : '' }}>
                                    {{ $contratto->nome_contratto }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">Salva Modifiche</button>
                        <a href="{{ route('report.index') }}" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
$(function () {
    $("#libro_autocomplete").autocomplete({
        minLength: 2,
        delay: 100,
        source: function (request, response) {
            $.ajax({
                url: "{{ route('report.autocomplete-libro') }}",
                data: { query: request.term },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.titolo + ' [' + item.isbn + ']',
                            value: item.titolo + ' [' + item.isbn + ']',
                            id: item.id
                        };
                    }));
                }
            });
        },
        select: function (event, ui) {
            $("#libro_autocomplete").val(ui.item.label);
            $("#libro_id").val(ui.item.id);
            return false;
        }
    });
});
</script>
@endpush
