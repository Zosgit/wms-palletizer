@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Nowa dostawa</b>
                </div>
                <form action="{{ route('shipments.store') }}" class="forms-sample" method="POST">
                    @csrf

                    {{-- dostawa na magazyn --}}
                    <div class="card-body"><canvas id="newshipment" width="100%" height="10"></canvas>
                        <div class="row p-1">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Nazwa</label>
                                    <input type="text" name="code"
                                        value="{{ isset($product) ? $product->code :  old('code') }}" class="form-control @error('code') is-invalid @enderror"
                                         id="code" required autofocus {{ isset($product) ? 'readonly':'' }}>

                                        @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                                <div class="form-group">
                                    <label for="code">Nazwa</label>
                                    <input type="text" name="code"
                                        value="{{ isset($product) ? $product->code :  old('code') }}" class="form-control @error('code') is-invalid @enderror"
                                         id="code" required autofocus {{ isset($product) ? 'readonly':'' }}>

                                        @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>

                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="longdesc">Komentarz</label>
                                    <textarea type="text" name="longdesc" class="form-control @error('longdesc') is-invalid @enderror" id="longdesc" rows="4" required>{{ old('longdesc') }}</textarea>

                                        @error('longdesc')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row p-2 pt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firm_id">Dostawca</label>
                                    <select name="firm_id" class="select2 select2-container form-control @error('firm_id') is-invalid @enderror"  id="firm_id" required>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_id">Właściciel</label>
                                    <select name="owner_id" class="select2 form-select form-control @error('owner_id') is-invalid @enderror" id="owner_id" required>
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
                            $('.select2').select2({

                            });

                            });
                        </script>
                    @endpush
            </div>
        </div>
    </div>

</div>
@endsection
