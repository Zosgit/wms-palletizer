@extends('layouts.app')
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Przesunięcie produktu na magazynie logicznym</b>
        </div>

        <div class="card-body">
            <form action="" method="GET">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź opakowanie" required/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Szukaj opakowania</button>
                    </div>
                </div>
            </form>
            @if (isset($storeunit->id))
                <div class="row mb-4">
                    <div>Opakowanie: <strong>{{ $storeunit->ean }}</strong></div>
                    <div>Rodzaj: <strong>{{ $storeunit->code_unit_type }}</strong></div>
                    <div>Lokalizacja: <strong>{{ $storeunit->ean_loc }}</strong></div>

                    @if ($storeunit->code_status == 'Dostępna')
                        <div class="mb-4">Status: <strong class="text-success">{{ $storeunit->code_status }}</strong></div>
                    @else
                        <div>Status: <strong class="text-warning">{{ $storeunit->code_status }}</strong></div>
                    @endif



                    @if(isset($stocks))
                        <div class="container">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    Zawartość opakowania
                                </div>
                                <div class="card-body">
                                <div class="table-responsive-sm">
                                    <table class="table table-striped table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Indeks</th>
                                            <th>Nr seryjny</th>
                                            <th>Termin</th>
                                            <th>Magazyn</th>
                                            <th class="center">Ilość</th>
                                            <th>Uwagi</th>
                                            <th>Właściciel</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stocks as $stock)
                                            <tr>
                                                <td>{{ $stock->prod_code}}</td>
                                                <td>{{ $stock->serial_nr }}</td>
                                                <td>{{ $stock->expiration_at }}</td>
                                                <td>{{ $stock->logical_area->code }}</td>
                                                <td>{{ $stock->quantity }}</td>
                                                <td>{{ $stock->remarks }}</td>
                                                <td>{{ $stock->owner->code }}</td>
                                                <td>
                                                    <a href="{{ route('move.area2', ['stock' => $stock]) }}">
                                                    <svg class="icon icon-lg">
                                                        <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                                                    </svg>
                                                </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @else
                @if($start <> '')
                <div class="row mb-4">
                    <div class="text-danger"><strong>Nie znalazłem opakowania !</strong></div>
                </div>
                @endif
            @endif
        </div>



    </div>

@endsection
