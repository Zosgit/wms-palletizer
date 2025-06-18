<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Status;
use App\Models\Firm;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\StoreUnit;
use App\Models\StoreUnitType;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    public function index($id)
    {
        $order = Order::findorfail($id);
        $status_id = $order->status_id;
        $order_id = $order->id;
        $owner_id = $order->owner_id;

        $orderdetails = OrderDetail::where('order_id',$id)->paginate(100);

        if ($status_id == 502)
        {
            return view('orderdetail.index',compact('order','orderdetails'));
        }

        return view('orderdetail.index',compact('order','orderdetails'));

    }

    public function create($id ,Request $request)
    {
        $order = Order::findorfail($id);
        $owner_id = $order->owner_id;


        if ($request->search != null)
        {
            $stocks = DB::table('v_reservation')->where('prod_code','like','%'.$request->search.'%')
                                            ->where('owner_id',$owner_id)
                                            ->where('sum_stock','>',0)
                                            ->where('status_id',302)->paginate(5000);
        }
        else
        {
            $stocks = DB::table('v_reservation')->where('owner_id',$owner_id)
                                                ->where('sum_stock','>',0)
                                                ->where('status_id',302)->paginate(5000);
        }

        return view('orderdetail.create',compact('order','stocks'));

    }

    public function save(Request $request)
    {

        $validatedAttributes = $request->validate([
            'order_id'          => 'required',
            'quantity'          => 'required',
            'logical_area_id'   => 'required',
            'product_id'        => 'required',
            'prod_code'         => 'required',
            'prod_desc'         => 'required'
        ]);

        OrderDetail::create($validatedAttributes);

        return redirect()->route('orderdetail.create',['id'=> $request->order_id])->with('success', 'Produkt dodany poprawnie!');


    }
    public function distroy($id)
    {

    }

    // wysyłam do kompletacji recznej
    public function sendpick($id)
    {
        $order = Order::findorfail($id);
        $order->status_id = 503;
        $order->save();
        return redirect()->route('orders.index')->with('success', 'Dokument przekazany do kompletacji');
    }

    // wysylam do kompletacji automatycznej(nasz algorytm)
    // public function autopick($id)
    // {
    //     // Tu dodamy logikę algorytmu optymalizacji (heurystyka, objętość, itp.)
    //     return redirect()->back()->with('success', 'Uruchomiono kompletację automatyczną (jeszcze bez logiki).');
    // }

