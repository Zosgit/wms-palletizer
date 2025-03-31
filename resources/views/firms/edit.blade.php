@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Dodaj nowego kontrahenta</b>
                </div>
                    <form action="{{ route('firms.update',['firm' => $firm]) }}" class="forms-sample" method="POST">
                        @csrf
                        @method('put')
                        {{--szablon Firms--}}
                        <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
                            <div class="row p-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Nazwa firmy</label>
                                        <input type="text" name="code"
                                            value="{{$firm->code}}" class="form-control @error('code') is-invalid @enderror" id="code" required readonly>

                                            @if (isset($firm))
                                                <input type="hidden" name="firm_id" value="{{ $firm->id }}">
                                            @endif

                                            @error('code')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax">NIP</label>
                                        <input type="text" name="tax"
                                            value="{{$firm->tax}}" class="form-control @error('tax') is-invalid @enderror" id="tax" required autofocus>

                                                @error('tax')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="longdesc">Pełna nazwa firmy</label>
                                        <textarea type="text" name="longdesc" class="form-control @error('longdesc') is-invalid @enderror" id="longdesc" rows="4" required>{{$firm->longdesc}}</textarea>

                                            @error('longdesc')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notes">Uwagi</label>
                                        <textarea type="text" name="notes" class="form-control @error('notes') is-invalid @enderror" id="notes" rows="4" required>{{$firm->notes}}</textarea>

                                            @error('notes')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="street">Ulica</label>
                                        <input type="text" name="street"
                                            value="{{$firm->street}}" class="form-control @error('street') is-invalid @enderror" id="street" />

                                            @error('street')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="postcode">Kod pocztowy</label>
                                        <input type="text" name="postcode"
                                            value="{{$firm->postcode}}" class="form-control @error('postcode') is-invalid @enderror" id="postcode" placeholder="123456" required>

                                                @error('postcode')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">Miasto</label>
                                        <input type="text" name="city"
                                            value="{{$firm->city}}" class="form-control @error('city') is-invalid @enderror" id="city" required>

                                            @error('city')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row p-4">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <label class="form-check-label" for="shipment">Dostawca</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="shipment" name="shipment" @checked(old('shipment', $firm->shipment))>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <label class="form-check-label" for="delivery">Odbiorca</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="delivery" name="delivery" @checked(old('delivery', $firm->delivery))>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <label class="form-check-label" for="owner">Właściciel</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="owner" name="owner" @checked(old('owner', $firm->owner))>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-1">
                                <div class="col-md-6 p-3">
                                    <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
                                    <a href="{{ route('firms.index') }}" class="btn btn-light">Anuluj</a>
                                </div>
                                <div class="col-md-6 p-3">
                                    <a href="#"  data-coreui-toggle="modal" data-coreui-target="#delete{{ $firm->id }}"
                                        class="btn btn-danger float-end text-white px-4 ">Usuń
                                    </a>
                                </div>
                            </div>
                        </div>
                        </form>
                        @include("firms.delete")
                    @push('scripts')
                        <script src="{{ asset("plugins/select2/dist/js/select2.min.js")}}"></script>
                        <script>
                            $(document).ready(function() {
                            $('.select_2').select2();
                            });
                        </script>
                    @endpush
            </div>
        </div>
    </div>

</div>
@endsection
