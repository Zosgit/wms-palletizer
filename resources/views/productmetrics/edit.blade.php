@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Dodaj nową jednostkę miary</b>
                </div>
                    <form action="{{ route('productmetrics.update',['productmetric' => $productmetric]) }}" class="forms-sample" method="POST">
                        @csrf
                        @method('put')
                        {{--szablon Products--}}
                        <div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
                            <div class="row p-2">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="code">Ilość (skrót)</label>
                                        <input type="text" name="code"
                                            value="{{$productmetric->code}}" class="form-control @error('code') is-invalid @enderror"  id="code" required readonly>

                                            @if (isset($productmetric))
                                                <input type="hidden" name="metric_id" value="{{ $productmetric->id }}">
                                            @endif

                                            @error('code')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 pt-2">
                                        <div class="form-group">
                                            <label for="amount">Liczba</label>
                                            <input type="text" name="amount"
                                                value="{{$productmetric->amount}}" class="form-control  @error('amount') is-invalid @enderror" id="amount" required>

                                                @error('amount')
                                                    <span class="invalid-feedback" role="alert">
                                                       <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="longdesc">Opis</label>
                                        <textarea type="text" name="longdesc" class="form-control  @error('longdesc') is-invalid @enderror" id="longdesc" rows="4" required>{{$productmetric->longdesc}}</textarea>

                                        @error('longdesc')
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
                                    <a href="{{ route('productmetrics.index') }}" class="btn btn-light">Anuluj</a>
                                </div>
                                <div class="col-md-6 p-3">
                                    <a href="#"  data-coreui-toggle="modal" data-coreui-target="#delete{{ $productmetric->id }}"
                                        class="btn btn-danger float-end text-white px-4 ">Usuń
                                    </a>
                                </div>
                            </div>
                        </div>

                        </div>
                    </form>
                    @include("productmetrics.delete")
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
