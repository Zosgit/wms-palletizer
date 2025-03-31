@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Lista Kontroli dostaw
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="ship_nr">Nr dostawy</option>
                            <option value="external_nr">Dok zewnętrzny</option>
                            <option value="code_firm">Dostawca</option>
                            <option value="code_owner">Właściciel</option>
                            <option value="code_status">Status</option>
                            <option value="location">Miejsce</option>
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
                        <th>Dokument dostawy</th>
                        <th>Numer zewnątrzny</th>
                        <th>Dostawca</th>
                        <th>Właściciel</th>
                        <th>Status</th>
                        <th>Miejsce</th>
                        <th>Data</th>
                        <th> </th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($shipments as $shipment)
                            <tr>
                                <td>{{ $shipment->ship_nr }}</td>
                                <td>{{ $shipment->external_nr}}</td>
                                <td>{{ $shipment->code_firm}}</td>
                                <td>{{ $shipment->code_owner}}</td>
                                <td>{{ $shipment->code_status}}</td>
                                <td>{{ $shipment->location ?? ''}}</td>
                                <td>{{ $shipment->updated_at}}</td>
                                <td><a href="{{ route('control.view',['id'=>$shipment->id])}}">
                                    <svg class="icon icon-lg">
                                        <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-right') }}"></use>
                                    </svg>
                                </a>
                            </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
            <div>
                {{$shipments->links() }}
            </div>
            <p>
                Ilość: {{$shipments->count()}} z {{ $shipments->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
