@extends('layouts.app')
@section('content')
<a class="big" href="{{ route('move.product')}}"><svg class="icon">
    <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
  </svg>&nbsp;Powrót</a>
    <div class="card mb-4">
        <div class="card-header">
            <b>Wybrany produkt</b>
        </div>

        <div class="card-body">
            @if (isset($stock))
            <div class="row">
                <div>Indeks: <strong>{{ $stock->prod_code }}</strong></div>
                <div>Nr seryjny: <strong>{{ $stock->serial_nr }}</strong></div>
                <div>Termin: <strong>{{ $stock->expiration_at }}</strong></div>
                <div>Magazyn: <strong>{{ $stock->logical_area->code }}</strong></div>
                <div>Uwagi: <strong>{{ $stock->remarks }}</strong></div>
                <div>Właściciel: <strong>{{ $stock->owner->code }}</strong></div>
                <div>Ilość: <strong>{{ $stock->quantity }}</strong></div>

            </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
    <div class="card-header">
        <b>Dane opakowania</b>
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
                <div class="row">
                    <div>Opakowanie: <strong>{{ $storeunit->ean }}</strong></div>
                    <div>Rodzaj: <strong>{{ $storeunit->code_unit_type }}</strong></div>
                    <div>Lokalizacja: <strong>{{ $storeunit->ean_loc }}</strong></div>

                    @if ($storeunit->code_status == 'Dostępna')
                        <div class="mb-4">Status: <strong class="text-success">{{ $storeunit->code_status }}</strong></div>
                    @else
                        <div>Status: <strong class="text-warning">{{ $storeunit->code_status }}</strong></div>
                    @endif



                    @if(isset($stocks))
                        <div class="container">
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    Zawartość opakowania
                                </div>
                                <div class="card-body">
                                <div class="table-responsive-sm">
                                    <table class="table table-striped table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Indeks</th>
                                            <th>Nr seryjny</th>
                                            <th>Termin</th>
                                            <th>Magazyn</th>
                                            <th>Uwagi</th>
                                            <th>Właściciel</th>
                                            <th class="center">Ilość</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stocks as $stock2)
                                            <tr>
                                                <td>{{ $stock2->prod_code}}</td>
                                                <td>{{ $stock2->serial_nr }}</td>
                                                <td>{{ $stock2->expiration_at }}</td>
                                                <td>{{ $stock2->logical_area->code }}</td>
                                                <td>{{ $stock2->remarks }}</td>
                                                <td>{{ $stock2->owner->code}}</td>
                                                <td>{{ $stock2->quantity }}</td>

                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>


                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                        <div class="card">
                            <div class="card-header">
                              <strong> Potwierdzenie operacji </strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('move.saveproduct') }}" class="forms-sample" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div  class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" id='hidden_stock' name='hidden_stock' value="{{ $stock->id }}"/>
                                                <input type="hidden" id='hidden_store_unit' name='hidden_store_unit' value="{{ $storeunit->id}}"/>
                                                <input type="number" Step=".01" name="quantity_new" max="{{ $stock->quantity  }}"
                                                    value="" class="form-control @error('quantity_new') is-invalid @enderror" id="quantity_new" placeholder="Wprowadź ilość"required>

                                                    @error('quantity_new')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                            </div>
                                            <div class="col-md-5">
                                                <button type="submit" class="btn btn-success">Potwierdzam</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </div>
                    @endif

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
@push('scripts')
<script>

    $('#quantity_new').on('change', function(){
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
</script>
@endpush
