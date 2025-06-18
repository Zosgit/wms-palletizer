@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Dodaj nowy rodzaj opakowania</b>
                </div>
                <form action="{{ route('storeunittypes.update',['storeunittype' => $storeunittype]) }}" class="forms-sample" method="POST">
                    @csrf
                    @method('put')
                {{--szablon StoreUnitTypes--}}
                <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
                    <div class="row p-2 mt-0">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Typ opakowania</label>
                                <input type="text" name="code"
                                    value="{{$storeunittype->code}}" class="form-control @error('code') is-invalid @enderror"
                                    id="code" required readonly>

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
                                    value="{{$storeunittype->prefix}}" class="form-control @error('prefix') is-invalid @enderror" id="prefix" required>

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
                                <input type="text" name="size_x"
                                    value="{{$storeunittype->size_x}}" class="number form-control @error('size_x') is-invalid @enderror" id="size_x" required>


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
                                <input type="text" name="size_y"
                                    value="{{$storeunittype->size_y}}" class="number form-control @error('size_y') is-invalid @enderror" id="size_y" required>


                                    @error('size_y')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="size_z">Dopuszczalna wysokość (cm)</label>
                                <input type="text" name="size_z"
                                    value="{{$storeunittype->size_z}}" class="number form-control @error('size_z') is-invalid @enderror" id="size_z" required>


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
                                <input type="text" name="loadwgt"
                                    value="{{$storeunittype->loadwgt}}" class="number form-control @error('loadwgt') is-invalid @enderror" id="loadwgt" required>


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
                                <input type="text" name="suwgt"
                                    value="{{$storeunittype->suwgt}}" class="number form-control @error('suwgt') is-invalid @enderror" id="suwgt" required>


                                    @error('suwgt')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>

                <div class="row p-1">
                    <div class="col-md-6 p-3">
                        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
                        <a href="{{ route('storeunittypes.index') }}"
                                    class="btn btn-light">Anuluj</a>
                    </div>
                    <div class="col-md-6 p-3">
                        <a href="#"  data-coreui-toggle="modal" data-coreui-target="#delete{{ $storeunittype->id }}"
                            class="btn btn-danger float-end text-white px-4 ">Usuń
                        </a>
                    </div>
                </div>
                </div>
                </form>
                @include("storeunittypes.delete")
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
@push('scripts')
<script>
    $('.number').on('change', function(){
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
</script>
@endpush
