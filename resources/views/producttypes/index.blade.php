@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Rodzaje produktów</b>
                <a href="{{ route('producttypes.create') }}"
                    class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
        </div>

        <div class="card-body">
            <table class="table table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th scope="col">Typ</th>
                    <th scope="col">Data dodania</th>
                    <th scope="col"> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($producttypes as $producttype)
                    <tr>
                        <td>{{ $producttype->code }}</td>
                        <td>{{ $producttype->created_at }}</td>
                        <td>
                            <a href="#"  data-coreui-toggle="modal" data-coreui-target="#delete{{ $producttype->id }}">
                            <svg class="icon icon-xl">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-trash') }}"></use>
                            </svg>
                        </a>
                    </td>
                    </tr>
                    @include("producttypes.delete")
                @endforeach
                </tbody>
            </table>
            <div>
                {{$producttypes->links() }}
            </div>
            <p>
                Ilość: {{$producttypes->count()}} z {{ $producttypes->total() }} rekordów.
            </p>


        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>
@endsection
