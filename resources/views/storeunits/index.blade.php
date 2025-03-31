@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Lista opakowań
                <a href="{{ route('storeunits.create') }}"
                    class="btn btn-primary float-end px-4 ">Dodaj</a>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="ean">EAN</option>
                            <option value="code_unit_type">Rodzaj</option>
                            <option value="ean_loc">Lokalizacja</option>
                            <option value="code_status">Status</option>
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



            <table id="data_table" class="table table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th scope="col">EAN opakowania</th>
                    <th scope="col">Rodzaj opakowania</th>
                    <th scope="col">Lokalizacja</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data</th>
                    <th class="th-sm"></th>
                    <th class="th-sm"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($storeunits as $storeunit)
                    <tr>
                        <td>{{ $storeunit->ean }}</td>
                        <td>{{ $storeunit->code_unit_type ?? ''}}</td>
                        <td>{{ $storeunit->ean_loc ?? ''}}</td>
                        <td>{{ $storeunit->code_status ?? ''}}</td>
                        <td>{{ $storeunit->updated_at}}</td>
                        <td>

                            <a href="{{ route('storeunit.generate-pdf', ['id' => $storeunit->id]) }}">
                                <svg class="icon icon-lg">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-print') }}"></use>
                                </svg>
                            </a>
                        </td>
                        <td>

                        </td>

                    </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                {{$storeunits->links() }}
            </div>
            <p>
                Ilość: {{$storeunits->count()}} z {{ $storeunits->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
