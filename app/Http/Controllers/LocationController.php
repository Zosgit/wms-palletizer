<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\StoreArea;
use App\Models\Status;
use DB;



class LocationController extends Controller
{
    public function index(Request $request)
    {
        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $locations  = DB::table('v_locations')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','desc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $locations = DB::table('v_locations')->wherenull($request->type)->orderby('updated_at','desc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $locations = DB::table('v_locations')->orderby('updated_at','desc')->paginate(100);
            }


        return view('locations.index', compact('locations','request'));

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

    public function edit($id)
    {
        $location= Location::findOrFail($id);
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
    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Etykieta lokacji usunięta');
    }
}
