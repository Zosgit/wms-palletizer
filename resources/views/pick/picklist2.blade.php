@extends('layouts.app')
@section('title') {{ 'pick.picklist2' }} @endsection
@section('content')
<a class="big" href="{{ route('pick.picklist',['id'=>$orderdetail->order_id]) }}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista zamówień</a></br></br>
<div class="container">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center">
        <div>Lista nr: <strong>{{ $orderdetail->order_id.'  -> '.$orderdetail->prod_desc}}</strong>
        </div>
       </div>
      <div class="card-body">
        <div class="row mb-4">

        <div class="table-responsive-sm">
          <table class="table table-striped table-hover table-sm">
            <thead>
              <tr>
                <th>Miejsce</th>
                <th>Opakowanie</th>
                <th>Fifo</th>
                <th class="center">ilość</th>
                <th class="center">Pobieram</th>
              </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                    <tr>
                        <td>{{ $stock->code_location }}</td>
                        <td class="center">{{ $stock->code_store_unit}}</td>
                        <td class="center">{{ $stock->fifo}}</td>
                        <td class="right">{{ $stock->quantity }}</td>
                        <td>
                            <form action="{{ route('pick.picklistsave') }}" method="post">
                                @csrf
                                <div class="row ">
                                    <div class="col-md-6 input-group-sm">
                                        <input type="number" Step=".01" id="quantity" name="quantity" min="1" max="{{ ($orderdetail->quantity_pick > $stock->quantity) ? $stock->quantity : $orderdetail->quantity_pick }}"
                                        value="{{ ($orderdetail->quantity_pick > $stock->quantity) ? $stock->quantity : $orderdetail->quantity_pick }}"
                                        class="form-control" placeholder="Ilość" required/>
                                        <input type="hidden" id='order_id' name='order_id' value="{{ $orderdetail->order_id }}"/>
                                        <input type="hidden" id='orderdetail_id' name='orderdetail_id' value="{{ $orderdetail->id }}"/>
                                        <input type="hidden" id='stock_id' name='stock_id' value="{{ $stock->id_stock }}"/>
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" class="btn btn-success btn-sm">Pobieram</button>
                                    </div>
                                </div>
                            </form>
                    </td>
                    </tr>
                @endforeach
                </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
  @endsection
