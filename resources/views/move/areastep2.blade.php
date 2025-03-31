@extends('layouts.app')
@section('content')
<a class="big" href="{{ route('move.area')}}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Powrót</a>
    <div class="card mb-4">
        <div class="card-header">
            <b>Wybrany produkt - przesunięcie logiczne</b>
        </div>

        <div class="card-body">
            @if (isset($stock))
            <div class="row mb-4">
                <div>Indeks: <strong>{{ $stock->prod_code }}</strong></div>
                <div>Nr seryjny: <strong>{{ $stock->serial_nr }}</strong></div>
                <div>Termin: <strong>{{ $stock->expiration_at }}</strong></div>
                <div>Magazyn: <strong>{{ $stock->logical_area->code.' - '.$stock->logical_area->longdesc }}</strong></div>
                <div>Uwagi: <strong>{{ $stock->remarks }}</strong></div>
                <div>Właściciel: <strong>{{ $stock->owner->code }}</strong></div>
                <div>Ilość: <strong>{{ $stock->quantity }}</strong></div>

            </div>
            <div class="container">
                <div class="card">
                    <div class="card-header">
                      <strong> Potwierdzenie operacji </strong>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('move.savearea') }}" class="forms-sample" method="POST">
                            @csrf
                            <input type="hidden" id='hidden_stock' name='hidden_stock' value="{{ $stock->id }}"/>
                            <div class="row p-2 mt-0">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="logical_area_id" class="select2-container form-control @error('logical_area_id') is-invalid @enderror"  id="logicalarea_id" required>
                                            <option value="">Wybierz magazyn logiczny</option>
                                            @foreach ($logicalareas as $logicalarea)
                                                <option value="{{ $logicalarea->id }}" @selected(old('logical_area_id') == $logicalarea)>
                                                    {{ '['.$logicalarea->code.'] - '.$logicalarea->longdesc }}
                                                </option>
                                            @endForeach
                                            @error('logical_area_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" Step=".01" name="quantity_new" max="{{ $stock->quantity  }}"
                                        value="" class="form-control @error('quantity_new') is-invalid @enderror" id="quantity_new" placeholder="Wprowadź ilość"required>

                                        @error('quantity_new')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>

                                <div  class="row p-2 mt-0">
                                    <div class="col-md-6">
                                        <input type="text" name="notes" max="{{ $stock->quantity  }}"
                                            value="" class="form-control @error('notes') is-invalid @enderror" id="notes" placeholder="Podaj powód zmiany" required>

                                            @error('notes')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div  class="row p-2 mt-0">
                                    <div class="col-md-5">
                                        <button type="submit" class="btn btn-success">Potwierdzam</button>
                                    </div>
                                </div>

                        </form>
                    </div>
                </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('scripts')
<script>

    $('#quantity_new').on('change', function(){
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
</script>
@endpush
