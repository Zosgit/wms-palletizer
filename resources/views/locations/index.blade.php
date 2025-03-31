

@extends('layouts.app')
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Lokalizacja</b>
                <div class="float-end">
                    <a href="{{ route('locations.createmulti') }}"
                        class="btn btn-primary px-4 btn-sm ">Dodaj wiele</a>
                    <a href="{{ route('locations.create') }}"
                        class="btn btn-primary px-4 btn-sm">Dodaj jedną</a>
                </div>
        </div>

        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="ean">EAN</option>
                            <option value="code_store_area">Strefa</option>
                            <option value="code_status">Status</option>
                            <option value="pos_x">Regał</option>
                            <option value="pos_y">Rząd</option>
                            <option value="pos_z">Piętro</option>
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
            <table class="table table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th scope="col">Etykieta</th>
                    <th scope="col">Strefa magazynu</th>
                    <th scope="col">Status</th>
                    <th scope="col">Regał</th>
                    <th scope="col">Rząd</th>
                    <th scope="col">Piętro</th>
                    <th scope="col">Data </th>
                    <th scope="col"> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($locations as $location)
                    <tr>
                        <td>{{ $location->ean }}</td>
                        <td>{{ $location->code_store_area  ?? '' }}</td>
                        <td>{{ $location->code_status  ?? '' }}</td>
                        <td>{{ $location->pos_x }}</td>
                        <td>{{ $location->pos_y }}</td>
                        <td>{{ $location->pos_z }}</td>
                        <td>{{ $location->updated_at }}</td>
                        <td>
                            <a href="{{ route('locations.edit', ['id' => $location->id]) }}">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                            </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mx-auto pb-10 w-4/5">
                {{$locations->links() }}
            </div>
            <p>
                Ilość: {{$locations->count()}} z {{ $locations->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
