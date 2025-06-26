@extends('layouts.app')

@section('title') Podsumowanie kompletacji @endsection

@section('content')
<a class="big" href="{{ route('orderpickauto.index') }}">
    <svg class="icon">
        <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
    </svg>&nbsp;Lista zatwierdzonych kompletacji
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
            @php
    $maxWeight = $entries->max('used_weight');
    $heaviestEntryId = optional($entries->firstWhere('used_weight', $maxWeight))->id;
@endphp

            <h5 class="mb-3">Szczegóły kompletacji</h5>
<div class="row">
    @foreach($entries as $i => $entry)
        @php
            $storeunit = $entry->storeunit;
            $type = $storeunit?->storeunittype?->code ?? 'brak';
        @endphp

        <div class="col-md-6">
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <strong>Opakowanie {{ $i + 1 }}</strong>: {{ $storeunit->ean ?? 'brak' }} / {{ $type }}
                    @if ($entry->id === $heaviestEntryId)
                        <span class="badge bg-warning text-dark ms-2">najcięższy</span>
                    @endif
                    <br>
                    Algorytm: <strong>
                        @if($entry->algorithm_type === 'volume') Objętościowy
                        @elseif($entry->algorithm_type === 'weight') Wagowy
                        @else Nieznany
                        @endif
                    </strong>
                </div>
                <div class="card-body">
                    <ul>
                        <li><strong>Zużyta objętość:</strong> {{ number_format($entry->used_volume / 1000000, 4) }} m³</li>
                        <li><strong>Zużyta waga:</strong> {{ number_format($entry->used_weight, 2) }} kg</li>
                    </ul>

                    <h6>Produkty w opakowaniu:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Lp.</th>
                                    <th>Kod produktu</th>
                                    <th>Ilość (szt.)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($productsByEntry[$entry->id]) && count($productsByEntry[$entry->id]) > 0)
                                    @foreach ($productsByEntry[$entry->id] as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product->product->code ?? 'brak' }}
                                                @if(isset($product->product->material_type) && Str::of($product->product->material_type)->contains('kruchy'))
                                            <span class="badge bg-danger-subtle text-danger ms-1" title="Produkt kruchy">!</span>
                                        @endif
                                            </td>
                                            <td>{{ $product->quantity }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Brak przypisanych produktów.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <a href="{{ route('orderdetail.autopick', ['id' => $order->id]) }}" class="btn btn-warning">
        Powtórz proces
    </a>
<p class="text-danger mt-3">
    <strong>Uwaga:</strong> Kliknięcie przycisku uruchomi <u>nową symulację kompletacji</u>.
    Dane zatwierdzone wcześniej <strong>nie zostaną nadpisane</strong>.
</p>
</div>


@endsection
