<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\StoreUnit;
use App\Models\Stock;
use App\Models\StoreLogs;
use App\Models\LogicalArea;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class MoveController extends Controller
{
        /*
            Zasada dla opakowań
                1 zmiana na opakowaniu
                2 zmiana na miejscu
                3 zmiana na poprzednim miejscu - spr czy są inne opakowania jak nie to - wolna
                4 log z info

        */
    public function storeunitshow(Request $request)
    {

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
        return view('move.storeunitstep1',compact('storeunit','start'));
    }

    public function storeunitshow2($id, Request $request)
    {


        if ($request->search != null)
        {
            $location = DB::table('v_locations')->where('ean',$request->search)->first();
            $start = '1';

        }
        else
        {
            $start = '';
            $location = '';
        }

        $storeunit = DB::table('v_store_units')->where('id',$id)->first();
        return view('move.storeunitstep2',compact('storeunit','location','start','id'));
    }

    public function storeunitsave(Request $request)
    {
        $request->validate([

            'hidden_su' => 'required',
            'hidden_loc' => 'required',
        ]);

        $storeunit = StoreUnit::findorfail($request->hidden_su);
        $locationNew = Location::findorfail($request->hidden_loc);

        $notes = 'Przesunięcie opakowania.';


        // czy opakowanie ma miejsce
        if ($storeunit->location_id > 0)
        {
            $locationOld = Location::findorfail($storeunit->location_id);
            $ile = StoreUnit::getLocationStoreUnit($storeunit->location_id);
        }
        else
        {
            $locationOld = $locationNew;
            $notes = 'Przesunięcie nowego opakowania.';
            $ile = 10;
        }

        StoreLogs::create([
            'job_type'              => 2,
            'storeunit_id_in'       => $request->hidden_su,
            'storeunit_ean_in'      => $storeunit->ean,
            'storeunit_id_out'      => $request->hidden_su,
            'storeunit_ean_out'     => $storeunit->ean,
            'location_in'           => $locationOld->id,
            'location_ean_in'       => $locationOld->ean,
            'location_out'          => $locationNew->id,
            'location_ean_out'      => $locationNew->ean,
            'notes'                 => $notes,
        ]);

        $storeunit->location_id = $request->hidden_loc;
        $storeunit->update();

        $locationNew->status_id = 204;
        $locationNew->update();

        // przed zmianą spr czy są inne opakowania w tym miejscu, jak nie ma to zmiana na wolną

        if ($ile == 1)
        {
            $locationOld->status_id = 202;
            $locationOld->update();
        }
        // spr na starym miejscu...
        return redirect()->route('move.su')->with('success', 'Wykonane przesunięcie opakowania'.$ile);

    }

    public function findstoreunit(Request $request)
    {
        $data = DB::table('v_store_units')->where('ean',$request->id)->first();
        return response()->json($data);
    }

    public function findlocation(Request $request)
    {
        $data = DB::table('v_locations')->where('ean',$request->id)->first();
        return response()->json($data);
    }

    public function productshow(Request $request)
    {
        if ($request->search != null)
        {
            $storeunit = DB::table('v_store_units')->where('ean',$request->search)->first();
            $start = '1';
            if ($storeunit != null) {
                $stocks = Stock::getStockStoreUnit($storeunit->id);
            }
            else {
                $stocks = '';
            }
        }
        else
        {
            $start = '';
            $storeunit = '';
            $stocks = '';
        }
        return view('move.productstep1',compact('storeunit','start','stocks'));
    }

    public function productshow2(Stock $stock, Request $request)
    {
        if ($request->search != null)
        {
            $storeunit = DB::table('v_store_units')->where('ean',$request->search)->where('status_id',102)->first();
            $start = '1';
            if ($storeunit != null) {
                $stocks = Stock::getStockStoreUnit($storeunit->id);
            }
            else {
                $stocks = '';
            }
        }
        else
        {
            $start = '';
            $location = '';
            $storeunit = '';
            $stocks = '';
        }

        return view('move.productstep2',compact('storeunit','start','stock','stocks'));
    }

    public function productsave(Request $request)
    {
        /*
            Zasada dla produktów
                1 zmiana na opakowaniu dodajemy
                2 zmiana na opakowaniu odejmujemy
                3 log z info

        */
        $request->validate([

            'quantity_new' => 'required',
            'hidden_stock' => 'required',
            'hidden_store_unit' => 'required'
        ]);

        $stock_in  = Stock::findorfail($request->hidden_stock);
        $storeunit_out = StoreUnit::findorfail($request->hidden_store_unit);

        $notes = 'Przesunięcie produktu, ilość: '.$request->quantity_new;

        //szukam czy jest produkt w opakowaniu mający takie same cechy
        $stock_out = Stock::where('id','<>',$stock_in->id)
        ->where('store_unit_id',$storeunit_out->id)
        ->where('product_id',$stock_in->product_id)
        ->where('prod_code',$stock_in->prod_code)
        ->where('prod_desc',$stock_in->prod_desc)
        ->where('expiration_at',$stock_in->expiration_at)
        ->where('serial_nr', $stock_in->serial_nr)
        ->where('quarantine', $stock_in->quarantine)
        ->where('owner_id',$stock_in->owner_id)
        ->where('ship_detail_id',$stock_in->ship_detail_id)
        ->where('logical_area_id',$stock_in->logical_area_id)
        ->where('fifo',$stock_in->fifo)
        ->where('remarks',$stock_in->remarks)->first();

        if (is_null($stock_out))
        {
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
                'quantity'          => $request->quantity_new,
                'fifo'              => $stock_in->fifo,
                'remarks'           => $stock_in->remarks,
                'status_id'         => 302
            ]);
        }
        else
        {
            $stock_out->quantity = $stock_out->quantity + $request->quantity_new;
            $stock_out->update();
        }

        StoreLogs::create([
            'job_type'              => 3,
            'storeunit_id_in'       => $stock_in->store_unit_id,
            'storeunit_ean_in'      => $stock_in->StoreUnit->ean,
            'storeunit_id_out'      => $storeunit_out->id,
            'storeunit_ean_out'     => $storeunit_out->ean,
            'prod_id'               => $stock_in->product_id,
            'prod_code'             => $stock_in->prod_code,
            'prod_desc'             => $stock_in->prod_desc,
            'quantity'              => $request->quantity_new,
            'expiration_at'         => $stock_in->expiration_at,
            'serial_nr'             => $stock_in->serial_nr,
            'owner_id'              => $stock_in->owner_id,
            'ship_detail_id'        => $stock_in->ship_detail_id,
            'logical_area_id_out'   => $stock_in->logical_area_id,
            'logical_area_code_out' => $stock_in->logical_area->code,
            'notes'                 => $notes
        ]);

        // jezeli ilosc mniejsza od przenoszonej to odejmuje - jak równa to usuwam
        if ($stock_in->quantity > $request->quantity_new)
            {
                $stock_in->quantity = $stock_in->quantity - $request->quantity_new;
                $stock_in->update();
            }
        else
            {
                $stock_in->delete();
            }

        return redirect()->route('move.product')->with('success', 'Wykonane przesunięcie produktu');

    }

    public function areashow(Request $request)
    {
        if ($request->search != null)
        {
            $storeunit = DB::table('v_store_units')->where('ean',$request->search)->first();
            $start = '1';
            if ($storeunit != null) {
                $stocks = Stock::getStockStoreUnit($storeunit->id);
            }
            else {
                $stocks = '';
            }
        }
        else
        {
            $start = '';
            $storeunit = '';
            $stocks = '';
        }
        return view('move.areastep1',compact('storeunit','start','stocks'));
    }

    public function areashow2(Stock $stock)
    {
        $logicalareas = LogicalArea::orderBy('code')->get();

        return view('move.areastep2',compact('stock','logicalareas'));
    }

    public function areasave(Request $request)
    {
        /*
            Zasada dla mag logicznego
                1 zmiana na opakowaniu dodajemy
                2 zmiana na opakowaniu odejmujemy
                3 log z info

        */
        $request->validate([
            'hidden_stock'      => 'required',
            'quantity_new'      => 'required',
            'logical_area_id'   => 'required',
            'notes'             => 'required'
        ]);

        $notes = 'Logiczne przesunięcie produktu, ilość: '.$request->quantity_new;

        $stock  = Stock::findorfail($request->hidden_stock);
        $storeunit = StoreUnit::findorfail($stock->store_unit_id);

        $logical_area_new = LogicalArea::findorfail($request->logical_area_id);

        Stock::create([
            'store_unit_id'     => $stock->store_unit_id,
            'product_id'        => $stock->product_id,
            'prod_code'         => $stock->prod_code,
            'prod_desc'         => $stock->prod_desc,
            'expiration_at'     => $stock->expiration_at,
            'serial_nr'         => $stock->serial_nr,
            'quarantine'        => $stock->quarantine,
            'owner_id'          => $stock->owner_id,
            'ship_detail_id'    => $stock->ship_detail_id,
            'logical_area_id'   => $request->logical_area_id,
            'quantity'          => $request->quantity_new,
            'fifo'              => $stock->fifo,
            'remarks'           => $request->notes,
            'status_id'         => 302
        ]);

        StoreLogs::create([
            'job_type'              => 4,
            'storeunit_id_in'       => $stock->store_unit_id,
            'storeunit_ean_in'      => $stock->StoreUnit->ean,
            'storeunit_id_out'      => $stock->store_unit_id,
            'storeunit_ean_out'     => $stock->StoreUnit->ean,
            'prod_id'               => $stock->product_id,
            'prod_code'             => $stock->prod_code,
            'prod_desc'             => $stock->prod_desc,
            'quantity'              => $request->quantity_new,
            'expiration_at'         => $stock->expiration_at,
            'serial_nr'             => $stock->serial_nr,
            'owner_id'              => $stock->owner_id,
            'ship_detail_id'        => $stock->ship_detail_id,
            'logical_area_id_in'    => $stock->logical_area_id,
            'logical_area_code_in'  => $stock->logical_area->code,
            'logical_area_id_out'   => $logical_area_new->id,
            'logical_area_code_out' => $logical_area_new->code,
            'notes'                 => $notes
        ]);


        if (($stock->quantity - $request->quantity_new) == 0)
        {
            // jest 0 więc usuwam
            $stock->delete();
        }
        else
        {
            $stock->quantity = $stock->quantity - $request->quantity_new;
            $stock->update();
        }


        return redirect()->route('move.area')->with('success', 'Wykonane przesunięcie produktu');

    }


}


