<form action="{{ route('locations.store') }}" class="forms-sample" method="POST">
@csrf

<div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="row p-2 pt-2">
        <div class="col-md-3">
            <div class="form-group">
                <label for="ean">Nazwa - EAN</label>
                <input type="text" name="ean"
                    value="{{ isset($location) ? $location->ean : old('ean') }}" class="form-control @error('ean') is-invalid @enderror" id="ean" required>

                    @error('ean')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pos_x">Rząd</label>
                <input type="number" name="pos_x"
                    value="{{ isset($location) ? $location->pos_x : old('pos_x') }}" class="form-control @error('pos_x') is-invalid @enderror" id="pos_x" required>

                    @error('pos_x')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pos_y">Regał</label>
                <input type="number" name="pos_y"
                    value="{{ isset($location) ? $location->size_y : old('pos_y') }}" class="form-control @error('pos_y') is-invalid @enderror" id="pos_y" required>

                    @error('pos_y')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="pos_z">Piętro</label>
                <input type="number" name="pos_z"
                    value="{{ isset($location) ? $location->pos_z : old('pos_z') }}" class="form-control @error('pos_z') is-invalid @enderror" id="pos_z" required>

                    @error('pos_z')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
        </div>
    </div>
    <div class="row p-2 pt-2">
        <div class="col-md-6">
            <div class="form-group">
                <label for="storearea_id">Strefa magazynu</label>
                    <select name="storearea_id" class="form-select form-control @error('storearea_id') is-invalid @enderror" id="storearea_id" required>
                        <option value="">Wybierz</option>
                        @foreach ($store_areas as $store_area)
                            <option value="{{ $store_area->id }}" @if (isset($location))
                                {{ $location->storearea_id == $store_areas->id ? 'selected' : '' }}
                        @endif>
                        {{ $store_area->longdesc }}</option>
                        @endforeach

                        @error('storearea_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="status_id">Status</label>
                    <select name="status_id" class="form-select form-control @error('status_id') is-invalid @enderror" id="status_id" required>
                        <option value="">Wybierz</option>
                        @foreach ($status as $stat)
                            <option value="{{ $stat->id }}" @if (isset($location))
                                {{ $location->status_id == $status->id ? 'selected' : '' }}
                        @endif>
                        {{ $stat->code }}</option>
                        @endforeach

                        @error('status_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </select>
            </div>
        </div>
    </div>
        <div class="row p-2 pt-2">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="size_x">Wysokość (cm)</label>
                    <input type="number" Step=".01" name="size_x"
                        value="{{ isset($location) ? $location->size_x : old('size_x') }}" class="number form-control @error('size_x') is-invalid @enderror" id="size_x" required>

                        @error('size_x')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="size_y">Szerokość (cm)</label>
                    <input type="number" name="size_y"
                        value="{{ isset($location) ? $location->size_y : old('size_y') }}" class="number form-control @error('size_y') is-invalid @enderror" id="size_y" required>

                        @error('size_y')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="size_z">Głębokość (cm)</label>
                    <input type="number" name="size_z"
                        value="{{ isset($location) ? $location->size_z : old('size_z') }}" class="number form-control @error('size_z') is-invalid @enderror" id="size_z" required>

                        @error('size_z')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="loadwgt">Maks. obciążenie (kg)</label>
                    <input type="number" name="loadwgt"
                        value="{{ isset($location) ? $location->loadwgt : old('loadwgt') }}" class="number form-control @error('loadwgt') is-invalid @enderror" id="loadwgt" required>

                        @error('loadwgt')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
            </div>
        </div>


    <div class="col-md-6 p-3">
        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
        <a href="{{ route('locations.index') }}"
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

{{--
<div class="card p-3">
        <div class="row p-1 text-center">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="storearea_id">Strefa</label>
                    <input type="text" class="form-control text-center" value="XX" readonly>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="storearea_id">Rząd</label>
                    <input type="text" class="form-control text-center" value="XX" readonly>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="storearea_id">Regał</label>
                    <input type="text" class="form-control text-center" value="XX" readonly>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="storearea_id">Piętro</label>
                    <input type="text" class="form-control text-center" value="XX" readonly>
                </div>
            </div>
        </div>
    </div>
--}}
