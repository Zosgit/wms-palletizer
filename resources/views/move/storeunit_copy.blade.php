@extends('layouts.app')
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Przesunięcie opakowania</b>
        </div>

        <div class="card-body">

            <form id="forma" action="{{ route('move.savestoreunit') }}" class="forms-sample" method="POST">
                @csrf
                <div id="BoxSU" class="row mb-2">
                    <div class="col-md-3">
                        <input type="text" name="search_su" id="search_su" value="" class="form-control" placeholder="Wprowadź opakowanie."/>
                    </div>
                    <div class="col-md-4">
                        <a id="addSU"class="btn btn-dark">Szukaj opakowania</a>
                    </div>
                </div>

                <div id="info_su" class="row mb-4"></div>

                <div id="BoxLOC" class="row mb-2">
                    <div class="col-md-3">
                        <input type="text" name="search_loc" id="search_loc" value="" class="form-control" placeholder="Wprowadź miejsce"/>
                    </div>
                    <div class="col-md-4">
                        <a id="addLOC"class="btn btn-dark">Szukaj miejsce</a>
                    </div>
                </div>


                <div id="info_loc" class="row mb-4"></div>


                </div>
                <input type="hidden" id='hidden_su' name='hidden_su' value=""/>
                <input type="hidden" id='hidden_loc' name='hidden_loc'value=""/>


            </form>
        </div>



    </div>

@endsection
@push('scripts')
<script>
    $(document).ready(function(){
        function CheckId()
        {
            let id_su = $('#hidden_su').val();
            let id_loc = $('#hidden_loc').val();

            if (id_loc > 0 && id_su > 0){
                $('#forma').append('<button type="submit" class="btn btn-primary mr-4">Wykonaj przesunięcie</button>');
            }

        };

        $('#addSU').on('click', function () {
            let storeunit = $('#search_su').val();
            if(storeunit == ""){
                $('#button_confirm').html('');
                $('#info_su').html('');
            }
            else{
                $.ajax({
                    type    : 'GET',
                    url     :'{!! URL::route('findstoreunit') !!}',

                    dataType: 'json',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'), 'id':storeunit},
                    success:function (data) {
                        if(data.id >0){
                            let SuInfo =
                            '<div>Opakowanie: <strong>'+data.ean+'</strong></div>\n' +
                            '<div>Rodzaj: <strong>'+data.code_unit_type+'</strong></div>\n' +
                            '<div>Lokalizacja: <strong>'+data.ean_loc+'</strong></div>\n' +
                            '<div">Status: <strong>'+data.code_status+'</strong></div>\n';
                            if (data.code_status == 'Dostępna'){
                                $('#hidden_su').val(data.id);
                                $('#BoxSU').html('');
                            }
                            $('#info_su').html(SuInfo);
                            CheckId();
                        }
                        else{
                            $('#info_su').html('<div><strong>Nie znalazłem danych !</strong></div>');
                        }
                    }
                });
            }
        });

        $('#addLOC').on('click', function () {
            let loc = $('#search_loc').val();
            if(loc == ""){
                $('#info_loc').html('');
            }
            else{
                $.ajax({
                    type    : 'GET',
                    url     :'{!! URL::route('findlocation') !!}',

                    dataType: 'json',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'), 'id':loc},
                    success:function (data) {
                        if(data.id >0){
                            let LocInfo =
                            '<div>Miejsce: <strong>'+data.ean+'</strong></div>\n' +
                            '<div>Lokalizacja: <strong>'+data.code_store_area+'</strong></div>\n' +
                            '<div">Status: <strong>'+data.code_status+'</strong></div>\n';

                            if (data.code_status == 'Wolna'){
                                $('#hidden_loc').val(data.id);
                                $('#BoxLOC').html('');
                            }
                            $('#info_loc').html(LocInfo);

                            CheckId();
                        }
                        else{
                            $('#info_loc').html('<div><strong>Nie znalazłem danych !</strong></div>');
                        }

                    }
                });
            }
        });
    });
</script>
@endpush
