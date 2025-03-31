@extends('layouts.app')
@section('content')

<form action="{{ route('locations.storemulti') }}" class="forms-sample" method="POST">
    @csrf
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Wzór etykiety do wygenerowania</b>
                </div>


                    <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>

                        <div class="row p-1 pt-0 justify-content-center text-center">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="storearea_id"><b>Rząd</b></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="storearea_id"><b>Regał</b></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="storearea_id"><b>Piętro</b></label>
                                </div>
                            </div>
                        </div>

                        <div class="row p-1 justify-content-center">
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Ilość znaków</label>
                                    <input type="number" class="form-control text-center" name="w1" required>
                                </div>
                            </div>
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Ilość znaków</label>
                                    <input type="number" class="form-control text-center" name="w2" required>
                                </div>
                            </div>
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Ilość znaków</label>
                                    <input type="number" class="form-control text-center" name="w3" required>
                                </div>
                            </div>
                        </div>

                        <div class="row p-1 justify-content-center">
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Liczba od</label>
                                    <input type="number" class="form-control text-center" name="od_1" required>
                                </div>
                            </div>
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Liczba od</label>
                                    <input type="number" class="form-control text-center" name="od_2" required>
                                </div>
                            </div>
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Liczba od</label>
                                    <input type="number" class="form-control text-center" name="od_3" required>
                                </div>
                            </div>
                        </div>

                        <div class="row p-1 justify-content-center">
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Liczba do</label>
                                    <input type="number" class="form-control text-center" name="do_1" required>
                                </div>
                            </div>
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Liczba do</label>
                                    <input type="number" class="form-control text-center" name="do_2" required>
                                </div>
                            </div>
                            <div class="col-md-3 pt-2">
                                <div class="form-group">
                                    <label for="storearea_id">Liczba do</label>
                                    <input type="number" class="form-control text-center" name="do_3" required>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
</div>
</div>




<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Dane podstawowe</b>
                </div>

                    <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>

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
                            <div class="row p-2 pt-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="size_x">Wysokość (cm)</label>
                                        <input type="text" name="size_x"
                                            value="{{ isset($location) ? $location->size_x : old('size_x') }}" class="form-control @error('size_x') is-invalid @enderror" id="size_x" required>

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
                                        <input type="text" name="size_y"
                                            value="{{ isset($location) ? $location->size_y : old('size_y') }}" class="form-control @error('size_y') is-invalid @enderror" id="size_y" required>

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
                                        <input type="text" name="size_z"
                                            value="{{ isset($location) ? $location->size_z : old('size_z') }}" class="form-control @error('size_z') is-invalid @enderror" id="size_z" required>

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
                                        <input type="text" name="loadwgt"
                                            value="{{ isset($location) ? $location->loadwgt : old('loadwgt') }}" class="form-control @error('loadwgt') is-invalid @enderror" id="loadwgt" required>

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


                    @push('scripts')
                        <script src="{{ asset("plugins/select2/dist/js/select2.min.js")}}"></script>
                        <script>
                            $(document).ready(function() {
                            $('.select_2').select2();
                            });
                        </script>
                    @endpush

                    {{--

                    --}}

            </div>
        </div>
    </div>

</div>
</form>

@endsection
