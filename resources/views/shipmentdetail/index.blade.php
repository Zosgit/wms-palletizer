@extends('layouts.app')
@section('content')

<a class="big" href="{{ url()->previous() }}">
    <svg class="icon"><use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use></svg>
    &nbsp;Lista dostaw
</a>
<br><br>

<div class="container">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div>Dostawa nr: <strong>{{ $shipment->ship_nr }}</strong></div>

            @if ($shipmentdetails->count() > 0)
                <form method="post" action="{{ route('shipmentdetail.send', ['id' => $shipment->id]) }}" class="ms-auto">
                    @csrf
                    <button class="btn btn-sm btn-success px-4">Kontroluj</button>
                </form>
            @endif
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4">
                    <h6 class="mb-3">Szczegóły:</h6>
                    <div>Dok: dostawy: <strong>{{ $shipment->external_nr }}</strong></div>
                    <div>Data przyjęcia: <strong>{{ $shipment->created_at }}</strong></div>
                    <div>Status: <strong>{{ $shipment->status->code }}</strong></div>
                    <div>Uwagi: {{ $shipment->remarks }}</div>
                </div>
                <div class="col-sm-4">
                    <h6 class="mb-3">Dostarczył:</h6>
                    <div><strong>{{ $shipment->firm->code }}</strong></div>
                    <div>{{ $shipment->firm->longdesc }}</div>
                    <div>{{ $shipment->firm->postcode }} - {{ $shipment->firm->city }}</div>
                </div>
                <div class="col-sm-4">
                    <h6 class="mb-3">Właściciel:</h6>
                    <div><strong>{{ $shipment->owner->code }}</strong></div>
                    <div>{{ $shipment->owner->longdesc }}</div>
                    <div>{{ $shipment->owner->postcode }} - {{ $shipment->owner->city }}</div>
                </div>
            </div>

            <a href="{{ route('shipmentdetail.create', ['shipment' => $shipment]) }}" class="btn btn-primary float-start px-4 btn-sm mb-3">
                Dodaj pozycję
            </a>

            <div class="table-responsive-sm">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Indeks</th>
                            <th>Nr seryjny</th>
                            <th>Termin</th>
                            <th>Magazyn</th>
                            <th>Kategoria</th>
                            <th class="center">Ilość</th>
                            <th class="center">Operacje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedDetails as $group)
                            @if (isset($group['is_set']) && $group['is_set'])
                                <tr class="table-set-header">
                                    <td colspan="7">
                                        <strong>Komplet: {{ $group['set_name'] }}</strong>
                                    </td>
                                </tr>
                                @foreach ($group['items'] as $item)
                                    <tr class="table-set-child table-light">
                                        <td class="ps-4"> {{ $item->prod_code }}</td>
                                        <td>{{ $item->serial_nr }}</td>
                                        <td>{{ $item->expiration_at }}</td>
                                        <td>{{ $item->logical_area->code }}</td>
                                        <td>{{ $item->product->producttype->code ?? '-' }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('shipmentdetail.destroy', $item->id) }}">
                                                @method('delete')
                                                @csrf
                                                <input class="btn btn-danger btn-sm" type="submit" value="Usuń" />
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $group->prod_code }}</td>
                                    <td>{{ $group->serial_nr }}</td>
                                    <td>{{ $group->expiration_at }}</td>
                                    <td>{{ $group->logical_area->code }}</td>
                                    <td>{{ $group->product->producttype->code ?? '-' }}</td>
                                    <td class="text-end">{{ $group->quantity }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('shipmentdetail.destroy', $group->id) }}">
                                            @method('delete')
                                            @csrf
                                            <input class="btn btn-danger btn-sm" type="submit" value="Usuń" />
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- STYLE dla kompletów --}}
<style>
    tr.table-set-header {
        background-color: #dee2e6;
        font-weight: bold;
    }

    tr.table-set-child td:first-child::before {
        content: '↳ ';
        color: #6c757d;
    }
</style>

@endsection