public function autopick($id)
{
    // 1. Pobierz zamówienie i zmień status
    $order = Order::findOrFail($id);
    $order->status_id = 503;
    $order->save();

    // 2. Pobierz pozycje zamówienia
$orderdetails = $order->order_details()
    ->get()
    ->sortByDesc(function ($detail) {
        return ($detail->product->weight ?? 0) * $detail->quantity;
    })
    ->values(); // resetuje indeksy
$heaviestDetail = $orderdetails->first(); // pierwszy po sortowaniu to najcięższy


    // 3. Oblicz wagę i objętość zamówienia
    $totalVolume = 0;
    $totalWeight = 0;
    $missingProducts = []; // tu zbierzemy brakujące produkty

    foreach ($orderdetails as $detail) {
        $product = $detail->product;

        $hasAllData = $product && $product->size_x && $product->size_y && $product->size_z && $product->weight;

        if (! $hasAllData) {
            $missingProducts[] = $detail;
            continue; // pomiń ten produkt
        }

        $volumePerItem = $product->size_x * $product->size_y * $product->size_z; // cm³
        $totalVolume += $volumePerItem * $detail->quantity;
        $totalWeight += $product->weight * $detail->quantity;
    }

    $totalVolume = $totalVolume / 1000000; // m³

    // 4. Pobierz dostępne opakowania z magazynu (z EAN i typem)
    $storeunits = StoreUnit::with('storeunittype')
        ->whereNotNull('ean')
        ->get();

    // 5. Posortuj opakowania wg objętości malejąco
    $sortedUnits = $storeunits->filter(function ($unit) {
        $type = $unit->storeunittype;
        return $type && $type->size_x && $type->size_y && $type->size_z && $type->loadwgt;
    })->sortByDesc(function ($unit) {
        $type = $unit->storeunittype;
        return $type->size_x * $type->size_y * $type->size_z;
    });

    // 6. Dobierz konkretne opakowania
    $usedUnits = [];
    $remainingVolume = $totalVolume;
    $remainingWeight = $totalWeight;

    foreach ($sortedUnits as $unit) {
        $type = $unit->storeunittype;
        $unitVolume = ($type->size_x * $type->size_y * $type->size_z) / 1000000; // m³
        $unitMaxWeight = $type->loadwgt;

        if ($unitVolume <= 0 || $unitMaxWeight <= 0) {
            continue;
        }

        $usedUnits[] = $unit;
        $remainingVolume -= $unitVolume;
        $remainingWeight -= $unitMaxWeight;

        if ($remainingVolume <= 0 && $remainingWeight <= 0) {
            break;
        }
    }

    // 7. Statystyki opakowań
    $unitsUsedCount = count($usedUnits);
    $usedVolumeTotal = 0;
    $usedWeightCapacityTotal = 0;

    foreach ($usedUnits as $unit) {
        $type = $unit->storeunittype;
        $usedVolumeTotal += ($type->size_x * $type->size_y * $type->size_z) / 1000000;
        $usedWeightCapacityTotal += $type->loadwgt;
    }

    $volumeFillPercent = $usedVolumeTotal > 0 ? round(($totalVolume / $usedVolumeTotal) * 100, 1) : 0;
    $weightFillPercent = $usedWeightCapacityTotal > 0 ? round(($totalWeight / $usedWeightCapacityTotal) * 100, 1) : 0;

    // 8. Statystyki zamówienia
    $totalItems = $orderdetails->sum('quantity');
    $uniqueProducts = $orderdetails->count();

    $fragilityStats = [
        'twardy' => 0,
        'miekki' => 0,
        'kruchy' => 0,
    ];

    foreach ($orderdetails as $detail) {
        $fragility = strtolower($detail->product->fragility ?? 'inne');
        if (isset($fragilityStats[$fragility])) {
            $fragilityStats[$fragility] += $detail->quantity;
        }
    }

    // 9. Oblicz liczbę palet na podstawie objętości i wagi
    $selectedUnit = $sortedUnits->first();
    $volumeBasedCount = 0;
    $weightBasedCount = 0;
    $paletCount = 0;
    $paletBasis = '';

    if ($selectedUnit && $selectedUnit->storeunittype) {
        $type = $selectedUnit->storeunittype;
        $unitVolume = ($type->size_x * $type->size_y * $type->size_z) / 1000000;
        $unitMaxWeight = $type->loadwgt;

        if ($unitVolume > 0) {
            $volumeBasedCount = ceil($totalVolume / $unitVolume);
        }

        if ($unitMaxWeight > 0) {
            $weightBasedCount = ceil($totalWeight / $unitMaxWeight);
        }

        $paletCount = max($volumeBasedCount, $weightBasedCount);

        if ($volumeBasedCount > $weightBasedCount) {
            $paletBasis = 'objętości';
        } elseif ($weightBasedCount > $volumeBasedCount) {
            $paletBasis = 'wagi';
        } else {
            $paletBasis = 'zarówno objętości, jak i wagi';
        }
    }




    // 10. Przekazanie danych do widoku
    return view('orderdetail.autopick', compact(
        'order',
        'orderdetails',
        'totalVolume',
        'totalWeight',
        'storeunits',
        'usedUnits',
        'unitsUsedCount',
        'usedVolumeTotal',
        'usedWeightCapacityTotal',
        'volumeFillPercent',
        'weightFillPercent',
        'totalItems',
        'uniqueProducts',
        'fragilityStats',
        'volumeBasedCount',
        'weightBasedCount',
        'paletCount',
        'paletBasis',
        'missingProducts',
'heaviestDetail'

    ));
}


}
