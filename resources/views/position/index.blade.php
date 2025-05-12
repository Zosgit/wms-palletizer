@extends('layouts.app')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Pozycjonowanie paczek</b>
                <a href="{{ route('position.create') }}"
                    class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
        </div>

        <div class="card-body">
@if($position->picklist_id)
    <span class="badge bg-info">Z kompletacji #{{ $position->picklist_id }}</span>
@endif

        </div>

    </div>
@endsection
