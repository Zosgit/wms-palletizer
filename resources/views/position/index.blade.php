@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Pozycjonowanie paczek</b>
            <a href="{{ route('position.create') }}"
                class="btn btn-primary float-end px-4">Dodaj</a>
        </div>

        <div class="card-body">

            {{-- FILTRUJ (opcjonalnie) --}}
            <form action="" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="name">Nazwa</option>
                            <option value="picklist_id">Numer kompletacji</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź dane..."/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Filtruj</button>
                    </div>
                </div>
            </form>

            {{-- TABELA --}}
            <table class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Numer kompletacji</th>
                        <th>Data</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $position)
                        <tr>
                            <td>{{ $position->name }}</td>
                            <td>
                                @if($position->picklist_id)
                                    <span class="badge bg-info">#{{ $position->picklist_id }}</span>
                                @else
                                    <span class="text-muted">brak</span>
                                @endif
                            </td>
                            <td>{{ $position->created_at }}</td>
                            <td>
                                <a href="{{ route('position.show', $position->id) }}">
                                    <svg class="icon icon-lg">
                                        <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-right') }}"></use>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginacja --}}
            <div>
                {{ $positions->links() }}
            </div>

            {{-- Licznik --}}
            <p>
                Ilość: {{ $positions->count() }} z {{ $positions->total() }} rekordów.
            </p>
        </div>
    </div>

@endsection
