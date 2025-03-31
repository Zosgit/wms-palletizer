@extends('layouts.app')

@section('title') {{ 'Raport zapasu' }} @endsection

@section('content')
<a class="big" href="{{ route('orderdetail.index',['id'=>$order->id])}}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista zamówień</a></br></br>

    <div class="card mb-4">
        <div class="card-header">
            Zapas magazynowy do zamówienia: {{ $order->order_nr }}
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź produkt" />
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Filtruj</button>
                    </div>
                </div>
            </form>
            <table id="data_table" class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Opis</th>
                        <th>Magazyn</th>
                        <th>Suma</th>
                        <th >Dopisuje...</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->prod_code }}</td>
                                <td>{{ $stock->longdesc}}</td>
                                <td>{{ $stock->code_la}}</td>
                                <td>{{ $stock->sum_stock}}</td>
                                <td>
                                    <form action="{{ route('orderdetail.save') }}" method="post">
                                        @csrf
                                        <div class="row ">
                                            <div class="col-md-6 input-group-sm">
                                                <input type="number" Step=".01" id="quantity" name="quantity" min="1"max="{{ $stock->sum_stock }}" value="" class="form-control" placeholder="Ilość" required/>
                                                <input type="hidden" id='order_id' name='order_id' value="{{ $order->id }}"/>
                                                <input type="hidden" id='logical_area_id' name='logical_area_id' value="{{ $stock->logical_area_id }}"/>
                                                <input type="hidden" id='product_id' name='product_id' value="{{ $stock->product_id }}"/>
                                                <input type="hidden" id='prod_code' name='prod_code' value="{{ $stock->prod_code}}"/>
                                                <input type="hidden" id='prod_desc' name='prod_desc' value="{{ $stock->longdesc }}"/>
                                            </div>
                                            <div class="col-md-5">
                                                <button type="submit" class="btn btn-success btn-sm">Dopisz</button>
                                            </div>
                                        </div>
                                    </form>
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

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>

@endsection
