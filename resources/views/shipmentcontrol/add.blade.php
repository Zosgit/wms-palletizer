@extends('layouts.app')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div>Kontrola produktu: <strong>{{ $shipmentdetail->prod_code}}</strong></div>
        </div>
        <div class="card-body">
            <form action="{{ route('control.store',['id' => $shipmentdetail->id,'loc' => $loc]) }}" class="forms-sample" method="POST">
                @csrf
                <div class="row p-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="su_id">Wybierz Opakowanie</label>
                            <select name="su_id" class="select_2 select2-container form-control @error('su_id') is-invalid @enderror"  id="su_id" required>
                                <option value="">Wybierz</option>
                                @foreach ($storeunits as $su)
                                    <option value="{{ $su->id }}" @selected(old('su_id') == $su)>
                                        {{ $su->ean }}
                                    </option>
                                @endForeach
                                @error('su_id')
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
                                value="{{$shipmentdetail->serial_nr}}" class="form-control @error('serial_nr') is-invalid @enderror" id="serial_nr">

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
                                value="{{$shipmentdetail->expiration_at}}" class="form-control @error('expiration_at') is-invalid @enderror" id="expiration_at">
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
                            <select name="logical_area_id" class="select2-container form-control @error('logical_area_id') is-invalid @enderror"  id="logical_area_id" required>
                                <option value="{{$shipmentdetail->logical_area_id}}"{{ $shipmentdetail->logical_area_id === old('$shipmentdetail->logical_area_id') ? 'Wybierz' : '' }}>
                                    {{ '['.$shipmentdetail->logical_area->code.'] - '.$shipmentdetail->logical_area->longdesc}}</option>
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
                            <label for="quantity_new">Ilość</label>
                            <input type="number" Step=".01" name="quantity_new" max="{{ $shipmentdetail->quantity_control }}"
                                value="{{$shipmentdetail->quantity_control}}" class="form-control @error('quantity_new') is-invalid @enderror" id="quantity_new" required>

                                @error('quantity_new')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="remarks">Komentarz</label>
                            <input type="text" name="remarks"
                                value="{{$shipmentdetail->remarks}}" class="form-control @error('remarks') is-invalid @enderror" id="remarks">

                                @error('remarks')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                </div>
                <div class="row p-1">
                    <div class="col-md-6 p-3">
                        <button type="submit" class="btn btn-primary mr-4">Dopisz</button>
                        <a href="{{ url()->previous() }}"
                                class="btn btn-light">Anuluj</a>
                    </div>
                    <div class="col-md-6 p-3">
                        <a href="#"  data-coreui-toggle="modal" data-coreui-target="#delete{{ $shipmentdetail->id }}"
                            class="btn btn-danger float-end text-white px-4 ">Zakończ
                        </a>
                    </div>
                </div>



            </form>
            @include("shipmentcontrol.close")

            @push('scripts')
                <script src="{{ asset("plugins/select2/dist/js/select2.min.js")}}"></script>
                <script>
                    $(document).ready(function() {
                    $('.select_2').select2();
                    });

                    $('#quantity_new').on('change', function(){
                        $(this).val(parseFloat($(this).val()).toFixed(2));
                    });
                </script>
            @endpush
        </div>
    </div>
</div>
  @endsection
