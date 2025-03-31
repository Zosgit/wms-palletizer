@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Rodzaj opakowania</b>
                <a href="{{ route('storeunittypes.create') }}"
                    class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
        </div>

        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Waga maksymalna (g)</th>
                    <th scope="col">Waga opakowania (g)</th>
                    <th scope="col">Skrót</th>
                    <th scope="col"> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($storeunittypes as $storeunittype)
                    <tr>
                        <td>{{ $storeunittype->code }}</td>
                        <td>{{ $storeunittype->loadwgt }}</td>
                        <td>{{ $storeunittype->suwgt }}</td>
                        <td>{{ $storeunittype->prefix }}</td>
                        <td><a href="{{ route('storeunittypes.edit', ['storeunittype' => $storeunittype]) }}">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                            </svg>
                        </a>
                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                {{$storeunittypes->links() }}
            </div>
            <p>
                Ilość: {{$storeunittypes->count()}} z {{ $storeunittypes->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
