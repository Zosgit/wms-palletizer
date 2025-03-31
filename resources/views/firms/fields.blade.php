<form action="{{ route('firms.store') }}" class="forms-sample" method="POST">
    @csrf

 {{--szablon Firms--}}
 <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="row p-2">
        <div class="col-md-6">
            <div class="form-group">
                <label for="code">Nazwa firmy</label>
                <input type="text" name="code"
                    value="{{ isset($firm) ? $firm->code : old('code') }}" class="form-control @error('code') is-invalid @enderror" id="code" required autofocus>

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
                    value="{{ isset($firm) ? $firm->tax : old('tax') }}" class="form-control @error('tax') is-invalid @enderror" id="tax" required>

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
                <textarea type="text" name="longdesc" value="{{ isset($firm) ? $firm->longdesc : old('longdesc') }}" class="form-control @error('longdesc') is-invalid @enderror" id="longdesc" rows="4" required></textarea>
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
                <textarea type="text" name="notes" value="{{ isset($firm) ? $firm->notes : old('notes') }}" class="form-control @error('notes') is-invalid @enderror" id="notes" rows="4" required></textarea>
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
                    value="{{ isset($firm) ? $firm->street : old('street') }}" class="form-control @error('street') is-invalid @enderror" id="street" required/>
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
                    value="{{ isset($firm) ? $firm->postcode : old('postcode') }}" class="form-control @error('postcode') is-invalid @enderror" id="postcode" required/>
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
                    value="{{ isset($firm) ? $firm->city : old('city') }}" class="form-control @error('city') is-invalid @enderror" id="city" required/>
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
                <input class="form-check-input" type="checkbox" role="switch" id="shipment" name="shipment" >
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch">
                <label class="form-check-label" for="delivery">Odbiorca</label>
                <input class="form-check-input" type="checkbox" role="switch" id="delivery" name="delivery">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check form-switch">
                <label class="form-check-label" for="owner">Właściciel</label>
                <input class="form-check-input" type="checkbox" role="switch" id="owner" name="owner">
            </div>
        </div>
    </div>

    <div class="col-md-6 p-3">
        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
        <a href="{{ route('firms.index') }}"
                    class="btn btn-light">Anuluj</a>
    </div>
</div>

</div>
</form>
