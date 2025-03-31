<form action="{{ route('storeunittypes.store') }}" class="forms-sample" method="POST">
    @csrf
{{--szablon StoreUnitTypes--}}
<div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="row p-2 mt-0">
        <div class="col-md-6">
            <div class="form-group">
                <label for="code">Typ opakowania</label>
                <input type="text" name="code"
                    value="{{ isset($storeunittype) ? $storeunittype->code : '' }}" class="form-control @error('code') is-invalid @enderror" id="code" required>

                    @if (isset($storeunittype))
                        <input type="hidden" name="package_id" value="{{ $storeunittype->id }}">
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
                <label for="prefix">Skrót</label>
                <input type="text" name="prefix"
                    value="{{ isset($storeunittype) ? $storeunittype->prefix : '' }}" class="form-control @error('prefix') is-invalid @enderror" id="prefix" required>

                    @error('prefix')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
    </div>

    <div class="row p-2">
        <div class="col-md-4">
            <div class="form-group">
                <label for="size_x">Długość (cm)</label>
                <input type="number" name="size_x"
                    value="{{ isset($storeunittype) ? $storeunittype->size_x : '' }}" class="number form-control @error('size_x') is-invalid @enderror" id="size_x" required>


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
                    value="{{ isset($storeunittype) ? $storeunittype->size_y : '' }}" class="number form-control @error('size_y') is-invalid @enderror" id="size_y" required>


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
                    value="{{ isset($storeunittype) ? $storeunittype->size_z : '' }}" class="number form-control @error('size_z') is-invalid @enderror" id="size_z" required>


                    @error('size_z')
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
                <label for="loadwgt">Waga maks. na opakowaniu (kg)</label>
                <input type="number" name="loadwgt"
                    value="{{ isset($storeunittype) ? $storeunittype->loadwgt : '' }}" class="number form-control @error('loadwgt') is-invalid @enderror" id="loadwgt" required>


                    @error('loadwgt')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="suwgt">Waga opakowania (kg)</label>
                <input type="number" name="suwgt"
                    value="{{ isset($storeunittype) ? $storeunittype->suwgt : '' }}" class="number form-control @error('suwgt') is-invalid @enderror" id="suwgt" required>


                    @error('suwgt')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
    </div>


    <div class="col-md-6 p-3">
        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
        <a href="{{ route('storeunittypes.index') }}"
                    class="btn btn-light">Anuluj</a>
    </div>
</div>
</form>
@push('scripts')
<script>
    $('.number').on('change', function(){
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
</script>
@endpush
