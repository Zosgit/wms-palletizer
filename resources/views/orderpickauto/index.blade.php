@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Kompletacja zamówień
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="ship_nr">Nr dostawy</option>
                            <option value="external_nr">Dok zewnętrzny</option>
                            <option value="location">Miejsce</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź dane..."/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Filtruj</button>
                    </div>
                </div>
            </form>
            <table id="data_table" class="table table-striped table-hover table-sm">
                <thead>
                    <tr>
                        <th>Dokument wydania</th>
                        <th>Data</th>
                        <th> </th>
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

        </div>
</div>
@endsection
