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

            {{-- Sekcja porównawcza: Algorytm objętościowy vs wagowy --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="alert alert-primary">
                        <h5><strong>📦 Algorytm objętościowy</strong></h5>
                        <ul>
                            <li><strong>Liczba opakowań:</strong> {{ $volCount }}</li>
                            <li><strong>Całkowita objętość opakowań:</strong> {{ number_format($volVolume, 4) }} m³</li>
                            <li><strong>Ładowność całkowita:</strong> {{ number_format($volWeight, 2) }} kg</li>
                        </ul>
                        @if (count($volumeUnits) > 0)
                        <div class="mt-2">
                            <p><strong>Użyte opakowania:</strong></p>
                            <ul>
                                @foreach ($volumeUnits as $unit)
                                    <li>
                                        EAN: {{ $unit->ean ?? '-' }},
                                        Typ: {{ $unit->storeunittype->code ?? '-' }},
                                        Wymiary: {{ $unit->storeunittype->size_x }}x{{ $unit->storeunittype->size_y }}x{{ $unit->storeunittype->size_z }} cm,
                                        Objętość: {{ number_format(($unit->storeunittype->size_x * $unit->storeunittype->size_y * $unit->storeunittype->size_z)/1000000, 3) }} m³,
                                        Max waga: {{ $unit->storeunittype->loadwgt }} kg
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h5><strong>📦 Algorytm wagowy</strong></h5>
                        <ul>
                            <li><strong>Liczba opakowań:</strong> {{ $wgtCount }}</li>
                            <li><strong>Całkowita objętość opakowań:</strong> {{ number_format($wgtVolume, 4) }} m³</li>
                            <li><strong>Ładowność całkowita:</strong> {{ number_format($wgtWeight, 2) }} kg</li>
                        </ul>
                        @if (count($weightUnits) > 0)
                        <div class="mt-2">
                            <p><strong>Użyte opakowania:</strong></p>
                            <ul>
                                @foreach ($weightUnits as $unit)
                                    <li>
                                        EAN: {{ $unit->ean ?? '-' }},
                                        Typ: {{ $unit->storeunittype->code ?? '-' }},
                                        Wymiary: {{ $unit->storeunittype->size_x }}x{{ $unit->storeunittype->size_y }}x{{ $unit->storeunittype->size_z }} cm,
                                        Objętość: {{ number_format(($unit->storeunittype->size_x * $unit->storeunittype->size_y * $unit->storeunittype->size_z)/1000000, 3) }} m³,
                                        Max waga: {{ $unit->storeunittype->loadwgt }} kg
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

           <div class="table-responsive-sm">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Lp.</th>
                            <th>Produkt</th>
                            <th>Materiał</th>
                            <th>Ilość</th>
                            <th>Waga (1 szt.)</th>
                            <th>Objętość (1 szt.)</th>
                            <th>Waga łączna</th>
                            <th>Czy może wystawać?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderdetails as $detail)
                            @php
                                $product = $detail->product;
                                $isMissing = in_array($detail, $missingProducts, true);
                                $isHeaviest = isset($heaviestDetail) && $detail->id === $heaviestDetail->id;
                                $weightPerItem = $product->weight ?? 0;
                                $itemTotalWeight = $weightPerItem * $detail->quantity;
                                $volume = ($product->size_x && $product->size_y && $product->size_z)
                                    ? ($product->size_x * $product->size_y * $product->size_z) / 1000000
                                    : null;
                            @endphp
                            <tr class="{{ $isMissing ? 'table-danger' : ($isHeaviest ? 'table-warning' : '') }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $detail->prod_code }}
                                    @if ($isHeaviest)
                                        <span class="badge bg-warning text-dark">najcięższy</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $product->material_type ?? 'brak danych' }}
                                    @if(isset($product->material_type) && Str::of($product->material_type)->contains('kruchy'))
                                        <span class="badge bg-danger-subtle text-danger ms-1">!</span>
                                    @endif
                                </td>
                                <td>{{ $detail->quantity }}</td>
                                <td>@if ($isMissing) ❌ brak @else {{ number_format($weightPerItem, 2) }} kg @endif</td>
                                <td>@if ($isMissing) ❌ brak @elseif($volume !== null) {{ number_format($volume, 4) }} m³ @else brak @endif</td>
                                <td>@if ($isMissing) ❌ brak @else {{ number_format($itemTotalWeight, 2) }} kg @endif</td>
                                <td>{{ $product->can_overhang ? 'Tak' : 'Nie' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($noUnitsAvailable)
                <div class="alert alert-danger mt-4">
                    ⚠️ Brakuje dostępnych opakowań na magazynie (status: nowa lub dostępna), które spełniają wymagania wagowe i objętościowe. Nie udało się spakować całego zamówienia.
                </div>
            @endif

            <div class="text-end mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Powrót</a>
                <form action="{{ route('orders.confirm', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        Zatwierdź kompletację
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
