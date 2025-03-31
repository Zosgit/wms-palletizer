<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Store Units</title>
        <style>
            @page { sheet-size: A6-L; }
         </style>
    </head>
    <body>
        @foreach ($storeunits as $su)
            <div>
                <div> <h1>{{$su->ean}}</h1></div>
                <img src="data::image/png;base64,{{ DNS1D::getBarcodePNG($su->ean, 'C128') }}" height="150" width="470" /><br />
                <div>{{$su->created_at}}</div>
                <div><h5>{{$su->storeunittype->code ?? ''}}</h5></div>
            </div>
        @endForeach



    </body>
</html>
