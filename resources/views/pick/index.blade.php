@extends('layouts.app')
@section('title') {{ 'pick.index' }} @endsection
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Kompletacja zamówień
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="ship_nr">Nr dostawy</option>
                            <option value="external_nr">Dok zewnętrzny</option>
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
                        <th>Dokument wydania</th>
                        <th>Numer zewnątrzny</th>
                        <th>Miejsce</th>
                        <th>Data</th>
                        <th> </th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->order_nr }}</td>
                                <td>{{ $order->external_nr}}</td>
                                <td>{{ $order->location}}</td>
                                <td>{{ $order->updated_at}}</td>
                                <td><a href="{{ route('pick.storeunit',['id'=>$order->id])}}">
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
                {{$orders->links() }}
            </div>
            <p>
                Ilość: {{$orders->count()}} z {{ $orders->total() }} rekordów.
            </p>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
