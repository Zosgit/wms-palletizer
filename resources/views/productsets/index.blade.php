@extends('layouts.app')

@section('title') {{ 'Lista kompletów' }} @endsection

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <b>Lista kompletów</b>
        <a href="{{ route('productsets.create') }}" class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
    </div>

    <div class="card-body">

        {{-- Tabela kompletów --}}
        <table class="table table-striped table-hover table-sm">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Produkty</th>
                    <th>Data utworzenia</th>
                    <th class="text-end">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sets as $set)
                <tr>
                    <td>{{ $set->code }}</td>
                    <td>
                        <ul class="mb-0 ps-3">
                            @foreach($set->products as $product)
                                <li>{{ $product->code }} – {{ $product->longdesc }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $set->created_at->format('Y-m-d H:i') }}</td>
                    <td class="text-end">
                        {{-- Edycja (jeśli chcesz dodać) --}}
                        {{-- <a href="{{ route('productsets.edit', $set->id) }}" class="me-2" title="Edytuj">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                            </svg>
                        </a> --}}

                        {{-- Szczegóły (jeśli jest widok show) --}}
                        {{-- <a href="{{ route('productsets.show', $set->id) }}" title="Szczegóły">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-info') }}"></use>
                            </svg>
                        </a> --}}

                        {{-- Usuwanie
                        <form action="{{ route('productsets.destroy', $set->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Czy na pewno chcesz usunąć ten komplet?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń">
                                <svg class="icon">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-trash') }}"></use>
                                </svg>
                            </button>
                        </form> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginacja --}}
        <div class="mt-3">
            {{ $sets->links() }}
        </div>

        <p>Ilość: {{ $sets->count() }} z {{ $sets->total() }} rekordów.</p>

    </div>
</div>

@endsection
