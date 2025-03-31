@extends('layouts.app')
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Przesunięcie opakowania</b>
        </div>

        <div class="card-body">
            <form action="" method="GET">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź opakowanie" required/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Szukaj opakowania</button>
                    </div>
                </div>
            </form>
            @if (isset($storeunit->id))
                <div class="row mb-4">
                    <div>Opakowanie: <strong>{{ $storeunit->ean }}</strong></div>
                    <div>Rodzaj: <strong>{{ $storeunit->code_unit_type }}</strong></div>
                    <div>Lokalizacja: <strong>{{ $storeunit->ean_loc }}</strong></div>

                    @if ($storeunit->code_status == 'Dostępna')
                        <div class="mb-4">Status: <strong class="text-success">{{ $storeunit->code_status }}</strong></div>
                    @else
                        <div>Status: <strong class="text-warning">{{ $storeunit->code_status }}</strong></div>
                    @endif

                    <div class="row mb-4">
                        <a href="{{ route('move.su2',['id' => $storeunit->id]) }}"
                            class="btn btn-primary float-start px-4 btn-sm">Wyszukaj miejsce >></a>
                    </div>

                </div>
            @else
                @if($start <> '')
                <div class="row mb-4">
                    <div class="text-danger"><strong>Nie znalazłem opakowania !</strong></div>
                </div>
                @endif
            @endif
        </div>



    </div>

@endsection
