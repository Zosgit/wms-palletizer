@extends('layouts.app')


@section('content')
    @include('dashboard')
    <div class="card mb-4">
        <div class="card-header">
          <strong>  {{ __('Dashboard') }} </strong>
        </div>
        <div class="card-body">
            {{ __('You are logged in!') }}
        </div>
    </div>
@endsection
