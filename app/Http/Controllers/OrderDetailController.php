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
        ->values();
    $heaviestDetail = $orderdetails->first();

    // 3. Pobierz dostępne opakowania
    $storeunits = StoreUnit::with('storeunittype')
        ->whereNotNull('ean')
        ->whereIn('status_id', [101, 102])
        ->get();

    // 4. Ustal maksymalne wymiary opakowań
    $maxUnitSize = [
        'x' => $storeunits->max(fn($u) => $u->storeunittype->size_x ?? 0),
        'y' => $storeunits->max(fn($u) => $u->storeunittype->size_y ?? 0),
        'z' => $storeunits->max(fn($u) => $u->storeunittype->size_z ?? 0),
    ];

    // 5. Oblicz wagę i objętość zamówienia
    $totalVolume = 0;
    $totalWeight = 0;
    $missingProducts = [];

    foreach ($orderdetails as $detail) {
        $product = $detail->product;

        $hasAllData = $product && $product->size_x && $product->size_y && $product->size_z && $product->weight;

        if (! $hasAllData) {
            $missingProducts[] = $detail;
            continue;
        }

        $volumePerItem = $product->size_x * $product->size_y * $product->size_z;

        if ($product->can_overhang == 1) {
            $cut_x = min($product->size_x, $maxUnitSize['x']);
            $cut_y = min($product->size_y, $maxUnitSize['y']);
            $cut_z = min($product->size_z, $maxUnitSize['z']);
            $volumePerItem = $cut_x * $cut_y * $cut_z;
        }

        $totalVolume += $volumePerItem * $detail->quantity;
        $totalWeight += $product->weight * $detail->quantity;
    }

    $totalVolume = $totalVolume / 1000000; // m³

    // 6. Zaoszczędzona objętość
    $reducedVolumeCount = 0;
    $reducedVolumeAmount = 0;
$trimmedProducts = [];


    foreach ($orderdetails as $detail) {
        $product = $detail->product;

        if (! $product || ! $product->size_x || ! $product->size_y || ! $product->size_z || ! $product->weight) {
            continue;
        }

if ($product->can_overhang == 1) {
    $fullVolume = $product->size_x * $product->size_y * $product->size_z;

    $cut_x = min($product->size_x, $maxUnitSize['x']);
    $cut_y = min($product->size_y, $maxUnitSize['y']);
    $cut_z = min($product->size_z, $maxUnitSize['z']);

    $cutVolume = $cut_x * $cut_y * $cut_z;

    $reducedVolumeAmount += ($fullVolume - $cutVolume) * $detail->quantity;
    $reducedVolumeCount++;

    $trimmedProducts[] = [
        'code' => $detail->prod_code,
        'desc' => $product->prod_desc ?? '',
        'original' => [
            'x' => $product->size_x,
            'y' => $product->size_y,
            'z' => $product->size_z,
        ],
        'trimmed' => [
            'x' => $cut_x,
            'y' => $cut_y,
            'z' => $cut_z,
        ]
    ];
}

    }

    $reducedVolumeAmount = round($reducedVolumeAmount / 1000000, 4); // m³

    // 7. Sortuj opakowania wg objętości malejąco
    $sortedUnits = $storeunits->filter(function ($unit) {
        $type = $unit->storeunittype;
        return $type && $type->size_x && $type->size_y && $type->size_z && $type->loadwgt;
    })->sortByDesc(function ($unit) {
        $type = $unit->storeunittype;
        return $type->size_x * $type->size_y * $type->size_z;
    });

    // 8. Dobór opakowań
    $usedUnits = [];
    $remainingVolume = $totalVolume;
    $remainingWeight = $totalWeight;

    foreach ($sortedUnits as $unit) {
        $type = $unit->storeunittype;
        $unitVolume = ($type->size_x * $type->size_y * $type->size_z) / 1000000;
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

    $noUnitsAvailable = ($remainingVolume > 0 || $remainingWeight > 0);

    // 9. Statystyki opakowań
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

    // 10. Statystyki zamówienia
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

    // 11. Oblicz liczbę palet
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

    // 12. Widok
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
        'heaviestDetail',
        'reducedVolumeCount',
        'reducedVolumeAmount',
        'noUnitsAvailable',
        'trimmedProducts'
    ));
}


}
