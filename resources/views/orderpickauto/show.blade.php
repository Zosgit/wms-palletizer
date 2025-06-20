@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Produkty w zamówieniu: {{ $order->name ?? 'Zamówienie #' . $order->id }}</h2>

<form method="POST" action="#">
    @csrf

    <table class="table">
        <thead>
            <tr>
                <th>✔</th>
                <th>Produkt</th>
                <th>Ilość</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->order_details as $detail)
    <tr>
        <td><input type="checkbox" name="products[]" value="{{ $detail->product->id }}"></td>
        <td>{{ $detail->product->code }}</td>
        <td>{{ $detail->quantity }}</td>
    </tr>
@endforeach

        </tbody>
    </table>
<a href="{{ route('orderpickauto.index') }}" class="btn btn-secondary">Powrót</a>
    <button class="btn btn-primary">Zapisz</button>
</form>

</div>
@endsection
