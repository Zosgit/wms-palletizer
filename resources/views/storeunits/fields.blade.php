<form action="{{ route('storeunits.store') }}" class="forms-sample" method="POST">
    @csrf
{{--szablon StoreUnitTypes--}}
<div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="row p-1">
        <div class="col-md-3">
            <div class="form-group">
                <label for="number">Ilość opakowań</label>
                <input type="number" name="number"
                    value="" class="form-control @error('number') is-invalid @enderror"
                     id="number" required autofocus min="1" max="50000">

                    @error('number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-group">
                <label for="storeunittype_id">Typ produktu</label>
                <select name="storeunittype_id" class="form-select form-control @error('storeunittype_id') is-invalid @enderror" id="storeunittype_id" required>
                <option value="">Wybierz</option>
                @foreach ($store_unit_types as $unit_types)
                    <option value="{{ $unit_types->id }}" @if (isset($unit_types))
                        {{ $unit_types->storeunittype_id == $unit_types->id ? 'selected' : '' }}
                @endif>
                {{ $unit_types->code }}</option>
                @endforeach
                @error('storeunittype_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </select>
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
