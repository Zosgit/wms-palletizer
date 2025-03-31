<?php

namespace App\Http\Controllers;

use App\Models\ShipmentDetail;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Product;
use App\Models\LogicalArea;

class ShipmentDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $shipment = Shipment::findorfail($id);
        $status_id = $shipment->status_id;
        $ship_id = $shipment->id;

        $products = Product::getShipment();
        $logicalareas = LogicalArea::orderBy('code')->get();
        $shipmentdetails = ShipmentDetail::where('ship_id',$ship_id)->paginate(200);

        if ($status_id < 403)
        {
            return view('shipmentdetail.index',compact('shipment','products','logicalareas','shipmentdetails'));
        }

        return view('shipmentdetail.show',compact('shipment','products','logicalareas','shipmentdetails'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Shipment $shipment)
    {
        $status_id = $shipment->status_id;

        if ($status_id > 402)
        {
            abort(404);
        }

        $products = Product::getShipment();
        $logicalareas = LogicalArea::orderBy('code')->get();

        return view('shipmentdetail.create',compact('shipment','products','logicalareas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shipment $shipment)
    {

        $validatedAttributes = $request->validate ([
            'product_id'        => 'required',
            'logical_area_id'   => 'required',
            'quantity'          => 'required|Decimal:2',
            'serial_nr'         => '',
            'expiration_at'     => '',
            'remarks'           => '',
        ]);

        $product = Product::findOrFail($validatedAttributes['product_id']);

        $validatedAttributes['ship_id'] = $shipment->id;
        $validatedAttributes['prod_code'] = $product->code;
        $validatedAttributes['prod_desc'] = $product->longdesc;

        ShipmentDetail::create($validatedAttributes);

        return redirect()->route('shipmentdetail.index', ['id' => $shipment->id]);
    }

    public function sendcontrol($id)
    {
        $shipment = Shipment::findorfail($id);
        $shipment->status_id = 403;
        $shipment->save();
        return redirect()->route('shipments.index')->with('success', 'Dokument przekazany do kontroli');
    }




    /**
     * Display the specified resource.
     */
    public function show(ShipmentDetail $shipmentDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShipmentDetail $shipmentDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShipmentDetail $shipmentDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
