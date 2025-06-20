@extends('layouts.app')
@section('content')

<a class="big" href="{{ route('control.index') }}">
    <svg class="icon"><use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use></svg>
    &nbsp;Lista dostaw
</a>
<br><br>

<div class="container mb-4">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div>Dostawa nr: <strong>{{ $shipment->ship_nr }}</strong></div>

            @if ($full_control == 0 and $shipment->status_id == 403)
                <form method="post" action="{{ route('control.close', ['id' => $shipment->id]) }}" class="ms-auto">
                    @csrf
                    <button class="btn btn-sm btn-success px-4">Zakończ kontrolę</button>
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

            <div>
                Miejsce: <strong>{{ $shipment->location->ean }}</strong>
            </div>

            <div class="table-responsive-sm mt-3">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Indeks</th>
                            <th>Nr seryjny</th>
                            <th>Termin</th>
                            <th>Magazyn</th>
                            <th>Kategoria</th>
                            <th class="center">Dostawa</th>
                            <th class="center">Pozostało</th>
                            <th class="center">Operacje</th>
                        </tr>
                    </thead>
<tbody>
    @foreach($groupedDetails as $group)
        @if (isset($group['is_set']) && $group['is_set'])
            <tr class="table-set-header">
                <td colspan="8">
                    <strong>Komplet: {{ $group['set_name'] }}</strong>
                </td>
            </tr>
            @foreach ($group['products'] as $item)
                <tr class="table-set-child table-light">
                    <td class="ps-4"> {{ $item->prod_code }}</td>
                    <td>{{ $item->serial_nr }}</td>
                    <td>{{ $item->expiration_at }}</td>
                    <td>{{ $item->logical_area->code ?? '-' }}</td>
                    <td>{{ $item->product->producttype->code ?? '-' }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">{{ $item->quantity_control }}</td>
                    <td>
                        @if ($item->quantity_control > 0)
                            <a href="{{ route('control.add', ['id' => $item->id, 'loc' => $shipment->location_id]) }}">
                                <svg class="icon icon-lg">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                                </svg>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>{{ $group['product']->prod_code }}</td>
                <td>{{ $group['product']->serial_nr }}</td>
                <td>{{ $group['product']->expiration_at }}</td>
                <td>{{ $group['product']->logical_area->code ?? '-' }}</td>
                <td>{{ $group['product']->product->producttype->code ?? '-' }}</td>
                <td class="text-end">{{ $group['product']->quantity }}</td>
                <td class="text-end">{{ $group['product']->quantity_control }}</td>
                <td>
                    @if ($group['product']->quantity_control > 0)
                        <a href="{{ route('control.add', ['id' => $group['product']->id, 'loc' => $shipment->location_id]) }}">
                            <svg class="icon icon-lg">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                            </svg>
                        </a>
                    @endif
                </td>
            </tr>
        @endif
    @endforeach
</tbody>

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


                </table>
            </div>
        </div>
    </div>
</div>

{{-- Sekcja przyjętych pozycji --}}
@if(isset($controls))
    <div class="container">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                Przyjęte pozycje
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
                            @foreach($controls as $control)
                                <tr>
                                    <td>{{ $control->ean }}</td>
                                    <td>{{ $control->prod_code }}</td>
                                    <td>{{ $control->serial_nr }}</td>
                                    <td>{{ $control->expiration_at }}</td>
                                    <td>{{ $control->code_logical }}</td>
                                    <td>{{ $control->quantity }}</td>
                                    <td>{{ $control->remarks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- STYLE kompletów --}}
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
