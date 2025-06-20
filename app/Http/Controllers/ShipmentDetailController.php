<?php

namespace App\Http\Controllers;

use App\Models\ShipmentDetail;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Product;
use App\Models\LogicalArea;
use App\Models\ProductSet;

class ShipmentDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index($id)
{
    $shipment = Shipment::findOrFail($id);
    $status_id = $shipment->status_id;
    $ship_id = $shipment->id;

    $products = Product::getShipment();
    $logicalareas = LogicalArea::orderBy('code')->get();
    $shipmentdetails = ShipmentDetail::where('ship_id', $ship_id)->with('product.productsets')->get();

    // Grupowanie: komplet vs pojedynczy produkt
    $groupedDetails = [];

    foreach ($shipmentdetails as $detail) {
        $product = $detail->product;
        $setId = optional($product->productsets->first())->id;

        if ($setId) {
            if (!isset($groupedDetails[$setId])) {
                $groupedDetails[$setId] = [
                    'is_set' => true,
                    'set_name' => optional($product->productsets->first())->code,
                    'items' => [],
                ];
            }
            $groupedDetails[$setId]['items'][] = $detail;
        } else {
            $groupedDetails[] = $detail; // pojedynczy produkt
        }
    }

    if ($status_id < 403) {
        return view('shipmentdetail.index', compact('shipment', 'products', 'logicalareas', 'shipmentdetails', 'groupedDetails'));
    }

    return view('shipmentdetail.show', compact('shipment', 'products', 'logicalareas', 'shipmentdetails', 'groupedDetails'));
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

        $productSets = ProductSet::orderBy('code')->get();
        return view('shipmentdetail.create', compact('shipment', 'products', 'logicalareas', 'productSets'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request, Shipment $shipment)
{
    // Zakładamy, że frontend zawsze przesyła albo produkt, albo komplet
    $productInput = $request->input('product_id'); // np. "prod_3"
    $productSetInput = $request->input('product_set_id'); // np. "set_1"

    $validatedAttributes = $request->validate([
        'logical_area_id'   => 'required',
        'quantity'          => 'required|Decimal:2',
        'serial_nr'         => '',
        'expiration_at'     => '',
        'remarks'           => '',
    ]);

    $logicalAreaId = $validatedAttributes['logical_area_id'];
    $quantity = $validatedAttributes['quantity'];
    $serialNr = $validatedAttributes['serial_nr'];
    $expiration = $validatedAttributes['expiration_at'];
    $remarks = $validatedAttributes['remarks'];

    $shipmentId = $shipment->id;

    // ✅ 1. Obsługa KOMPLETU
    if ($productSetInput && str_starts_with($productSetInput, 'set_')) {
        $setId = str_replace('set_', '', $productSetInput);
        $productSet = ProductSet::with('products')->findOrFail($setId);

        foreach ($productSet->products as $product) {
            ShipmentDetail::create([
                'ship_id' => $shipmentId,
                'product_id' => $product->id,
                'prod_code' => $product->code,
                'prod_desc' => $product->longdesc,
                'logical_area_id' => $logicalAreaId,
                'quantity' => $quantity, // lub przeliczenie jeśli komplet zawiera inne proporcje
                'serial_nr' => $serialNr,
                'expiration_at' => $expiration,
                'remarks' => '[komplet: ' . $productSet->code . '] ' . $remarks,
            ]);
        }

    // ✅ 2. Obsługa pojedynczego PRODUKTU
    } elseif ($productInput && str_starts_with($productInput, 'prod_')) {
        $productId = str_replace('prod_', '', $productInput);
        $product = Product::findOrFail($productId);

        ShipmentDetail::create([
            'ship_id' => $shipmentId,
            'product_id' => $product->id,
            'prod_code' => $product->code,
            'prod_desc' => $product->longdesc,
            'logical_area_id' => $logicalAreaId,
            'quantity' => $quantity,
            'serial_nr' => $serialNr,
            'expiration_at' => $expiration,
            'remarks' => $remarks,
        ]);
    }

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
