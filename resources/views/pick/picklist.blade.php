@extends('layouts.app')
@section('title') {{ 'pick.picklist' }} @endsection
@section('content')
<a class="big" href="{{ route('pick.index') }}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista zamówień</a></br></br>
  @if(isset($storeunit))
  <div class="container">
      <div class="card mb-4">
        <div class="card-header d-flex">
            <div class="col-md-6">
                Aktywne Opakowanie:  <strong>{{ $storeunit->ean }}</strong>
            </div>
            <div class="col-md-6">
            <form action="{{ route('pick.licklistsu_close',['id'=>$id,'su'=>$storeunit->id]) }}" method="post">
                @csrf
                        <button type="submit" class="btn btn-primary btn-sm float-end px-4">Zakończ opakowanie</button>
            </form>
            </div>
          </div>
      </div>
  </div>


  @endif

<div class="container">
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center">
        <div>Lista nr: <strong>{{ $id}}</strong>
        </div>

       </div>
      <div class="card-body">
        <div class="row mb-4">

        <div class="table-responsive-sm">
          <table class="table table-striped table-hover table-sm">
            <thead>
              <tr>
                <th>Indeks</th>
                <th>Kategoria</th>
                <th class="center">ilość</th>
                <th class="center">Pozostało</th>
                <th class="center"></th>
              </tr>
            </thead>
            <tbody>
                @foreach($orderdetails as $orderdetail)
                    <tr>
                        <td>{{ $orderdetail->prod_code }}</td>
                        <td class="center">{{ $orderdetail->logical_area->longdesc }}</td>
                        <td class="right">{{ $orderdetail->quantity }}</td>
                        <td class="right">{{ $orderdetail->quantity_pick }}</td>
                        <td>
                            <form action="{{ route('pick.picklist2',['id'=>$orderdetail->id]) }}" method="post">
                                @csrf
                                <div class="row ">
                                    <div class="col-md-6 input-group-sm">
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" class="btn btn-success btn-sm">Wybierz</button>
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
