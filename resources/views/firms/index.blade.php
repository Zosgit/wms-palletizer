@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Kontrahenci</b>
                <a href="{{ route('firms.create') }}"
                    class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th scope="col">Nazwa firmy</th>
                    <th scope="col">NIP</th>
                    <th scope="col">Ulica</th>
                    <th scope="col">Kod pocztowy</th>
                    <th scope="col">Miasto</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($firms as $firm)
                    <tr>
                        <td>{{ $firm->code }}</td>

                        <td>{{ $firm->tax }}</td>
                        <td>{{ $firm->street }}</td>
                        <td>{{ $firm->postcode }}</td>
                        <td>{{ $firm->city }}</td>
                        <td><a href="{{ route('firms.edit', ['firm' => $firm]) }}">
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
                {{$firms->links() }}
            </div>
            <p>
                Ilość: {{$firms->count()}} z {{ $firms->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>
@endsection
