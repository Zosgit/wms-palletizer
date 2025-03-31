@extends('layouts.app')

@section('title') {{ 'Raport zapasu' }} @endsection

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Zapas magazynowy
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">Rodzaj raportu</option>
                            <option value="all">Cały zapas</option>
                            <option value="la">Podział na logiczny</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Wyświetl</button>
                    </div>
                </div>
            </form>
            @if ($start==1)
            <table id="data_table" class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Opis</th>
                        <th>Właściciel</th>
                        <th>Suma</th>
                        @if($report=='la')
                            <th>Status</th>
                            <th>Strefa</th>
                        @endif
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->prod_code }}</td>
                                <td>{{ $stock->longdesc}}</td>
                                <td>{{ $stock->code_firm}}</td>
                                <td>{{ $stock->sum_stock}}</td>
                                @if($report=='la')
                                    <td>{{ $stock->code }}</td>
                                    <td>{{ $stock->code_la }}</td>
                                @endif
                                <td>
                                    <a href="{{ route('report.product', ['id' => $stock->product_id]) }}">
                                        <svg class="icon icon-lg">
                                            <use xlink:href="{{ asset('icons/coreui.svg#cil-info') }}"></use>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>

            <div>
                {{$stocks->links() }}
            </div>
            <p>
                Ilość: {{$stocks->count()}} z {{ $stocks->total() }} rekordów.
            </p>
            @endif

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
