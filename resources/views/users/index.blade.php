@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <b>Spis pracowników</b>{{ ' - '.$userCount}}
        </div>

        <div class="card-body">

            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Imię</th>
                    <th scope="col">Nazwisko</th>
                    <th scope="col">Email</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->firstname }}</td>
                        <td>{{ $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>

        {{--
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        --}}
    </div>
@endsection
