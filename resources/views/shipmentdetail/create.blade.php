@extends('layouts.app')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div>Dostawa nr: <strong>{{ $shipment->ship_nr}}</strong></div>
        </div>
        <div class="card-body">
            <form action="{{ route('shipmentdetail.store',['shipment' => $shipment]) }}" class="forms-sample" method="POST">
                @csrf
                <div class="row p-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="product_id">Wybierz produkt</label>
                            <select name="product_id" class="select_2 select2-container form-control @error('product_id') is-invalid @enderror"  id="product_id" required>
                                <option value="">Wybierz</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('product_id') == $product)>
                                        {{ $product->ean.' - '.$product->code}}
                                    </option>
                                @endForeach
                                @error('product_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="serial_nr">Numer seryjny</label>
                            <input type="text" name="serial_nr"
                                value="" class="form-control @error('serial_nr') is-invalid @enderror" id="serial_nr">

                                @error('serial_nr')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiration_at">Data ważności</label>
                            <input type="date" name="expiration_at"
                                value="" class="form-control @error('expiration_at') is-invalid @enderror" id="expiration_at">
                                @error('expiration_at')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="logical_area_id">Magazyn logiczny</label>
                            <select name="logical_area_id" class="select2-container form-control @error('logical_area_id') is-invalid @enderror"  id="logicalarea_id" required>
                                <option value="">Wybierz</option>
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
                </div>
                <div class="row p-2">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quantity">Ilość</label>
                            <input type="number" Step=".01" name="quantity"
                                value="" class="form-control @error('quantity') is-invalid @enderror" id="quantity" required>

                                @error('quantity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for=" remarks">Komentarz</label>
                            <input type="text" name="remarks"
                                value="" class="form-control @error('remarks') is-invalid @enderror" id="remarks">

                                @error('remarks')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6 p-3">
                    <button type="submit" class="btn btn-primary mr-4">Dopisz</button>
                    <a href="{{ url()->previous() }}"
                            class="btn btn-light">Anuluj</a>
                </div>
            </form>
        @push('scripts')
            <script src="{{ asset("plugins/select2/dist/js/select2.min.js")}}"></script>
            <script>
                $(document).ready(function() {
                $('.select_2').select2();
                });

                $('#quantity').on('change', function(){
                    $(this).val(parseFloat($(this).val()).toFixed(2));
                });
            </script>
        @endpush
        </div>
    </div>
</div>
  @endsection
