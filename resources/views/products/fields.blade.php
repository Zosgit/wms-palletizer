<form action="{{ route('products.store') }}" class="forms-sample" method="POST">
    @csrf

    {{--szablon Products--}}
    <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
        <div class="row p-1">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="code">Nazwa</label>
                    <input type="text" name="code"
                        value="{{ isset($product) ? $product->code : old('code') }}"
                        class="form-control @error('code') is-invalid @enderror"
                        id="code" required autofocus {{ isset($product) ? 'readonly' : '' }}>
                    @error('code')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="producttype_id">Typ produktu</label>
                    <select name="producttype_id" class="form-select form-control @error('producttype_id') is-invalid @enderror" id="producttype_id" required>
                        <option value="">Wybierz</option>
                        @foreach ($product_types as $product_type)
                            <option value="{{ $product_type->id }}"
                                @if (isset($product) && $product->producttype_id == $product_type->id) selected @endif>
                                {{ $product_type->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('producttype_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="material_type">Rodzaj materiału</label>
                    <select name="material_type" class="form-select form-control @error('material_type') is-invalid @enderror" id="material_type" required>
                        <option value="">Wybierz</option>
                        <option value="kruchy" {{ (isset($product) && $product->material_type == 'kruchy') ? 'selected' : '' }}>Kruchy</option>
                        <option value="miekki" {{ (isset($product) && $product->material_type == 'miekki') ? 'selected' : '' }}>Miękki</option>
                        <option value="twardy" {{ (isset($product) && $product->material_type == 'twardy') ? 'selected' : '' }}>Twardy</option>
                    </select>
                    @error('material_type')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group h-100">
                    <label for="longdesc">Opis</label>
                    <textarea name="longdesc"
                              class="form-control @error('longdesc') is-invalid @enderror"
                              id="longdesc"
                              style="height: 200px;"
                              required>{{ isset($product) ? $product->longdesc : old('longdesc') }}</textarea>
                    @error('longdesc')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

        </div>



        <div class="row p-2 pt-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="size_x">Długość (cm)</label>
                    <input type="number" Step=".01" name="size_x"
                        value="{{ isset($product) ? $product->size_x : old('size_x') }}" class="number form-control @error('size_x') is-invalid @enderror" id="size_x" required>

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
                    <input type="number" name="size_y"
                        value="{{ isset($product) ? $product->size_y : old('size_y') }}" class="number form-control @error('size_y') is-invalid @enderror" id="size_y" required>

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
                    <input type="number" name="size_z"
                        value="{{ isset($product) ? $product->size_z : old('size_z') }}" class="number form-control @error('size_z') is-invalid @enderror" id="size_z" required>

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
                    <input type="number" name="weight"
                        value="{{ isset($product) ? $product->weight : old('weight') }}" class="number form-control @error('weight') is-invalid @enderror" id="weight" required>

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
                    <input type="number" name="ean"
                        value="{{ isset($product) ? $product->ean : old('ean') }}" class="form-control @error('ean') is-invalid @enderror" id="ean" required>

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
                                    {{ $product->productmetric_id == $product_metric->id ? 'selected' : '' }}
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
                    <label class="form-label">Czy produkt może wystawać poza opakowanie?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="can_overhang" id="overhang_yes" value="1">
                        <label class="form-check-label" for="overhang_yes">Tak</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="can_overhang" id="overhang_no" value="0">
                        <label class="form-check-label" for="overhang_no">Nie</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                        <label class="form-check-label" for="shipment">Blokada dostawy</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="shipment" name="shipment">
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="delivery">Blokada wydania</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="delivery" name="delivery">
                    </div>
                </div>
            </div>
        </div>

        </div>

        <div class="col-md-6 p-3">
            <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
            <a href="{{ route('products.index') }}"
                    class="btn btn-light">Anuluj</a>
        </div>
    </div>
    </form>
    @push('scripts')
        <script src="{{ asset("plugins/select2/dist/js/select2.min.js")}}"></script>
        <script>
            $(document).ready(function() {
            $('.select_2').select2();
            });

            $('.number').on('change', function(){
                $(this).val(parseFloat($(this).val()).toFixed(2));
            });

        </script>
    @endpush
