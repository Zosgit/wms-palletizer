@extends('layouts.app')
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Jednostki miary produktów</b>
                <a href="{{ route('productmetrics.create') }}"
                    class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Opis</th>
                    <th scope="col">Ilość</th>
                    <th scope="col"> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($productmetrics as $productmetric)
                    <tr>
                        <td>{{ $productmetric->code }}</td>
                        <td>{{ $productmetric->longdesc }}</td>
                        <td>{{ $productmetric->amount }}</td>
                        <td><a href="{{ route('productmetrics.edit', ['productmetric' => $productmetric]) }}">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                            </svg>
                        </a>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                {{$productmetrics->links() }}
            </div>
            <p>
                Ilość: {{$productmetrics->count()}} z {{ $productmetrics->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>
@endsection
