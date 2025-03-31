@extends('layouts.app')
@section('content')
<a class="big" href="{{ url()->previous() }}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista dostaw</a></br></br>
<div class="container">
    <div class="card">
      <div class="card-header d-flex align-items-center"><div>Dostawa nr: <strong>{{ $shipment->ship_nr}}</strong>
    </div>

        @if ($shipmentdetails->total() > 0)
            <form method="post" action="{{ route('shipmentdetail.send',['id' => $shipment->id]) }}">
                @csrf
                <button class="btn btn-sm btn-success float-end px-4">Kontroluj</button>
            </form>
        @endif


        </div>
      <div class="card-body">
        <div class="row mb-4">
          <!-- /.col-->
          <div class="col-sm-4">
            <h6 class="mb-3">Szczegóły:</h6>
            <div>Dok: dostawy: <strong>{{ $shipment->external_nr}}</strong></div>
            <div>Data przyjęcia: <strong>{{ $shipment->created_at}}</strong></div>
            <div>Status: <strong>{{ $shipment->status->code}}</strong></div>
            <div>Uwagi: {{ $shipment->remarks}}</div>
          </div>
          <div class="col-sm-4">
            <h6 class="mb-3">Dostarczył:</h6>
            <div><strong>{{ $shipment->firm->code}}</strong></div>
            <div>{{ $shipment->firm->longdesc}}</div>
            <div>{{ $shipment->firm->postcode.' - '.$shipment->firm->city}}</div>
          </div>
          <!-- /.col-->
          <div class="col-sm-4">
            <h6 class="mb-3">Właściciel:</h6>
            <div><strong>{{ $shipment->owner->code}}</strong></div>
            <div>{{ $shipment->owner->longdesc}}</div>
            <div>{{ $shipment->owner->postcode.' - '.$shipment->owner->city}}</div>
          </div>
          <!-- /.col-->
        </div>
        <!-- /.row-->

        <a href="{{ route('shipmentdetail.create',['shipment' => $shipment]) }}"
        class="btn btn-primary float-start px-4 btn-sm">Dodaj pozycję</a>
        <div class="table-responsive-sm">
          <table class="table table-striped table-hover table-sm">
            <thead>
              <tr>
                <th>Indeks</th>
                <th>Nr seryjny</th>
                <th>Termin</th>
                <th>Magazyn</th>
                <th>Kategoria</th>
                <th class="center">ilość</th>
                <th class="center">Operacje</th>
              </tr>
            </thead>
            <tbody>
                @foreach($shipmentdetails as $shipmentdetail)
                    <tr>
                        <td>{{ $shipmentdetail->prod_code }}</td>
                        <td class="center">{{ $shipmentdetail->serial_nr }}</td>
                        <td class="center">{{ $shipmentdetail->expiration_at }}</td>
                        <td class="center">{{ $shipmentdetail->logical_area->code }}</td>
                        <td class="center">{{ $shipmentdetail->product->producttype->code }}</td>
                        <td class="right">{{ $shipmentdetail->quantity }}</td>
                        <td>
                            <form href="{{ route('shipmentdetail.destroy', $shipmentdetail->id)}}" method="POST">
                                @method('delete')
                                @csrf
                                <input class="btn btn-danger btn-sm" type="submit" value="Delete" />
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
