@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cube"></i>
                    &nbsp; <b>Dodaj nowego kontrahenta</b>
                </div>
              @include('firms.fields')
            </div>
        </div>
    </div>

</div>
@endsection
