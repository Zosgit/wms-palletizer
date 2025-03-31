@extends('layouts.app')
@section('content')
<a class="big" href="{{ route('pick.index')}}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista zamówień</a></br></br>
<div class="container mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center">
            <div class="col-md-6">
                Aktywne Opakowanie:  <strong>{{ $order->order_nr }}</strong>
            </div>
            @if ($order->status_id == 503)
            <div class="col-md-6">
                <form action="{{ route('pick.close',['id'=>$order->id]) }}" method="post">
                    @csrf
                        <button type="submit" class="btn btn-primary btn-sm float-end px-4">Wydaj zamówienie</button>
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
        <!-- miejsce-->
        <div>
             Miejsce: <strong>{{ $order->location->ean}}</strong>
        </div>
      </div>
    </div>
  </div>
  @if(isset($pickings))
  <div class="container">
      <div class="card mb-4">
          <div class="card-header d-flex align-items-center">
              Kompletacja
          </div>
          <div class="card-body">
          <div class="table-responsive-sm">
              <table class="table table-striped table-hover table-sm">
              <thead>
                  <tr>
                      <th>Opakowanie</th>
                      <th>Indeks</th>
                      <th>Nr seryjny</th>
                      <th>Termin</th>
                      <th>Magazyn</th>
                      <th class="center">Ilość</th>
                      <th>Uwagi</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($pickings as $pick)
                      <tr>
                          <td>{{ $pick->store_unit_ean}}</td>
                          <td>{{ $pick->prod_code}}</td>
                          <td>{{ $pick->serial_nr }}</td>
                          <td>{{ $pick->expiration_at }}</td>
                          <td>{{ $pick->logical_area->code ?? ''}}</td>
                          <td>{{ $pick->quantity }}</td>
                          <td>{{ $pick->remarks }}</td>
                      </tr>
                  @endforeach
                  </tbody>

              </table>
          </div>
          </div>
      </div>
  </div>
  @endif
  @endsection
