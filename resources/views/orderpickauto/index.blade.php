@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Zatwierdzone kompletacje</h1>

    @if($orders->isEmpty())
        <p>Brak zatwierdzonych kompletacji.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID zamówienia</th>
                    <th>Data zatwierdzenia</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $entry)
                    <tr>
                        <td>{{ $entry->order->order_nr ?? 'Brak numeru'  }}</td>
                        <td>
                            {{
                                optional(
                                    \App\Models\OrderPickAuto::where('order_id', $entry->order_id)
                                        ->where('confirmed', true)
                                        ->orderByDesc('created_at')
                                        ->first()
                                )->created_at?->format('Y-m-d H:i') ?? 'Brak daty'
                            }}
                        </td>
                        <td>
                            <a href="{{ route('orderpickauto.show', $entry->order_id) }}" class="btn btn-primary btn-sm">
                                Pokaż szczegóły
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
