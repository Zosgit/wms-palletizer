@extends('layouts.app')
@section('content')
<a class="big" href="{{ route('orders.index') }}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista zamówień</a></br></br>
<div class="container">
    <div class="card">
      <div class="card-header d-flex align-items-center"><div>Zamówienie nr: <strong>{{ $order->order_nr}}</strong></div>
        @if ($orderdetails->total() > 0)
            <div class="ms-auto text-end w-100">
                <form method="post" action="{{ route('orderdetail.sendpick', ['id' => $order->id]) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-info px-4">Rozpocznij kompletację ręczną</button>
                </form>

                <form method="post" action="{{ route('orderdetail.autopick', ['id' => $order->id]) }}" class="d-inline ms-3">
                    @csrf
                    <button class="btn btn-sm btn-info px-4">
                        <i class="fas fa-cogs me-1"></i> Rozpocznij kompletację automatyczną
                    </button>
                </form>
            </div>
        @endif
    </div>
      <div class="card-body">
        <div class="row mb-4">
          <!-- /.col-->
          <div class="col-sm-4">
            <h6 class="mb-3">Szczegóły:</h6>
            <div>Dok: wydania: <strong>{{ $order->external_nr}}</strong></div>
            <div>Data wydania: <strong>{{ $order->created_at}}</strong></div>
            <div>Status: <strong>{{ $order->status->code}}</strong></div>
            <div>Uwagi: {{ $order->remarks}}</div>
          </div>
          <div class="col-sm-4">
            <h6 class="mb-3">Odbiorca:</h6>
            <div><strong>{{ $order->firm->code}}</strong></div>
            <div>{{ $order->firm->longdesc}}</div>
            <div>{{ $order->firm->postcode.' - '.$order->firm->city}}</div>
          </div>
          <!-- /.col-->
          <div class="col-sm-4">
            <h6 class="mb-3">Właściciel:</h6>
            <div><strong>{{ $order->owner->code}}</strong></div>
            <div>{{ $order->owner->longdesc}}</div>
            <div>{{ $order->owner->postcode.' - '.$order->owner->city}}</div>
          </div>
          <!-- /.col-->
        </div>
        <!-- /.row-->

        <a href="{{ route('orderdetail.create',['id' => $order->id]) }}"
        class="btn btn-primary float-start px-4 btn-sm">Dodaj pozycję</a>
        <div class="table-responsive-sm">
          <table class="table table-striped table-hover table-sm">
            <thead>
              <tr>
                <th>Indeks</th>
                <th>Magazyn</th>
                <th>Kategoria</th>
                <th class="center">ilość</th>
                <th class="center">Operacje</th>
              </tr>
            </thead>
            <tbody>
                @foreach($orderdetails as $orderdetail)
                    <tr>
                        <td>{{ $orderdetail->prod_code }}</td>
                        <td class="center">{{ $orderdetail->prod_desc }}</td>
                        <td class="center">{{ $orderdetail->logical_area->code }}</td>
                        <td class="center">{{ $orderdetail->product->producttype->code }}</td>
                        <td class="right">{{ $orderdetail->quantity }}</td>
                        <td>
                            <form href="{{ route('orderdetail.destroy', $orderdetail->id)}}" method="POST">
                                @method('delete')
                                @csrf
                                <input class="btn btn-success btn-sm" type="submit" value="Wybierz" />
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
