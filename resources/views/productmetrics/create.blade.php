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
              @include('productmetrics.fields')
            </div>
        </div>
    </div>

</div>
@endsection
