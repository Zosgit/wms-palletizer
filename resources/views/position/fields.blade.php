
    @csrf

    <div class="card-body">
        <div class="row p-1">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="name">Nazwa pozycjonowania</label>
                    <input type="text" name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name" required autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                @if(isset($id))
    <input type="hidden" name="picklist_id" value="{{ $id }}">
@endif


                <div class="form-group mb-3">
                    <label for="storeunit_id">Wybierz opakowanie</label>
                    <select name="storeunit_id" class="form-select form-control @error('storeunit_id') is-invalid @enderror" id="storeunit_id" required>
                        <option value="">Wybierz</option>
                        @foreach ($storeunits as $storeunit)
                            <option value="{{ $storeunit->id }}" {{ old('storeunit_id') == $storeunit->id ? 'selected' : '' }}>
                                {{ $storeunit->code }} – {{ $storeunit->loadwgt }} kg, {{ $storeunit->size_z }} cm
                            </option>
                        @endforeach
                    </select>
                    @error('storeunit_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

            </div>

            <div class="col-md-6">
                <label>Produkty do zapakowania</label>
                <div class="overflow-auto border rounded p-2" style="max-height: 300px;">
                    @foreach ($products as $product)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                   id="product_{{ $product->id }}"
                                   {{ (is_array(old('product_ids')) && in_array($product->id, old('product_ids'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="product_{{ $product->id }}">
                                {{ $product->name }} – {{ $product->weight }} kg, {{ $product->size_x }}x{{ $product->size_y }}x{{ $product->size_z }} cm
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('product_ids')
                    <span class="text-danger" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div>

        <div class="row p-3">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary mr-4">Zapisz pozycjonowanie</button>
                <a href="{{ route('position.index') }}" class="btn btn-light">Anuluj</a>
            </div>
        </div>
    </div>


@push('scripts')
    <script>
        $('.number').on('change', function(){
            $(this).val(parseFloat($(this).val()).toFixed(2));
        });
    </script>
@endpush
