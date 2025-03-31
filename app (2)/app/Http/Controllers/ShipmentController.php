<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Counter;

class ShipmentController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        return view('shipments.create',['status' => Status::getObject('SHIPMENT'),
                                        'firms'=>Firm::getShipment(),
                                        'owners'=>Firm::getOwner()]);
    }


    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'external_nr' => 'required|max:50',
            'firm_id' => 'required',
            'owner_id' => 'required',
            'remarks' => '',
        ]);

        $validatedAttributes['ship_nr'] = Counter::getNumber('SHIPMENT');


        $shipment = Shipment::create($validatedAttributes);
       // $id = $shipment->id;
        //dd($id);

        return redirect()->route('shipmentdetail.index', ['shipment' => $shipment]);
    }


    public function show(Shipment $shipment)
    {
        //
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
