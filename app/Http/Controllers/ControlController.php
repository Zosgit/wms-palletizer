<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Counter;
use App\Models\Location;
use App\Models\ShipmentDetail;
use App\Models\StoreUnit;
use App\Models\LogicalArea;
use App\Models\StoreLogs;
use App\Models\Stock;
use App\Models\ShipmentControl;
use DB;

class ControlController extends Controller
{
    public function index(Request $request)
    {
        if ($request->type != null) {
            if ($request->search != null && $request->type != null) {
                $shipments = DB::table('v_shipments_controls')
                    ->where($request->type, 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(100);
            } else {
                $shipments = DB::table('v_shipments_controls')
                    ->whereNull($request->type)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(100);
            }
        } else {
            $shipments = DB::table('v_shipments_controls')
                ->orderBy('updated_at', 'desc')
                ->paginate(100);
        }

        return view('shipmentcontrol.index', compact('shipments'));
    }

    public function view($id)
{
    $shipment = Shipment::findOrFail($id);
    $status_id = $shipment->status_id;
    $full_control = ShipmentDetail::getControls($id);

    // Pobieramy szczegóły dostawy z produktami i ich kompletami
    $shipmentdetails = ShipmentDetail::with(['product', 'product.productsets'])
        ->where('ship_id', $id)
        ->get();

    $groupedDetails = [];

    foreach ($shipmentdetails as $detail) {
        $product = $detail->product;
        $sets = $product->productsets;

        if ($sets->isNotEmpty()) {
            foreach ($sets as $set) {
                $setId = $set->id;

                if (!isset($groupedDetails[$setId])) {
                    $groupedDetails[$setId] = [
                        'set_name' => $set->code,
                        'is_set' => true,
                        'products' => []
                    ];
                }

                $groupedDetails[$setId]['products'][] = $detail;
            }
        } else {
            $groupedDetails[] = [
                'is_set' => false,
                'product' => $detail
            ];
        }
    }

    // Kontrole (już przyjęte produkty)
    $controls = DB::table('v_control_list')
        ->where('ship_id', $id)
        ->orderBy('updated_at', 'desc')
        ->paginate(200);

    return view('shipmentcontrol.view', compact('shipment', 'groupedDetails', 'full_control', 'controls'));
}


    public function add($id, $loc)
    {
        $shipmentdetail = ShipmentDetail::findOrFail($id);
        $ship_id = $shipmentdetail->ship_id;

        $storeunits = DB::table('v_ship_locations')
            ->where('status_id', 101)
            ->orWhere('ship_id', $ship_id)
            ->paginate(200);

        $logicalareas = LogicalArea::orderBy('code')->get();

        return view('shipmentcontrol.add', compact('shipmentdetail', 'storeunits', 'logicalareas', 'loc'));
    }

    public function store($id, $loc, Request $request)
    {
        $request->validate([
            'su_id' => 'required',
            'logical_area_id' => 'required',
            'quantity_new' => 'required|decimal:2',
            'serial_nr' => '',
            'expiration_at' => '',
            'remarks' => '',
        ]);

        $shipmentdetail = ShipmentDetail::findOrFail($id);
        $shipmentdetail->quantity_control -= $request->quantity_new;
        $shipmentdetail->update();

        $storeunit = StoreUnit::findOrFail($request->su_id);
        $storeunit->location_id = $loc;
        $storeunit->status_id = 102;
        $storeunit->update();

        $location = Location::findOrFail($loc);
        $ship_nr = $shipmentdetail->shipment->ship_nr;
        $owner_id = $shipmentdetail->shipment->owner_id;

        $logical = LogicalArea::findOrFail($request->logical_area_id);

        Stock::create([
            'store_unit_id' => $request->su_id,
            'product_id' => $shipmentdetail->product_id,
            'prod_code' => $shipmentdetail->prod_code,
            'prod_desc' => $shipmentdetail->prod_desc,
            'expiration_at' => $request->expiration_at,
            'serial_nr' => $request->serial_nr,
            'quarantine' => $request->quarantine,
            'owner_id' => $owner_id,
            'ship_detail_id' => $shipmentdetail->id,
            'logical_area_id' => $request->logical_area_id,
            'quantity' => $request->quantity_new,
            'fifo' => now(),
            'remarks' => $request->remarks,
            'status_id' => 302
        ]);

        ShipmentControl::create([
            'ship_id' => $shipmentdetail->ship_id,
            'store_unit_id' => $request->su_id,
            'store_unit_ean' => $storeunit->ean,
            'product_id' => $shipmentdetail->product_id,
            'prod_code' => $shipmentdetail->prod_code,
            'prod_desc' => $shipmentdetail->prod_desc,
            'expiration_at' => $request->expiration_at,
            'serial_nr' => $request->serial_nr,
            'quarantine' => $request->quarantine,
            'ship_detail_id' => $shipmentdetail->id,
            'logical_area_id' => $request->logical_area_id,
            'quantity' => $request->quantity_new,
            'fifo' => now(),
            'remarks' => $request->remarks,
            'status_id' => 302
        ]);

        StoreLogs::create([
            'job_type' => 3,
            'storeunit_id_out' => $request->su_id,
            'storeunit_ean_out' => $storeunit->ean,
            'location_out' => $loc,
            'location_ean_out' => $location->ean,
            'prod_id' => $shipmentdetail->product_id,
            'prod_code' => $shipmentdetail->prod_code,
            'prod_desc' => $shipmentdetail->prod_desc,
            'quantity' => $request->quantity_new,
            'expiration_at' => $request->expiration_at,
            'serial_nr' => $request->serial_nr,
            'owner_id' => $owner_id,
            'ship_detail_id' => $id,
            'logical_area_id_out' => $request->logical_area_id,
            'logical_area_code_out' => $logical->code,
            'notes' => $ship_nr . ' - kontrola dostawy - wstawiam produkty'
        ]);

        return redirect()->route('control.view', ['id' => $shipmentdetail->ship_id])
            ->with('success', 'Pozycja skontrolowana');
    }

    public function close($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->status_id = 404;
        $shipment->save();
        return redirect()->route('control.index')->with('success', 'Kontrola zakończona');
    }

    public function closePos($id)
    {
        $shipmentdetail = ShipmentDetail::findOrFail($id);
        $shipmentdetail->quantity_control = 0;
        $shipmentdetail->update();
        return redirect()->route('control.view', ['id' => $shipmentdetail->ship_id])
            ->with('error', $shipmentdetail->prod_code . ' - zrezygnowano z kontroli !');
    }
}
