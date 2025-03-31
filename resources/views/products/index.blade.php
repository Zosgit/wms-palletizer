@extends('layouts.app')
@section('title') {{ 'Lista produktów' }} @endsection
@section('content')

    <div class="card mb-4">
        <div class="card-header">
            <b>Lista produktów</b>
                <a href="{{ route('products.create') }}"
                    class="btn btn-primary float-end px-4 btn-sm">Dodaj</a>
        </div>

        <div class="card-body">

            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Wybierz pole</option>
                            <option value="ean">EAN</option>
                            <option value="code">Nazwa</option>
                            <option value="longdesc">Opis</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" value="" class="form-control" placeholder="Wprowadź dane..."/>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-dark">Filtruj</button>
                    </div>
                </div>

            </form>
            <table id="data_table" class="table table-striped table-hover table-sm">
                <thead>
                <tr>
                    <th scope="col">Nazwa</th>
                    <th scope="col">Opis</th>
                    <th scope="col">EAN</th>
                    <th scope="col">JM</th>
                    <th scope="col"> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->code }}</a></td>
                        <td>{{ $product->longdesc }}</td>
                        <td>{{ $product->ean }}</td>
                        <td>{{ $product->code_product_metric ?? '' }}</td>
                        <td><a href="{{ route('products.edit', ['id' => $product->id]) }}">
                                <svg class="icon icon-lg">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-color-border') }}"></use>
                                </svg>
                            </a>

                            <a href="{{ route('report.product', ['id' => $product->id]) }}">
                                <svg class="icon icon-lg">
                                    <use xlink:href="{{ asset('icons/coreui.svg#cil-info') }}"></use>
                                </svg>
                            </a>



                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                {{$products->links() }}
            </div>
            <p>
                Ilość: {{$products->count()}} z {{ $products->total() }} rekordów.
            </p>

        </div>



    </div>

@endsection
