@extends('layouts.app')
@section('title') {{ 'pick.newstoreunit' }} @endsection
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Dodaje opakowanie</b>
        </div>

        <div class="card-body">
            <form action="" method="GET">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź opakowanie" required/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Szukaj opakowania</button>
                    </div>
                </div>
            </form>
            @if (isset($storeunit->id))
                <div class="row mb-4">
                    <div>Opakowanie: <strong>{{ $storeunit->ean }}</strong></div>
                    <div>Rodzaj: <strong>{{ $storeunit->code_unit_type }}</strong></div>
                    <div>Lokalizacja: <strong>{{ $storeunit->ean_loc }}</strong></div>

                    @if ($storeunit->code_status == 'Nowa')
                        <div class="mb-4">Status: <strong class="text-success">{{ $storeunit->code_status }}</strong></div>
                        <div class="row mb-4">
                            <a href="{{ route('pick.licklistsu_save',['id' => $id,'su'=>$storeunit->id]) }}"
                                class="btn btn-primary float-start px-4 btn-sm">Zatwierdzam</a>
                        </div>

                    @else
                        <div>Status: <strong class="text-warning">{{ $storeunit->code_status }}</strong></div>
                    @endif



                </div>
            @else
                @if($start <> '')
                <div class="row mb-4">
                    <div class="text-danger"><strong>Nie znalazłem opakowania !</strong></div>
                </div>
                @endif
            @endif
        </div>



    </div>

@endsection
