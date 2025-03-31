<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\StoreUnit;
use App\Models\Location;
use App\Models\StoreUnitType;

class StoreUnitController extends Controller
{
    public function index()
    {

        $su = new StoreUnit();
        $su->CreateUnit(1);

        $storeunits = StoreUnit::whereNotNull('location_id')->get();
        return view('storeunits.index', compact('storeunits'));

    }

    public function create()
    {
        return view('storeunits.create',['store_unit_types' => StoreUnitType::all(),
                                        'locations' => Location::all()]);
    }


    public function store(Request $request)
    {
        dd($request);
        //Product::updateOrCreate(['id' => $request->id], $request->except('id'));
        //return redirect()->route('products.index')->with('success', 'Produkt dodany poprawnie!');
    }

}
