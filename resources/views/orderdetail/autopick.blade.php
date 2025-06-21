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
                    $isHeaviest = isset($heaviestDetail) && $detail->id === $heaviestDetail->id;
                    $weightPerItem = $product->weight ?? 0;
                    $itemTotalWeight = $weightPerItem * $detail->quantity;
                    $volume = ($product->size_x && $product->size_y && $product->size_z)
                        ? ($product->size_x * $product->size_y * $product->size_z) / 1000000
                        : null;
                @endphp
                <tr class="{{ $isHeaviest ? 'table-warning' : '' }}">
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
                    <td>{{ number_format($weightPerItem, 2) }} kg</td>
                    <td>{{ $volume !== null ? number_format($volume, 4) . ' m³' : 'brak' }}</td>
                    <td>{{ number_format($itemTotalWeight, 2) }} kg</td>
                    <td>{{ $product->can_overhang ? 'Tak' : 'Nie' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<hr>
<h5 class="mb-3">📦 Kompletacja objętościowa</h5>
<div class="table-responsive-sm mb-4">
    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Lp.</th>
                <th>Opakowanie (EAN / Typ)</th>
                <th>Produkty</th>
                <th>Użyta objętość</th>
                <th>Użyta waga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($volumeAlgorithm as $i => $package)
                @php
                    $unit = $storeunits->firstWhere('id', $package['storeunit_id']);
                    $type = $unit?->storeunittype?->code ?? 'brak';
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $unit->ean ?? 'brak' }} / {{ $type }}</td>
                    <td>
                        <ul class="mb-0">
                            @foreach ($package['products'] as $p)
                                @php
                                    $detail = $orderdetails->firstWhere('id', $p['detail_id']);
                                @endphp
                                <li>{{ $detail->prod_code ?? '??' }} – {{ $p['quantity'] }} szt.</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ number_format($package['volume_used'], 2) }} cm³</td>
                    <td>{{ number_format($package['weight_used'], 2) }} kg</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<p><strong>Liczba użytych opakowań:</strong> {{ count($volumeAlgorithm) }}</p>
<p><strong>Średnie wykorzystanie objętości:</strong>
    {{ number_format(collect($volumeAlgorithm)->avg('volume_used'), 2) }} cm³
</p>
<p><strong>Średnie wykorzystanie wagi:</strong>
    {{ number_format(collect($volumeAlgorithm)->avg('weight_used'), 2) }} kg
</p>
<ul>
                <li>Użyte opakowania: <strong>{{ $unitsUsedCount }}</strong></li>
                <li>Całkowita pojemność: <strong>{{ number_format($volumeCapacity, 3, ',', ' ') }} m³</strong></li>
                <li>Zużyta objętość: <strong>{{ number_format($volumeUsed, 3, ',', ' ') }} m³</strong></li>
                <li>Wypełnienie objętościowe: <strong>{{ $volumeFillPercent }}%</strong></li>
                <li>Wypełnienie wagowe: <strong>{{ $weightFillPercent }}%</strong></li>
            </ul>

<h5 class="mb-3">⚖️ Kompletacja wagowa</h5>
<div class="table-responsive-sm">
    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Lp.</th>
                <th>Opakowanie (EAN / Typ)</th>
                <th>Produkty</th>
                <th>Użyta objętość</th>
                <th>Użyta waga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($weightAlgorithm as $i => $package)
                @php
                    $unit = $storeunits->firstWhere('id', $package['storeunit_id']);
                    $type = $unit?->storeunittype?->code ?? 'brak';
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $unit->ean ?? 'brak' }} / {{ $type }}</td>
                    <td>
                        <ul class="mb-0">
                            @foreach ($package['products'] as $p)
                                @php
                                    $detail = $orderdetails->firstWhere('id', $p['detail_id']);
                                @endphp
                                <li>{{ $detail->prod_code ?? '??' }} – {{ $p['quantity'] }} szt.</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ number_format($package['volume_used'], 2) }} cm³</td>
                    <td>{{ number_format($package['weight_used'], 2) }} kg</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@php
        $weightUsedW = array_sum(array_column($weightAlgorithm, 'weight_used'));
        $volumeUsedW = array_sum(array_column($weightAlgorithm, 'volume_used')) / 1000000;

        $unitsUsedWeight = collect();
        $weightCapacityW = 0;
        $volumeCapacityW = 0;

        foreach ($weightAlgorithm as $entry) {
            $unit = $storeunits->firstWhere('id', $entry['storeunit_id']);
            if ($unit && $unit->storeunittype) {
                $unitsUsedWeight->push($unit);
                $t = $unit->storeunittype;
                $volumeCapacityW += ($t->size_x * $t->size_y * $t->size_z) / 1000000;
                $weightCapacityW += $t->loadwgt ?? 0;
            }
        }

        $weightFillPercentW = $weightCapacityW > 0 ? round(($weightUsedW / $weightCapacityW) * 100, 1) : 0;
        $volumeFillPercentW = $volumeCapacityW > 0 ? round(($volumeUsedW / $volumeCapacityW) * 100, 1) : 0;
    @endphp

    <ul>
        <li>Użyte opakowania: <strong>{{ count($unitsUsedWeight) }}</strong></li>
        <li>Całkowita pojemność: <strong>{{ number_format($volumeCapacityW, 3, ',', ' ') }} m³</strong></li>
        <li>Zużyta objętość: <strong>{{ number_format($volumeUsedW, 3, ',', ' ') }} m³</strong></li>
        <li>Wypełnienie objętościowe: <strong>{{ $volumeFillPercentW }}%</strong></li>
        <li>Całkowita nośność: <strong>{{ number_format($weightCapacityW, 2, ',', ' ') }} kg</strong></li>
        <li>Zużyta waga: <strong>{{ number_format($weightUsedW, 2, ',', ' ') }} kg</strong></li>
        <li>Wypełnienie wagowe: <strong>{{ $weightFillPercentW }}%</strong></li>
    </ul>

            <div class="text-end mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Powrót</a>
                <form action="{{ route('orders.confirm', $order->id) }}" method="POST">
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
