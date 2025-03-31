<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Counter;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{

    public function index(Request $request)
    {

        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $shipments = DB::table('v_shipments')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','desc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $shipments = DB::table('v_shipments')->wherenull($request->type)->orderby('updated_at','desc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $shipments = DB::table('v_shipments')->orderby('updated_at','desc')->paginate(100);
            }

        return view('shipments.index', compact('shipments'));
    }


    public function create()
    {
        return view('shipments.create',['status' => Status::getObject('SHIPMENT'),
                                        'firms'=>Firm::getShipment(),
                                        'owners'=>Firm::getOwner(),
                                        'locations'=>Location::getLocationShipment()
                                    ]);
    }


    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'external_nr' => 'required|max:50',
            'firm_id' => 'required',
            'owner_id' => 'required',
            'location_id' => 'required',
            'remarks' => '',

        ]);

        /*

        DB::select(DB::raw("exec my_stored_procedure :Param1, :Param2"),[
            ':Param1' => $param_1,
            ':Param2' => $param_2,
        ]);
        */

        $location_id = $validatedAttributes['location_id'];

        $validatedAttributes['ship_nr'] = Counter::getNumber('SHIPMENT');

        $shipment = Shipment::create($validatedAttributes);

        Location::SetStatus($location_id,204,'Blokuję miejsce dla dostawy: '.$validatedAttributes['ship_nr']);

        return redirect()->route('shipmentdetail.index', ['id' => $shipment->id]);
    }


    public function show(Shipment $shipment)
    {
        dd($shipment);
        return view('shipments.index');
    }


    public function edit(Shipment $shipment)
    {
        //
    }


    public function update(Request $request, Shipment $shipment)
    {
        //
    }


    public function destroy(Shipment $shipment)
    {
        //
    }

}
