@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Lista opakowań do wydruku
                <a href="{{ route('storeunit.generate-multi-pdf',['print' => $print]) }}"
                class="btn btn-sm btn-primary float-end px-4 mr-4">Generuj etykiety</a>
                <a href="{{ route('storeunits.index') }}"
                    class="btn btn-sm btn-dark float-end px-4 ">Powrót</a>
        </div>

        <div class="card-body">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th scope="col">EAN opakowania</th>
                    <th scope="col">Rodzaj opakowania</th>
                    <th scope="col">Status</th>
                    <th scope="col">Utworzona</th>
                </tr>
                </thead>
                <tbody>
                @foreach($storeunits as $storeunit)
                    <tr>
                        <td>{{ $storeunit->ean }}</td>
                        <td>{{ $storeunit->storeunittype->code ?? ''}}</td>
                        <td>{{ $storeunit->sustatus->code ?? ''}}</td>
                        <td>{{ $storeunit->created_at}}</td>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>

    </div>

@endsection
@push('scripts')
<script>
    var table = new DataTable('#data_table', {
    language: {
        url: '{{asset("plugins/DataTables/pl.json")}}',
    },
});
</script>
@endpush
