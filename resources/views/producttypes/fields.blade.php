<form action="{{ route('producttypes.store') }}" class="forms-sample" method="POST">
    @csrf
 {{--szablon ProductTypes--}}
 <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="row p-2 px-3">
        <div class="col-md-7">
            <div class="form-group">
                <label for="code">Typ produktu</label>
                <input type="text" name="code"
                    value="{{ isset($type) ? $type->code : '' }}" class="form-control @error('code') is-invalid @enderror" id="code" />

                    @if (isset($type))
                        <input type="hidden" name="type_id" value="{{ $type->id }}">
                    @endif


                    @error('code')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
    </div>


    <div class="col-md-6 p-3">
        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
        <a href="{{ route('producttypes.index') }}"
                class="btn btn-light">Anuluj</a>
    </div>
</div>
</form>
