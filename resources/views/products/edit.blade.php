@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Edytuj produkt</b>
                </div>
                    <form action="{{ route('products.update',['product' => $product]) }}" class="forms-sample" method="POST">
                        @csrf
                        @method('put')
                        {{--szablon Products--}}
                        <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
                            <div class="row p-1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Nazwa</label>
                                        <input type="text" name="code"
                                            value="{{$product->code}}" class="form-control @error('code') is-invalid @enderror"
                                            id="code" required readonly>

                                            @error('code')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                    <div class="col-md-12 pt-2">
                                        <div class="form-group">
                                            <label for="producttype_id">Typ produktu</label>
                                                <select name="producttype_id" class="form-select form-control @error('producttype_id') is-invalid @enderror" id="producttype_id" required>
                                                <option value="">Wybierz</option>
                                                @foreach ($product_types as $product_type)
                                                    <option value="{{ $product_type->id }}" @if (isset($product))
                                                        {{ $product->producttype_id == $product_type->id ? 'selected' : old($product->producttype_id) }}
                                                @endif>
                                                {{ $product_type->code }}</option>
                                                @endforeach
                                                @error('producttype_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="longdesc">Opis</label>
                                        <textarea type="text" name="longdesc" class="form-control @error('longdesc') is-invalid @enderror" id="longdesc" rows="4" required autofocus>{{$product->longdesc}}</textarea>

                                            @error('longdesc')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2 pt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="size_x">Długość (cm)</label>
                                        <input type="text" name="size_x"
                                            value="{{$product->size_x}}" class="form-control @error('size_x') is-invalid @enderror" id="size_x" required>

                                            @error('size_x')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="size_y">Szerokość (cm)</label>
                                        <input type="text" name="size_y"
                                            value="{{$product->size_y}}" class="form-control @error('size_y') is-invalid @enderror" id="size_y" required>

                                            @error('size_y')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="size_z">Wysokość (cm)</label>
                                        <input type="text" name="size_z"
                                            value="{{$product->size_z}}" class="form-control @error('size_z') is-invalid @enderror" id="size_z" required>

                                            @error('size_z')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row p-2 pt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight">Waga (kg)</label>
                                        <input type="text" name="weight"
                                            value="{{$product->weight}}" class="form-control @error('weight') is-invalid @enderror" id="weight" required>

                                            @error('weight')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ean">Kod EAN</label>
                                        <input type="text" name="ean"
                                            value="{{$product->ean}}" class="form-control @error('ean') is-invalid @enderror" id="ean" required>

                                            @error('ean')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="metric_id">Jednostka</label>
                                            <select name="metric_id" class="form-select form-control @error('metric_id') is-invalid @enderror" id="metric_id" required>
                                                <option value="">Wybierz</option>
                                                @foreach ($product_metrics as $product_metric)
                                                    <option value="{{ $product_metric->id }}" @if (isset($product))
                                                        {{ $product->metric_id == $product_metric->id ? 'selected' : '' }}
                                                @endif>
                                                {{ $product_metric->code }}</option>
                                                @endforeach


                                                    @error('metric_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror

                                                </select>
                                    </div>
                                </div>
                                <div class="row p-4">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label" for="shipment">Blokada dostawy</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="shipment" name="shipment" @checked(old('shipment', $product->shipment))>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label" for="delivery">Blokada wydania</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="delivery" name="delivery" @checked(old('delivery', $product->delivery))>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-1">
                                <div class="col-md-6 p-3">
                                    <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
                                    <a href="{{ route('products.index') }}" class="btn btn-light">Anuluj</a>
                                </div>
                                <div class="col-md-6 p-3">
                                    <a href="#"  data-coreui-toggle="modal" data-coreui-target="#delete{{ $product->id }}"
                                        class="btn btn-danger float-end text-white px-4 ">Usuń
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    @include("products.delete")
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
