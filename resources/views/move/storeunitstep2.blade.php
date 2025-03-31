@extends('layouts.app')
@section('content')
<a class="big" href="{{ route('move.su')}}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Powrót</a>
    <div class="card mb-4">
        <div class="card-header">
            <b>Przesunięcie opakowania - {{ $storeunit->ean }}</b>
        </div>

        <div class="card-body">
            <form action="" method="GET">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź miejsce" required/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Szukaj miejsce</button>
                    </div>
                </div>
            </form>
            @if (isset($location->id))
                <div class="row mb-4">
                    <div>Miejsce: <strong>{{ $location->ean }}</strong></div>
                    <div>Strefa: <strong>{{ $location->code_store_area}}</strong></div>

                    @if ($location->code_status == 'Wolna')
                        <div class="mb-4">Status: <strong class="text-success">{{ $location->code_status }}</strong></div>
                        <div class="row mb-4">
                            <form action="{{ route('move.savestoreunit') }}" class="forms-sample" method="POST">
                                @csrf
                                <input type="hidden" id='hidden_su' name='hidden_su' value="{{ $storeunit->id }}"/>
                                <input type="hidden" id='hidden_loc' name='hidden_loc'value="{{ $location->id }}"/>
                                <button type="submit" class="btn btn-primary mr-4">Potwierdzam przesunięcie</button>
                            </form>
                        </div>
                    @else
                        <div>Status: <strong class="text-warning">{{ $location->code_status }}</strong></div>
                    @endif

                </div>
            @else
                @if($start <> '')
                <div class="row mb-4">
                    <div class="text-danger"><strong>Nie znalazłem miejsca !</strong></div>
                </div>
                @endif
            @endif
        </div>



    </div>

@endsection
