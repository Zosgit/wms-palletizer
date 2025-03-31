<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\StoreArea;
use App\Models\Status;


class LocationController extends Controller
{
    public function index()
    {
        //$loc = new Location();
        //$w1 = $loc->getStoreAreaCount(1,201);
        //$w2 = $loc->getStoreAreaCount(1,202);
        //echo 'jest: '.$w1.' na: '.$w2;

        $locations = Location::paginate();
        $locationCount = Location::count();

        return view('locations.index', compact('locations','locationCount'));
    }

    public function create()
    {
        return view('locations.create',['store_areas' => StoreArea::all(),
                                        'status' => Status::getObject('LOC')]);
    }

    public function show()
    {
return 'bbb';
    }

    public function createmulti()
    {
        return 'aa';
       // return view('locations.createmulti',['store_areas' => StoreArea::all(),
        //                                'status' => Status::getObject('LOC')]);
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'pos_x' => 'required',
            'pos_y' => 'required',
            'pos_z' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'loadwgt' => 'required|Decimal:2',
            'storearea_id' => 'required',
            'status_id' => 'required',
            'ean' => 'required'
        ]);


        //dd($request);
        Location::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('locations.index')->with('success', 'Etykieta lokacji dodana poprawnie!');
    }

    public function storeMulti(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'pos_x' => 'required',
            'pos_y' => 'required',
            'pos_z' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'loadwgt' => 'required|Decimal:2',
            'storearea_id' => 'required',
            'status_id' => 'required',
            'ean' => 'required'
        ]);


        //dd($request);
        Location::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('locations.index')->with('success', 'Etykieta lokacji dodana poprawnie!');
    }

    public function edit(Location $location)
    {
       // dd($products = Product::find($id));
        return view('locations.edit', ['location' => $location,
                                        'store_areas' => StoreArea::all(),
                                        'status' => Status::getObject('LOC')]);
    }

    public function update(Location $location, Request $request)
    {
        //dd($request);
        $validatedAttributes = $request->validate ([
            'pos_x' => 'required',
            'pos_y' => 'required',
            'pos_z' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'loadwgt' => 'required|Decimal:2',
            'storearea_id' => 'required',
            'status_id' => 'required',
            'ean' => 'required'
        ]);

       // dd($validatedAttributes);

        $location->update($validatedAttributes);
        return redirect()->route('locations.index')->with('success', 'Etykieta lokacji edytowa poprawnie!');

    }
    // należy dodać wpis sprawdzający czy asortyment jest na zapasie.
    //jeżeli jest to komunikat i nie usuwamy !!!
    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Etykieta lokacji usunięta');
    }
}
