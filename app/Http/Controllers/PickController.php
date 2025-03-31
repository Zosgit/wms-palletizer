<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Location;
use App\Models\OrderDetail;
use App\Models\PickOrder;
use App\Models\StoreUnit;
use App\Models\StoreLogs;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class PickController extends Controller
{

    public function index(Request $request)
    {
        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $orders = DB::table('v_orders_pick')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','asc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $orders = DB::table('v_orders_pick')->wherenull($request->type,'like',$request->search)->orderby('updated_at','asc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $orders = DB::table('v_orders_pick')->orderby('updated_at','asc')->paginate(100);
            }

        return view('pick.index', compact('orders'));
    }

    public function picklistsu($id, Request $request)
    {

        // sprawdzam ile jest wykonane pobrania / przepakowania
        $count_pick = OrderDetail::getfull($id);

        // pobieram id opakowania
        $pickorder = PickOrder::where('order_id',$id)->where('status_id',102)->orderby('updated_at','asc')->first();

        if ($count_pick == 0)
        {
            // wszystko wykonane - jeżeli opakowanie jest aktywne to zamykam automatycznie status na 105 (opakowanie + pick)
            if (!is_null($pickorder))
            {
                $storeunit = StoreUnit::findorfail($pickorder->store_unit_id);
                $storeunit->status_id = 105;
                $storeunit->update();

                $pickorder->status_id = 105;
                $pickorder->update();

            }
            // przekierowanie i pokazuje wszystko
            return redirect()->route('pick.view',['id'=> $id]);
        }

        if (is_null($pickorder) and ($count_pick > 0))
        {
            // nowe opakowanie
            if ($request->search != null)
            {
                $storeunit = DB::table('v_store_units')->where('ean',$request->search)->first();
                $start = '1';
            }
            else
            {
                $start = '';
                $storeunit = '';
            }
            return view('pick.newstoreunit', compact('id','start','storeunit'));

        }

        return redirect()->route('pick.picklist',['id'=> $id,'su' => $pickorder->store_unit_id]);
    }

    public function licklistsu_save($id,$su)
    {
        /*
             1. opakowanie - ustawiam status + miejsce
             2. dodaje wpis do pick odnośnie opakowania
             3. logi
        */
        $order = Order::findorfail($id);
        $storeunit = StoreUnit::findorfail($su);
        $location = Location::findorfail($order->location_id);

        $storeunit->location_id = $order->location_id;
        $storeunit->status_id = 102;
        $storeunit->update();


        PickOrder::create([
            'order_id'          => $id,
            'store_unit_id'     => $su,
            'store_unit_ean'    => $storeunit->ean,
            'status_id'         => 102
        ]);

        StoreLogs::create([
            'job_type' => 1,
            'storeunit_id_out'      => $su,
            'storeunit_ean_out'     => $storeunit->ean,
            'location_out'          => $order->location_id,
            'location_ean_out'      => $location->ean,
            'notes'                 => 'Wydanie - przypisuje opakowanie: '.$order->order_nr,
        ]);

        return redirect()->route('pick.picklist',['id'=> $id]);

    }

    public function licklistsu_close($id,$su)
    {

        $storeunit = StoreUnit::findorfail($su);
        $storeunit->status_id = 105;
        $storeunit->update();

        // oraz na pick
        $pickorder = PickOrder::where('order_id',$id)->where('status_id',102)->where('store_unit_id',$su)->orderby('updated_at','asc')->first();
        $pickorder->status_id=105;
        $pickorder->update();


        return redirect()->route('pick.storeunit',['id'=> $id])->with('success', 'Opakowanie zamknięte');
    }

    public function picklist($id)
    {
        // lista produktów do spakowania - kompletacji
        $orderdetails = OrderDetail::where('order_id',$id)->paginate(500);

        // operacje wykonane
        $pickings = PickOrder::where('order_id',$id)->whereNotNull('quantity')->paginate(500);

        $su = PickOrder::where('order_id',$id)->where('status_id',102)->orderby('updated_at','asc')->first();

        //opakowanie
        if (is_null($su))
        {
            $order = Order::findorfail($id);
        }
        else
        {
            $storeunit = StoreUnit::findorfail($su->store_unit_id);
        }

        return view('pick.picklist', compact('orderdetails','id','pickings','storeunit'));

    }

    public function picklist2($id)
    {
        // lista miejsc
        $orderdetail = OrderDetail::findorfail($id);

        $stocks = DB::table('v_pick_list')->where('product_id',$orderdetail->product_id)
                                            ->where('logical_area_id',$orderdetail->logical_area_id)
                                            ->orderby('fifo','asc')->paginate(100);

        return view('pick.picklist2', compact('orderdetail','stocks'));

    }

    public function picklistsave(Request $request)
    {
        /*
             1. opakowanie - dodaje do opakowania
             2. logi

             2. dodaje wpis do pick odnośnie opakowania
             3. logi
             4. zmiana na opakowaniu od... jeżeli pusta to delete i stan na lokacji
        */
        $validatedAttributes = $request->validate([
            'order_id'          => 'required',
            'quantity'          => 'required',
            'stock_id'          => 'required',
            'orderdetail_id'    => 'required'
        ]);

        $order_nr = $request->order_id;

        $stock_in = Stock::findorfail($request->stock_id);
        $su_in = $stock_in->store_unit_id;

        // pobieram opakowanie z ktorego pobieram
        $storeunit_in = StoreUnit::findorfail($su_in);

        // pobieram opakowanie do ktorego dokładam
        $storeunit_out_id = PickOrder::getActiveStoreUnit($order_nr);

        $storeunit_out = StoreUnit::findorfail($storeunit_out_id->store_unit_id);

        $notes = 'Kompletacja - przesunięcie produktu, ilość: '.$request->quantity;

        //ok dodaje do zapasu opakowania
        Stock::create([
            'store_unit_id'     => $storeunit_out->id,
            'product_id'        => $stock_in->product_id,
            'prod_code'         => $stock_in->prod_code,
            'prod_desc'         => $stock_in->prod_desc,
            'expiration_at'     => $stock_in->expiration_at,
            'serial_nr'         => $stock_in->serial_nr,
            'quarantine'        => $stock_in->quarantine,
            'owner_id'          => $stock_in->owner_id,
            'ship_detail_id'    => $stock_in->ship_detail_id,
            'logical_area_id'   => $stock_in->logical_area_id,
            'quantity'          => $request->quantity,
            'fifo'              => $stock_in->fifo,
            'remarks'           => $stock_in->remarks,
            'status_id'         => 302
        ]);

        //logi
        StoreLogs::create([
            'job_type'              => 3,
            'storeunit_id_in'       => $stock_in->store_unit_id,
            'storeunit_ean_in'      => $stock_in->StoreUnit->ean,
            'storeunit_id_out'      => $storeunit_out->id,
            'storeunit_ean_out'     => $storeunit_out->ean,
            'location_in'           => $storeunit_in->sulocation->id,
            'location_ean_in'       => $storeunit_in->sulocation->ean,
            'location_out'          => $storeunit_out->sulocation->id,
            'location_ean_out'      => $storeunit_out->sulocation->ean,
            'prod_id'               => $stock_in->product_id,
            'prod_code'             => $stock_in->prod_code,
            'prod_desc'             => $stock_in->prod_desc,
            'quantity'              => $request->quantity,
            'expiration_at'         => $stock_in->expiration_at,
            'serial_nr'             => $stock_in->serial_nr,
            'owner_id'              => $stock_in->owner_id,
            'ship_detail_id'        => $stock_in->ship_detail_id,
            'logical_area_id_out'   => $stock_in->logical_area_id,
            'logical_area_code_out' => $stock_in->logical_area->code,
            'notes'                 => $notes
        ]);

        PickOrder::create([
            'order_id'          => $order_nr,
            'store_unit_id'     => $storeunit_out->id,
            'store_unit_ean'    => $storeunit_out->ean,
            'product_id'        => $stock_in->product_id,
            'prod_code'         => $stock_in->prod_code,
            'prod_desc'         => $stock_in->prod_desc,
            'expiration_at'     => $stock_in->expiration_at,
            'serial_nr'         => $stock_in->serial_nr,
            'ship_detail_id'    => $stock_in->ship_detail_id,
            'logical_area_id'   => $stock_in->logical_area_id,
            'quantity'          => $request->quantity,
            'fifo'              => $stock_in->fifo,
            'remarks'           => $stock_in->remarks,
            'status_id'         => 302
        ]);

        // jezeli ilosc mniejsza od przenoszonej to odejmuje - jak równa to usuwam oraz zmiana stanu na lokacji [wolna]
        if ($stock_in->quantity > $request->quantity)
            {
                $stock_in->quantity = $stock_in->quantity - $request->quantity;
                $stock_in->update();
            }
        else
            {
                $stock_in->delete();
                Location::where('id',$storeunit_in->location_id)->update(['status_id' => 202]);
                $storeunit_in->delete();
            }

        //zmiana na pozycji zamówienia - odejmuje
        $orderdatail = OrderDetail::findorfail($request->orderdetail_id);
        $orderdatail->quantity_pick = $orderdatail->quantity_pick - $request->quantity;
        $orderdatail->update();


        return redirect()->route('pick.storeunit',['id'=> $order_nr])->with('success', 'Produkt dodany poprawnie!');
    }

    public function view($id)
    {

        $order = Order::findorfail($id);

        $pickings = PickOrder::where('order_id',$id)->whereNotNull('quantity')->paginate(500);

        return view('pick.view', compact('order','pickings'));
    }

    public function pickclose($id)
    {
        /*
            wydaje - zmieniam
            1. zamówienie - status
            2. zapas - delete
            3. opakowania - delete
            4. miejsce na wolne
        */

        $order = Order::findorfail($id);

        // pobieram liste opakowań
        $pickorders = PickOrder::where('order_id',$id)->where('status_id',105)->get();

        foreach($pickorders as $pick)
        {
            $storeunit = StoreUnit::findorfail($pick->store_unit_id);
            $delete = DB::delete('DELETE FROM stocks WHERE store_unit_id = ?', [$storeunit->id]);

            $storeunit->delete();
        }

        $order->status_id = 504;
        $order->update();

        Location::where('id',$order->location_id)->update(['status_id' => 202]);
        OrderDetail::where('order_id',$id)->update(['status_id' => 504]);

        return redirect()->route('pick.view',['id'=> $id]);

    }
}
