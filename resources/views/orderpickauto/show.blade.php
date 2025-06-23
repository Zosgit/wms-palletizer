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
                <div class="col-md-6">
                    <h6 class="mb-3">Informacje o zamówieniu:</h6>
                    <div><strong>Dokument:</strong> {{ $order->external_nr }}</div>
                    <div><strong>Data utworzenia:</strong> {{ $order->created_at }}</div>
                    <div><strong>Status:</strong> {{ $order->status->code }}</div>
                    <div><strong>Uwagi:</strong> {{ $order->remarks }}</div>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Odbiorca:</h6>
                    <div><strong>{{ $order->firm->code }}</strong></div>
                    <div>{{ $order->firm->longdesc }}</div>
                    <div>{{ $order->firm->postcode }} {{ $order->firm->city }}</div>
                </div>
            </div>

            <hr>
            <h5 class="mb-3">Szczegóły kompletacji</h5>

            @foreach($entries as $i => $entry)
                @php
                    $storeunit = $entry->storeunit;
                    $type = $storeunit?->storeunittype?->code ?? 'brak';
                @endphp

                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <strong>Opakowanie {{ $i + 1 }}</strong>: {{ $storeunit->ean ?? 'brak' }} / {{ $type }}<br>
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
                                    @foreach ($productsByEntry[$entry->id] as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product->product->code ?? 'brak' }}</td>
                                            <td>{{ $product->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection
