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

        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $shipments = DB::table('v_shipments_controls')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','desc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $shipments = DB::table('v_shipments_controls')->wherenull($request->type)->orderby('updated_at','desc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $shipments = DB::table('v_shipments_controls')->orderby('updated_at','desc')->paginate(100);
            }

        return view('shipmentcontrol.index', compact('shipments'));
    }

    public function view($id)
    {
        $shipment = Shipment::findorfail($id);
        $status_id = $shipment->status_id;

        $full_control = ShipmentDetail::getControls($id);

        $controls = DB::table('v_control_list')->where('ship_id',$id)->orderby('updated_at','desc')->paginate(200);

        if ($status_id = 403)
        {
            $shipmentdetails = ShipmentDetail::where('ship_id',$id)->paginate(200);
            return view('shipmentcontrol.view',compact('shipment','shipmentdetails','full_control','controls'));
        }
        return view('shipmentdetail.show',compact('shipment','products','logicalareas','shipmentdetails','controls'));
    }

    public function add($id,$loc)
    {
        // opakowanie + ilosć max jaka dostępna
        $shipmentdetail = ShipmentDetail::findorfail($id);
        $ship_id = $shipmentdetail->ship_id;
        //$storeunits = StoreUnit::getUnitShipment();

        $storeunits = DB::table('v_ship_locations')->where('status_id', 101)->orwhere('ship_id',$ship_id)->paginate(200);

        $logicalareas = LogicalArea::orderBy('code')->get();

        return view('shipmentcontrol.add',compact('shipmentdetail','storeunits','logicalareas','loc'));
    }

    public function store($id,$loc,Request $request)
    {
        /*
            1 - walidancja danych
            2 - zmiana ilości dla pozycji dostawy od ilości skontrolowanej odejmuje obecną ilość
            3 - wstawiam opakowanie do miejsca + log
            4 - wstawiam produkty do opakowania + log

        */

        // 1
        $request->validate ([
            'su_id' => 'required',
            'logical_area_id'   => 'required',
            'quantity_new'  => 'required|Decimal:2',
            'serial_nr'         => '',
            'expiration_at'     => '',
            'remarks'           => '',
        ]);

        // 2
        $shipmentdetail = ShipmentDetail::findorfail($id);

        $shipmentdetail->quantity_control = $shipmentdetail->quantity_control - $request->quantity_new;
        $shipmentdetail->update();

        //3
        $storeunit = StoreUnit::findorfail($request->su_id);
        $storeunit->location_id = $loc;
        $storeunit->status_id = 102;
        $storeunit->update();

        $location = Location::findorfail($loc);
        $ship_nr = $shipmentdetail->shipment->ship_nr;
        $owner_id = $shipmentdetail->shipment->owner_id;

        $logical = LogicalArea::findorfail($request->logical_area_id);
/*
chyba nie ma sensu ?
        StoreLogs::create([
            'job_type' => 2,
            'storeunit_id_out'  => $request->su_id,
            'storeunit_ean_out' => $storeunit->ean,
            'location_out'      => $loc,
            'location_ean_out'  => $location->ean,
            'ship_detail_id'    => $id,
            'notes'             => $ship_nr.' - kontrola dostawy - wstawiam opakowanie'
        ]);
*/
        //4
        Stock::create([
            'store_unit_id'     => $request->su_id,
            'product_id'        => $shipmentdetail->product_id,
            'prod_code'         => $shipmentdetail->prod_code,
            'prod_desc'         => $shipmentdetail->prod_desc,
            'expiration_at'     => $request->expiration_at,
            'serial_nr'         => $request->serial_nr,
            'quarantine'        => $request->quarantine,
            'owner_id'          => $owner_id,
            'ship_detail_id'    => $shipmentdetail->id,
            'logical_area_id'   => $request->logical_area_id,
            'quantity'          => $request->quantity_new,
            'fifo'              => now(),
            'remarks'           => $request->remarks,
            'status_id'         => 302
        ]);

        ShipmentControl::create([
            'ship_id'           => $ship_nr = $shipmentdetail->ship_id,
            'store_unit_id'     => $request->su_id,
            'store_unit_ean'    => $storeunit->ean,
            'product_id'        => $shipmentdetail->product_id,
            'prod_code'         => $shipmentdetail->prod_code,
            'prod_desc'         => $shipmentdetail->prod_desc,
            'expiration_at'     => $request->expiration_at,
            'serial_nr'         => $request->serial_nr,
            'quarantine'        => $request->quarantine,
            'ship_detail_id'    => $shipmentdetail->id,
            'logical_area_id'   => $request->logical_area_id,
            'quantity'          => $request->quantity_new,
            'fifo'              => now(),
            'remarks'           => $request->remarks,
            'status_id'         => 302

            ]);

        StoreLogs::create([
            'job_type' => 3,
            'storeunit_id_out'      => $request->su_id,
            'storeunit_ean_out'     => $storeunit->ean,
            'location_out'          => $loc,
            'location_ean_out'      => $location->ean,
            'prod_id'               => $shipmentdetail->product_id,
            'prod_code'             => $shipmentdetail->prod_code,
            'prod_desc'             => $shipmentdetail->prod_desc,
            'quantity'              => $request->quantity_new,
            'expiration_at'         => $request->expiration_at,
            'serial_nr'             => $request->serial_nr,
            'owner_id'              => $owner_id,
            'ship_detail_id'        => $id,
            'logical_area_id_out'   => $request->logical_area_id,
            'logical_area_code_out' => $logical->code,
            'notes' => $ship_nr.' - kontrola dostawy - wstawiam produkty'
        ]);


        return redirect()->route('control.view',['id'=>$shipmentdetail->ship_id])->with('success', 'Pozycja skontrolowana');

    }

    public function close($id)
    {
        $shipment = Shipment::findorfail($id);
        $shipment->status_id = 404;
        $shipment->save();
        return redirect()->route('control.index')->with('success', 'Kontrola zakończona');
    }

    public function closePos($id)
    {
        $shipmentdetail = ShipmentDetail::findorfail($id);
        $shipmentdetail->quantity_control = 0;
        $shipmentdetail->update();
        return redirect()->route('control.view',['id'=>$shipmentdetail->ship_id])->with('error', $shipmentdetail->prod_code.' - zrezygnowano z kontroli !');
    }
}
