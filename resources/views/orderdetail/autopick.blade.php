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
                                    $isMissing = in_array($detail, $missingProducts ?? [], true);
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
                                           <span class="badge bg-danger-subtle text-danger ms-1" title="Produkt kruchy">!</span>
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

                <div class="alert alert-info mt-4">
                    <h5><strong>Dane zamówienia</strong></h5>
                    <ul>
                        <li><strong>Liczba pozycji:</strong> {{ $uniqueProducts }}</li>
                        <li><strong>Łączna ilość sztuk:</strong> {{ $totalItems }}</li>
                        <li><strong>Waga całkowita:</strong> {{ number_format($totalWeight, 3) }} kg</li>
                        <li><strong>Objętość całkowita:</strong> {{ number_format($totalVolume, 3) }} m³</li>
                        <li><strong>Średnia waga produktu:</strong> {{ $totalItems > 0 ? number_format($totalWeight / $totalItems, 3) : 0 }} kg</li>
                        <li><strong>Średnia objętość produktu:</strong> {{ $totalItems > 0 ? number_format($totalVolume / $totalItems, 3) : 0 }} m³</li>
                    </ul>
                </div>


                <hr>
                <div class="row">
                    {{-- ALGORYTM OBJĘTOŚCIOWY --}}
                    <div class="col-md-6">
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Kompletacja objętościowa</h5>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>Użyte opakowania:</strong> {{ $unitsUsedCount }}</li>
                                    <li><strong>Całkowita pojemność:</strong> {{ number_format($volumeCapacity, 3, ',', ' ') }} m³</li>
                                    <li><strong>Zużyta objętość:</strong> {{ number_format($volumeUsed, 3, ',', ' ') }} m³</li>
                                    <li><strong>Wypełnienie objętościowe:</strong> {{ $volumeFillPercent }}%</li>
                                    <li><strong>Wypełnienie wagowe:</strong> {{ $weightFillPercent }}%</li>
                                </ul>
                                <hr>
                                <div class="table-responsive-sm mb-3">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Lp.</th>
                                                <th>Opakowanie <br>(EAN / Typ)</th>
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
                                                    <td>{{ number_format($package['volume_used'] / 1000000, 4) }} m³</td>
                                                    <td>{{ number_format($package['weight_used'], 2) }} kg</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ALGORYTM WAGOWY --}}
                    <div class="col-md-6">
                        <div class="card border-success mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Kompletacja wagowa</h5>
                            </div>
                            <div class="card-body">
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

                                <ul class="mb-0">
                                    <li><strong>Użyte opakowania:</strong> {{ count($unitsUsedWeight) }}</li>
                                    <li><strong>Całkowita pojemność:</strong> {{ number_format($volumeCapacityW, 3, ',', ' ') }} m³</li>
                                    <li><strong>Zużyta objętość:</strong> {{ number_format($volumeUsedW, 3, ',', ' ') }} m³</li>
                                    <li><strong>Wypełnienie objętościowe:</strong> {{ $volumeFillPercentW }}%</li>
                                    <li><strong>Całkowita nośność:</strong> {{ number_format($weightCapacityW, 2, ',', ' ') }} kg</li>
                                    <li><strong>Zużyta waga:</strong> {{ number_format($weightUsedW, 2, ',', ' ') }} kg</li>
                                    <li><strong>Wypełnienie wagowe:</strong> {{ $weightFillPercentW }}%</li>
                                </ul>
                                <hr>
                                <div class="table-responsive-sm mb-3">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Lp.</th>
                                                <th>Opakowanie <br>(EAN / Typ)</th>
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
                                                    <td>{{ number_format($package['volume_used'] / 1000000, 4) }} m³</td>
                                                    <td>{{ number_format($package['weight_used'], 2) }} kg</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                @if ($reducedVolumeCount > 0)
                    <div class="alert alert-danger mt-4">
                            <h5><strong>Produkty przycięte (ze względu na wystawanie poza opakowanie)</strong></h5>

                        <div class="card-body">
                            <p>Poniższe produkty przekraczały wymiary dostępnych opakowań – ich wymiary zostały dopasowane do maksymalnych dopuszczalnych wartości w celu poprawnego przeliczenia objętości.</p>

                            <ul class="mb-4">
                                <li><strong>Liczba produktów z wystawaniem:</strong> {{ $reducedVolumeCount }}</li>
                                <li><strong>Zaoszczędzona objętość:</strong> {{ number_format($reducedVolumeAmount, 4) }} m³</li>
                            </ul>

                            @if (count($trimmedProducts) > 0)
                                <div class="table-responsive-sm">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produkt</th>
                                                <th>Wymiary oryginalne (cm)</th>
                                                <th>Wymiary po przycięciu (cm)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($trimmedProducts as $trim)
                                                <tr>
                                                    <td>{{ $trim['code'] }}</td>
                                                    <td>{{ $trim['original']['x'] }} × {{ $trim['original']['y'] }} × {{ $trim['original']['z'] }}</td>
                                                    <td>{{ $trim['trimmed']['x'] }} × {{ $trim['trimmed']['y'] }} × {{ $trim['trimmed']['z'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="alert alert-warning mt-4">

                        <h5><strong>Porównanie wypełnienia opakowań</strong></h5>

                    <div class="card-body">
                        <canvas id="packingChart" width="400" height="180"></canvas>
                    </div>
                </div>
                <form method="POST" action="{{ route('orderdetail.storeConfirmedPacking', $order->id) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Wybierz algorytm do zapisania:</label><br>
                        <input type="radio" name="algorithm_choice" value="volume" id="volume" required>
                        <label for="volume">Algorytm objętościowy</label><br>

                        <input type="radio" name="algorithm_choice" value="weight" id="weight">
                        <label for="weight">Algorytm wagowy</label>
                    </div>

                    <input type="hidden" name="volume_algorithm" value="{{ json_encode($volumeAlgorithm) }}">
                    <input type="hidden" name="weight_algorithm" value="{{ json_encode($weightAlgorithm) }}">


                    <div class="d-flex justify-content-end gap-2 mt-4">
                <div style="width: 200px">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary w-100">Powrót</a>
                </div>
                <div style="width: 200px">
                    <form action="{{ route('orders.confirm', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">Zatwierdź kompletację</button>
                    </form>
                </div>
            </div>
                </form>



        </div>
    </div>
</div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('packingChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Objętość (%)', 'Waga (%)'],
            datasets: [
                {
                    label: 'Algorytm objętościowy',
                    data: [{{ $volumeFillPercent }}, {{ $weightFillPercent }}],
                    backgroundColor: '#0d6efd',
                },
                {
                    label: 'Algorytm wagowy',
                    data: [{{ $volumeFillPercentW }}, {{ $weightFillPercentW }}],
                    backgroundColor: '#198754',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Wypełnienie opakowań w %' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: '%' }
                }
            }
        }
    });
});
</script>
