@extends('layouts.app')
@section('title') {{ 'pick.picklistsu' }} @endsection
@section('content')
<a class="big" href="{{ route('orders.index') }}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Lista zamówień</a></br></br>
<div class="container">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <div>Lista opakowań: <strong>{{ $id}}</strong>
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
                <th class="center"></th>
              </tr>
            </thead>
            <tbody>
                @foreach($orderdetails as $orderdetail)
                    <tr>
                        <td>{{ $orderdetail->prod_code }}</td>
                        <td class="center">{{ $orderdetail->logical_area->longdesc }}</td>
                        <td class="right">{{ $orderdetail->quantity }}</td>
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
  @endsection
