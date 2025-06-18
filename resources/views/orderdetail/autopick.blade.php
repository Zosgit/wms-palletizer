{{-- http://127.0.0.1:8000/orderdetail/37/autopick --}}

@extends('layouts.app')

@section('title') {{ 'Kompletacja automatyczna' }} @endsection

@section('content')
<a class="big" href="{{ route('orders.index') }}">
    <svg class="icon">
        <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
    </svg>&nbsp;Lista zamówień
</a>
<br><br>
<div class="container">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div>Zamówienie nr: <strong>{{ $order->order_nr }}</strong></div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4">
                    <h6 class="mb-3">Szczegóły:</h6>
                    <div>Dok: wydania: <strong>{{ $order->external_nr }}</strong></div>
                    <div>Data wydania: <strong>{{ $order->created_at }}</strong></div>
                    <div>Status: <strong>{{ $order->status->code }}</strong></div>
                    <div>Uwagi: {{ $order->remarks }}</div>
                </div>

                <div class="col-sm-4">
                    <h6 class="mb-3">Odbiorca:</h6>
                    <div><strong>{{ $order->firm->code }}</strong></div>
                    <div>{{ $order->firm->longdesc }}</div>
                    <div>{{ $order->firm->postcode }} - {{ $order->firm->city }}</div>
                </div>

                <div class="col-sm-4">
                    <h6 class="mb-3">Właściciel:</h6>
                    <div><strong>{{ $order->owner->code }}</strong></div>
                    <div>{{ $order->owner->longdesc }}</div>
                    <div>{{ $order->owner->postcode }} - {{ $order->owner->city }}</div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3">Podsumowanie kompletacji</h5>

            <div class="mb-4">


                <hr>
                {{-- <p><strong>Użyte opakowania:</strong></p>
                    @if($storeunits->count() > 0)
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kod EAN</th>
                                    <th>Typ opakowania</th>
                                    <th>Wymiary (cm)</th>
                                    <th>Objętość (m³)</th>
                                    <th>Max waga (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($storeunits as $unit)
                                    @php
                                        $type = $unit->storeunittype;
                                        $volume = null;
                                        if ($type && $type->size_x && $type->size_y && $type->size_z) {
                                            $volume = ($type->size_x * $type->size_y * $type->size_z) / 1000000;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $unit->ean ?? '-' }}</td>
                                        <td>{{ $unit->storeunittype->code ?? '-' }}</td>
                                        <td>{{ $unit->storeunittype->size_x }} x {{ $unit->storeunittype->size_y }} x {{ $unit->storeunittype->size_z }}</td>
                                        <td>{{ $volume ? number_format($volume, 3) : 'brak danych' }}</td>
                                        <td>{{ $unit->storeunittype->loadwgt ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-danger">Brak jednostek opakowaniowych do wyświetlenia.</p>
                    @endif --}}
            </div>

            <div class="table-responsive-sm">
                <table class="table table-striped table-hover table-sm">
    <thead>
        <tr>
            <th>Produkt</th>
            <th>Materiał</th>
            <th>Ilość</th>
            <th>Waga (1 szt.)</th>
            <th>Objętość (1 szt.)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderdetails as $detail)
            @php
                $product = $detail->product;
                $weight = $product->weight ?? null;
                $volume = null;

                if ($product && $product->size_x && $product->size_y && $product->size_z) {
                    $volume = ($product->size_x * $product->size_y * $product->size_z) / 1000000; // m³
                }
            @endphp
            <tr>
                <td>{{ $detail->prod_code }}</td>
                <td>{{ $product->material_type ?? 'brak danych' }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $weight !== null ? number_format($weight, 2) . ' kg' : 'brak' }}</td>
                <td>{{ $volume !== null ? number_format($volume, 4) . ' m³' : 'brak' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

            </div>


{{-- <div class="alert alert-success">
    <p><strong>Optymalnie dobrane opakowania ({{ $unitsUsedCount }} szt.):</strong></p>
    <ul>
        @foreach ($usedUnits as $unit)
            <li>
                EAN: {{ $unit->ean ?? '-' }},
                Typ: {{ $unit->storeunittype->code ?? '-' }},
                Wymiary: {{ $unit->storeunittype->size_x }}x{{ $unit->storeunittype->size_y }}x{{ $unit->storeunittype->size_z }} cm,
                Objętość: {{ number_format(($unit->storeunittype->size_x * $unit->storeunittype->size_y * $unit->storeunittype->size_z)/1000000, 3) }} m³,
                Max waga: {{ $unit->storeunittype->loadwgt }} kg
            </li>
        @endforeach
    </ul>
</div> --}}



<div class="alert alert-info mt-4">
    <strong>Statystyki zamówienia:</strong><br>
    Liczba pozycji: {{ $uniqueProducts }}<br>
    Łączna ilość sztuk: {{ $totalItems }}<br>
    Waga całkowita: {{ number_format($totalWeight, 2) }} kg<br>
    Objętość całkowita: {{ number_format($totalVolume, 4) }} m³<br>
    <br>
    Wykorzystano {{ $unitsUsedCount }} opakowań<br>
    Objętość opakowań: {{ number_format($usedVolumeTotal, 4) }} m³ (wypełnienie: {{ $volumeFillPercent }}%)<br>
    Ładowność opakowań: {{ number_format($usedWeightCapacityTotal, 2) }} kg (wypełnienie: {{ $weightFillPercent }}%)<br>
    <br>
    Produkty wg kruchości:<br>
    - Twarde: {{ $fragilityStats['twardy'] }}<br>
    - Miękkie: {{ $fragilityStats['miekki'] }}<br>
    - Kruche: {{ $fragilityStats['kruchy'] }}
</div>


<div class="alert alert-info mt-4">
    <h5><strong>📦 Dane zamówienia</strong></h5>
    <ul>
        <li><strong>Liczba pozycji:</strong> {{ $uniqueProducts }}</li>
        <li><strong>Łączna ilość sztuk:</strong> {{ $totalItems }}</li>
        <li><strong>Waga całkowita:</strong> {{ number_format($totalWeight, 2) }} kg</li>
        <li><strong>Objętość całkowita:</strong> {{ number_format($totalVolume, 4) }} m³</li>
        <li><strong>Średnia waga produktu:</strong> {{ $totalItems > 0 ? number_format($totalWeight / $totalItems, 3) : 0 }} kg</li>
        <li><strong>Średnia objętość produktu:</strong> {{ $totalItems > 0 ? number_format($totalVolume / $totalItems, 6) : 0 }} m³</li>
    </ul>
</div>


<div class="alert alert-warning mt-4">
    <h5><strong>📦 Użyte opakowania</strong></h5>
    <ul>
        <li><strong>Liczba opakowań:</strong> {{ $unitsUsedCount }}</li>
        <li><strong>Objętość wszystkich opakowań:</strong> {{ number_format($usedVolumeTotal, 4) }} m³</li>
        <li><strong>Wypełnienie objętościowe:</strong> {{ $volumeFillPercent }}%</li>
        <li><strong>Ładowność wszystkich opakowań:</strong> {{ number_format($usedWeightCapacityTotal, 2) }} kg</li>
        <li><strong>Wypełnienie wagowe:</strong> {{ $weightFillPercent }}%</li>
        <li><strong>Średnia objętość opakowania:</strong> {{ $unitsUsedCount > 0 ? number_format($usedVolumeTotal / $unitsUsedCount, 4) : 0 }} m³</li>
        <li><strong>Średnia ładowność opakowania:</strong> {{ $unitsUsedCount > 0 ? number_format($usedWeightCapacityTotal / $unitsUsedCount, 2) : 0 }} kg</li>
    </ul>
</div>


@php
    $totalFragileCount = array_sum($fragilityStats);
@endphp


@if (isset($paletCount) && $paletCount > 0)
    <div class="alert alert-primary mt-4">
        <h5><strong>📊 Szacowana liczba opakowań</strong></h5>
        <ul>
            <li><strong>Na podstawie objętości:</strong> {{ $volumeBasedCount }} opakowania</li>
            <li><strong>Na podstawie wagi:</strong> {{ $weightBasedCount }} opakowania</li>
            <li><strong>Wybrana liczba palet:</strong> {{ $paletCount }} opakowania<br>
                <small class="text-muted">na podstawie {{ $paletBasis }}</small>
            </li>
        </ul>
    </div>
@endif

{{-- Statystyki kompletacji --}}
@if ($unitsUsedCount > 0)
    <div class="alert alert-success mt-4">
        <h5><strong>📦 Użyte opakowania (konkretne jednostki z magazynu)</strong></h5>
        <ul>
            @foreach ($usedUnits as $unit)
                <li>
                    EAN: <strong>{{ $unit->ean }}</strong>,
                    Typ: {{ $unit->storeunittype->code ?? '-' }},
                    Wymiary: {{ $unit->storeunittype->size_x }}x{{ $unit->storeunittype->size_y }}x{{ $unit->storeunittype->size_z }} cm,
                    Objętość: {{ number_format(($unit->storeunittype->size_x * $unit->storeunittype->size_y * $unit->storeunittype->size_z)/1000000, 3) }} m³,
                    Max waga: {{ $unit->storeunittype->loadwgt }} kg
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="alert alert-info mt-4">
    <h5><strong>📊 Statystyki zamówienia</strong></h5>
    <ul>
        <li><strong>Liczba pozycji:</strong> {{ $uniqueProducts }}</li>
        <li><strong>Łączna ilość sztuk:</strong> {{ $totalItems }}</li>
        <li><strong>Waga całkowita:</strong> {{ number_format($totalWeight, 2) }} kg</li>
        <li><strong>Objętość całkowita:</strong> {{ number_format($totalVolume, 4) }} m³</li>
        <li><strong>Wypełnienie opakowań objętościowo:</strong> {{ $volumeFillPercent }}%</li>
        <li><strong>Wypełnienie opakowań wagowo:</strong> {{ $weightFillPercent }}%</li>
    </ul>
</div>

<div class="alert alert-primary mt-4">
    <h5><strong>📦 Szacowana liczba opakowań</strong></h5>
    <ul>
        <li>Na podstawie objętości: {{ $volumeBasedCount }} palet</li>
        <li>Na podstawie wagi: {{ $weightBasedCount }} palet</li>
        <li><strong>Użyta liczba opakowań:</strong> {{ $paletCount }}<br>
            <small class="text-muted">wybrano na podstawie {{ $paletBasis }}</small>
        </li>
    </ul>
</div>

<div class="alert alert-danger mt-4">
    <h5><strong>🧊 Kruchość produktów</strong></h5>
    <ul>
        <li>Twarde: {{ $fragilityStats['twardy'] }}</li>
        <li>Miękkie: {{ $fragilityStats['miekki'] }}</li>
        <li>Kruche: {{ $fragilityStats['kruchy'] }}</li>
    </ul>
</div>

{{-- @if ($remainingVolume > 0 || $remainingWeight > 0)
    <div class="alert alert-warning">
        <strong>⚠️ Uwaga!</strong> Na magazynie brakuje wystarczającej liczby opakowań, by zapakować całe zamówienie.
    </div>
@endif --}}




            <div class="text-end mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Powrót</a>
                <button class="btn btn-success">Zatwierdź kompletację</button>
            </div>
        </div>
    </div>
</div>
@endsection
