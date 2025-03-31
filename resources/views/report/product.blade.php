@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
           <b>{{ $product->code}}</b>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
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
            <table id="data_table" class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>Opakowanie</th>
                        <th>Miejsce</th>
                        <th>Magazyn</th>
                        <th>Właściciel</th>
                        <th>Status</th>
                        <th>Nr seryjny</th>
                        <th>Ważny do</th>
                        <th>Fifo</th>
                        <th>Ilość</th>
                        <th>Uwagi</th>


                    </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->ean_su }}</td>
                                <td>{{ $product->ean_loc }}</td>
                                <td>{{ $product->desc_la }}</td>
                                <td>{{ $product->code }}</td>
                                <td>{{ $product->code_status }}</td>
                                <td>{{ $product->serial_nr}}</td>
                                <td>{{ $product->expiration_at }}</td>
                                <td>{{ $product->fifo }}</td>
                                <td><strong>{{ $product->quantity}}</strong></td>
                                <td>{{ $product->remarks}}</td>

                            </tr>
                        @endforeach
                </tbody>
            </table>

            <div>
                {{$products->links() }}
            </div>
            <p>
                Ilość: {{$products->count()}} z {{ $products->total() }} rekordów.
            </p>

        </div>

    </div>



@endsection
