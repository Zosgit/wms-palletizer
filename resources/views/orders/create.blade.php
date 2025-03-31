@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Nowe zamówienie</b>
                </div>
                <form action="{{ route('orders.store') }}" class="forms-sample" method="POST">
                    @csrf

                    <div class="card-body"><canvas id="myshipment" width="100%" height="10"></canvas>
                        <div class="row p-1">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Nr dokumentu:</label></br>
                                    <strong>AUTONUMER</strong>
                                </div>
                                <div class="col-md-12 pt-2">
                                    <div class="form-group">
                                        <label for="code">Dokument wydania</label>
                                        <input type="text" name="external_nr"
                                            value="" class="form-control @error('external_nr') is-invalid @enderror"
                                             id="external_nr" required autofocus>

                                            @error('external_nr')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="remarks">Komentarz</label>
                                    <textarea type="text" name="remarks" class="form-control @error('remarks') is-invalid @enderror" id="remarks" rows="4">{{ old('remarks') }}</textarea>

                                        @error('remarks')
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
                                    <label for="firm_id">Odbiorca</label>
                                    <select name="firm_id" class="select_2 select2-container form-control @error('firm_id') is-invalid @enderror"  id="firm_id" required>
                                        <option value="">Wybierz</option>
                                        @foreach ($firms as $firm)
                                            <option value="{{ $firm->id }}" @selected(old('firm_id') == $firm)>
                                                {{ $firm->code }}
                                            </option>
                                        @endForeach
                                        @error('firm_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="owner_id">Właściciel</label>
                                    <select name="owner_id" class="select_2 form-select form-control @error('owner_id') is-invalid @enderror" id="owner_id" required>
                                        <option value="">Wybierz</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}" @selected(old('owner_id') == $owner)>
                                                {{ $owner->code }}
                                            </option>
                                        @endForeach
                                        @error('owner_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location_id">Miejsce</label>
                                    <select name="location_id" class="select_2 form-select form-control @error('location_id') is-invalid @enderror" id="location_id" required>
                                        <option value="">Wybierz</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" @selected(old('location_id') == $location)>
                                                {{ $location->ean }}
                                            </option>
                                        @endForeach
                                        @error('location_id')
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
                            <a href="{{ route('orders.index') }}"
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
                        </script>
                    @endpush

            </div>
        </div>
    </div>

</div>
@endsection
