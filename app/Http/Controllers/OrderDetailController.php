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
use App\Models\OrderPickAuto;
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

public function create($id, Request $request)
{
    $order = Order::findOrFail($id);
    $owner_id = $order->owner_id;

    // Produkty spoza kompletów (z widoku v_reservation)
    $query = DB::table('v_reservation')
                ->where('owner_id', $owner_id)
                ->where('sum_stock', '>', 0)
                ->where('status_id', 302);

    if ($request->filled('search')) {
        $query->where('prod_code', 'like', '%' . $request->search . '%');
    }

    $stocks = $query->paginate(5000);

    // Produkty należące do kompletów
    $completeStocks = DB::table('complete_product')
        ->join('products', 'products.id', '=', 'complete_product.product_id')
        ->join('product_sets', 'product_sets.id', '=', 'complete_product.product_sets_id')
        ->join('stocks', function ($join) use ($owner_id) {
            $join->on('stocks.product_id', '=', 'products.id')
                 ->where('stocks.owner_id', '=', $owner_id);
        })
        ->join('logical_areas', 'stocks.logical_area_id', '=', 'logical_areas.id')
        ->select(
            'product_sets.code as set_name',
            'product_sets.id as set_id',
            'stocks.product_id',
            'stocks.logical_area_id',
            'stocks.prod_code',
            'products.longdesc',
            'logical_areas.code as code_la',
            DB::raw('SUM(stocks.quantity) as sum_stock')
        )
        ->groupBy(
            'product_sets.code',
            'product_sets.id',
            'stocks.product_id',
            'stocks.logical_area_id',
            'stocks.prod_code',
            'products.longdesc',
            'logical_areas.code'
        )
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->where('stocks.prod_code', 'like', '%' . $request->search . '%');
        })
        ->get();

    // Grupowanie kompletów
    $groupedBySet = $completeStocks->groupBy('set_id');
    $groupedStocks = [];

    foreach ($groupedBySet as $setId => $items) {
        $groupedStocks[] = [
            'is_set' => true,
            'set_id' => $setId,
            'set_name' => $items->first()->set_name,
            'items' => $items,
        ];
    }

    // Dodajemy produkty spoza kompletów jako osobne wpisy
    foreach ($stocks as $stock) {
        $groupedStocks[] = $stock;
    }

    return view('orderdetail.create', compact('order', 'stocks'))->with('groupedDetails', $groupedStocks);

}


   public function save(Request $request)
{
    if ($request->has('set_id')) {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'set_id'   => 'required|integer',
            'set_quantity' => 'required|integer|min:1',
        ]);

        // Pobierz komplet
        $set_id = $validated['set_id'];
        $qty = $validated['set_quantity'];
        $order_id = $validated['order_id'];

        // Pobierz produkty wchodzące w skład kompletu
        $productsInSet = DB::table('complete_product')
            ->where('product_sets_id', $set_id)
            ->get();

        foreach ($productsInSet as $productInSet) {
            // Pobierz dane produktu i zapasu (np. z widoku v_reservation lub z tabeli stocks)
            $stock = DB::table('v_reservation')
                ->where('product_id', $productInSet->product_id)
                ->where('owner_id', function($q) use ($order_id) {
                    $q->select('owner_id')->from('orders')->where('id', $order_id)->limit(1);
                })
                ->where('sum_stock', '>', 0)
                ->first();

            if (!$stock) continue;

            // Dopisz każdy produkt jako osobny wpis
            OrderDetail::create([
                'order_id'        => $order_id,
                'quantity'        => $qty * 1, // 1 sztuka danego produktu na komplet — rozbuduj jeśli inne przeliczniki
                'logical_area_id' => $stock->logical_area_id,
                'product_id'      => $stock->product_id,
                'prod_code'       => $stock->prod_code,
                'prod_desc'       => $stock->longdesc
            ]);
        }

        // (Opcjonalnie) zapisz w tabeli np. `shipment_sets` informacje o dodanym komplecie
        // ShipmentSet::create([ 'order_id' => $order_id, 'product_sets_id' => $set_id, 'quantity' => $qty ]);

        return redirect()->route('orderdetail.create', ['id' => $order_id])
            ->with('success', 'Komplet dopisany i rozbity na produkty!');
    }

    // Obsługa pojedynczego produktu (jak do tej pory)
    $validatedAttributes = $request->validate([
        'order_id'          => 'required',
        'quantity'          => 'required',
        'logical_area_id'   => 'required',
        'product_id'        => 'required',
        'prod_code'         => 'required',
        'prod_desc'         => 'required'
    ]);

    OrderDetail::create($validatedAttributes);

    return redirect()->route('orderdetail.create', ['id' => $request->order_id])
        ->with('success', 'Produkt dodany poprawnie!');
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
        $order = Order::findOrFail($id);
        $order->status_id = 503;
        $order->save();

        $orderdetails = $order->order_details()
            ->with('product')
            ->get()
            ->sortByDesc(fn($d) => ($d->product->weight ?? 0) * $d->quantity)
            ->values();

        $heaviestDetail = $orderdetails->first();

        $storeunits = StoreUnit::with('storeunittype')
            ->whereNotNull('ean')
            ->whereIn('status_id', [101, 102])
            ->get();

        $maxUnitSize = [
            'x' => $storeunits->max(fn($u) => $u->storeunittype->size_x ?? 0),
            'y' => $storeunits->max(fn($u) => $u->storeunittype->size_y ?? 0),
            'z' => $storeunits->max(fn($u) => $u->storeunittype->size_z ?? 0),
        ];

        $totalVolume = 0;
        $totalWeight = 0;
        $missingProducts = [];
        $trimmedProducts = [];

        $groupedOrderDetails = $orderdetails->groupBy(fn($item) => $item->product_set_id ?? 'product_' . $item->id);

        foreach ($groupedOrderDetails as $group) {
            $groupVolume = 0;
            $groupWeight = 0;
            $validGroup = true;

            foreach ($group as $detail) {
                $product = $detail->product;
                $hasAllData = $product && $product->size_x && $product->size_y && $product->size_z && $product->weight;

                if (! $hasAllData) {
                    $missingProducts[] = $detail;
                    $validGroup = false;
                    continue;
                }

                $volumePerItem = $product->size_x * $product->size_y * $product->size_z;

                if ($product->can_overhang == 1) {
                    $cut_x = min($product->size_x, $maxUnitSize['x']);
                    $cut_y = min($product->size_y, $maxUnitSize['y']);
                    $cut_z = min($product->size_z, $maxUnitSize['z']);
                    $volumePerItem = $cut_x * $cut_y * $cut_z;

                    $trimmedProducts[] = [
                        'code' => $detail->prod_code,
                        'desc' => $product->prod_desc ?? '',
                        'original' => ['x' => $product->size_x, 'y' => $product->size_y, 'z' => $product->size_z],
                        'trimmed' => ['x' => $cut_x, 'y' => $cut_y, 'z' => $cut_z]
                    ];
                }

                $groupVolume += $volumePerItem * $detail->quantity;
                $groupWeight += $product->weight * $detail->quantity;
            }

            if ($validGroup) {
                $totalVolume += $groupVolume;
                $totalWeight += $groupWeight;
            }
        }

        $totalVolume = $totalVolume / 1000000;

        $reducedVolumeCount = count($trimmedProducts);
        $reducedVolumeAmount = 0;

        foreach ($trimmedProducts as $item) {
            $full = $item['original']['x'] * $item['original']['y'] * $item['original']['z'];
            $cut = $item['trimmed']['x'] * $item['trimmed']['y'] * $item['trimmed']['z'];
            $reducedVolumeAmount += ($full - $cut);
        }

        $reducedVolumeAmount = round($reducedVolumeAmount / 1000000, 4);

        // --- ALGORYTM OBJĘTOŚCIOWY ---
        $volumeUnits = $this->pickUnitsBy('volume', $storeunits, $totalVolume, $totalWeight);
        $volCount = count($volumeUnits);
        $volVolume = $volumeUnits->map(fn($u) => ($u->storeunittype->size_x * $u->storeunittype->size_y * $u->storeunittype->size_z) / 1000000)->sum();
        $volWeight = $volumeUnits->sum(fn($u) => $u->storeunittype->loadwgt);

        // --- ALGORYTM WAGOWY ---
        $weightUnits = $this->pickUnitsBy('weight', $storeunits, $totalVolume, $totalWeight);
        $wgtCount = count($weightUnits);
        $wgtVolume = $weightUnits->map(fn($u) => ($u->storeunittype->size_x * $u->storeunittype->size_y * $u->storeunittype->size_z) / 1000000)->sum();
        $wgtWeight = $weightUnits->sum(fn($u) => $u->storeunittype->loadwgt);

        $usedUnits = ($volCount <= $wgtCount) ? $volumeUnits : $weightUnits;
        $noUnitsAvailable = (count($usedUnits) === 0);

        $totalItems = $orderdetails->sum('quantity');
        $uniqueProducts = $orderdetails->count();

        $fragilityStats = ['twardy' => 0, 'miekki' => 0, 'kruchy' => 0];
        foreach ($orderdetails as $detail) {
            $fragility = strtolower($detail->product->fragility ?? 'inne');
            if (isset($fragilityStats[$fragility])) {
                $fragilityStats[$fragility] += $detail->quantity;
            }
        }

        OrderPickAuto::where('order_id', $order->id)->delete();

        foreach ($usedUnits as $unit) {
            $type = $unit->storeunittype;

            $auto = OrderPickAuto::create([
                'order_id' => $order->id,
                'store_unit_id' => $unit->id,
                'used_volume' => ($type->size_x * $type->size_y * $type->size_z) / 1000000,
                'used_weight' => $type->loadwgt,
            ]);

            foreach ($order->order_details as $detail) {
                DB::table('order_pick_auto_product')->insert([
                    'order_pick_auto_id' => $auto->id,
                    'product_id' => $detail->product_id,
                ]);
            }
        }

        return view('orderdetail.autopick', compact(
            'order',
            'orderdetails',
            'totalVolume',
            'totalWeight',
            'storeunits',
            'usedUnits',
            'volumeUnits',
            'weightUnits',
            'volCount',
            'wgtCount',
            'volVolume',
            'wgtVolume',
            'volWeight',
            'wgtWeight',
            'totalItems',
            'uniqueProducts',
            'fragilityStats',
            'missingProducts',
            'heaviestDetail',
            'reducedVolumeCount',
            'reducedVolumeAmount',
            'noUnitsAvailable',
            'trimmedProducts'
        ));
    }

private function pickUnitsBy($criterion, $storeunits, $totalVolume, $totalWeight)
{
    $unitsByType = $storeunits->groupBy('storeunittype_id');
    $bestTypeId = null;
    $bestCount = PHP_INT_MAX;

    foreach ($unitsByType as $typeId => $units) {
        $type = $units->first()->storeunittype;

        if (! $type || ! $type->size_x || ! $type->size_y || ! $type->size_z || ! $type->loadwgt) {
            continue;
        }

        $unitCapacity = ($criterion === 'volume')
            ? ($type->size_x * $type->size_y * $type->size_z) / 1000000
            : $type->loadwgt;

        if ($unitCapacity <= 0) continue;

        $requiredCount = ceil((($criterion === 'volume') ? $totalVolume : $totalWeight) / $unitCapacity);

        // Jeśli liczba dostępnych jednostek danego typu jest mniejsza niż potrzebna, pomijamy
        if ($units->count() < $requiredCount) continue;

        if ($requiredCount < $bestCount) {
            $bestCount = $requiredCount;
            $bestTypeId = $typeId;
        }
    }

    if (! $bestTypeId) return [];

    $bestUnits = $unitsByType[$bestTypeId]->take($bestCount)->values();

    return $bestUnits;
}










//public function autopick($id)
// {
//     $order = Order::findOrFail($id);
//     $order->status_id = 503;
//     $order->save();

//     $orderdetails = $order->order_details()
//         ->with('product')
//         ->get()
//         ->sortByDesc(fn($d) => ($d->product->weight ?? 0) * $d->quantity)
//         ->values();

//     $heaviestDetail = $orderdetails->first();

//     // Opakowania dostępne
//     $storeunits = StoreUnit::with('storeunittype')
//         ->whereNotNull('ean')
//         ->whereIn('status_id', [101, 102])
//         ->get();

//     $maxUnitSize = [
//         'x' => $storeunits->max(fn($u) => $u->storeunittype->size_x ?? 0),
//         'y' => $storeunits->max(fn($u) => $u->storeunittype->size_y ?? 0),
//         'z' => $storeunits->max(fn($u) => $u->storeunittype->size_z ?? 0),
//     ];

//     $totalVolume = 0;
//     $totalWeight = 0;
//     $missingProducts = [];
//     $trimmedProducts = [];

//     // Grupuj po kompletach lub pojedynczych produktach
//     $groupedOrderDetails = $orderdetails->groupBy(fn($item) => $item->product_set_id ?? 'product_' . $item->id);

//     foreach ($groupedOrderDetails as $group) {
//         $groupVolume = 0;
//         $groupWeight = 0;
//         $validGroup = true;

//         foreach ($group as $detail) {
//             $product = $detail->product;

//             $hasAllData = $product && $product->size_x && $product->size_y && $product->size_z && $product->weight;

//             if (! $hasAllData) {
//                 $missingProducts[] = $detail;
//                 $validGroup = false;
//                 continue;
//             }

//             $volumePerItem = $product->size_x * $product->size_y * $product->size_z;

//             if ($product->can_overhang == 1) {
//                 $cut_x = min($product->size_x, $maxUnitSize['x']);
//                 $cut_y = min($product->size_y, $maxUnitSize['y']);
//                 $cut_z = min($product->size_z, $maxUnitSize['z']);
//                 $volumePerItem = $cut_x * $cut_y * $cut_z;

//                 $trimmedProducts[] = [
//                     'code' => $detail->prod_code,
//                     'desc' => $product->prod_desc ?? '',
//                     'original' => [
//                         'x' => $product->size_x,
//                         'y' => $product->size_y,
//                         'z' => $product->size_z,
//                     ],
//                     'trimmed' => [
//                         'x' => $cut_x,
//                         'y' => $cut_y,
//                         'z' => $cut_z,
//                     ]
//                 ];
//             }

//             $groupVolume += $volumePerItem * $detail->quantity;
//             $groupWeight += $product->weight * $detail->quantity;
//         }

//         if ($validGroup) {
//             $totalVolume += $groupVolume;
//             $totalWeight += $groupWeight;
//         }
//     }

//     $totalVolume = $totalVolume / 1000000;

//     // Zaoszczędzona objętość
//     $reducedVolumeCount = count($trimmedProducts);
//     $reducedVolumeAmount = 0;

//     foreach ($trimmedProducts as $item) {
//         $full = $item['original']['x'] * $item['original']['y'] * $item['original']['z'];
//         $cut = $item['trimmed']['x'] * $item['trimmed']['y'] * $item['trimmed']['z'];
//         $reducedVolumeAmount += ($full - $cut); // * ilość można dodać, jeśli potrzebne
//     }

//     $reducedVolumeAmount = round($reducedVolumeAmount / 1000000, 4);

//     // Sortowanie opakowań
//    $sortedUnits = $storeunits->filter(function ($unit) {
//         $type = $unit->storeunittype;
//         return $type && $type->size_x && $type->size_y && $type->size_z && $type->loadwgt;
//     })->sortBy(function ($unit) {
//         $type = $unit->storeunittype;
//         return $type->size_x * $type->size_y * $type->size_z;
//     });

//     // Dobór opakowań
//     $usedUnits = [];
//     $remainingVolume = $totalVolume;
//     $remainingWeight = $totalWeight;

//     foreach ($sortedUnits as $unit) {
//     $type = $unit->storeunittype;
//     $unitVolume = ($type->size_x * $type->size_y * $type->size_z) / 1000000;
//     $unitMaxWeight = $type->loadwgt;

//     if ($unitVolume >= $totalVolume && $unitMaxWeight >= $totalWeight) {
//         $usedUnits[] = $unit;
//         $remainingVolume = 0;
//         $remainingWeight = 0;
//         break;
//     }
// }

//     $noUnitsAvailable = ($remainingVolume > 0 || $remainingWeight > 0);

//     $unitsUsedCount = count($usedUnits);
//     $usedVolumeTotal = 0;
//     $usedWeightCapacityTotal = 0;

//     foreach ($usedUnits as $unit) {
//         $type = $unit->storeunittype;
//         $usedVolumeTotal += ($type->size_x * $type->size_y * $type->size_z) / 1000000;
//         $usedWeightCapacityTotal += $type->loadwgt;
//     }

//     $volumeFillPercent = $usedVolumeTotal > 0 ? round(($totalVolume / $usedVolumeTotal) * 100, 1) : 0;
//     $weightFillPercent = $usedWeightCapacityTotal > 0 ? round(($totalWeight / $usedWeightCapacityTotal) * 100, 1) : 0;

//     $totalItems = $orderdetails->sum('quantity');
//     $uniqueProducts = $orderdetails->count();

//     $fragilityStats = [
//         'twardy' => 0,
//         'miekki' => 0,
//         'kruchy' => 0,
//     ];

//     foreach ($orderdetails as $detail) {
//         $fragility = strtolower($detail->product->fragility ?? 'inne');
//         if (isset($fragilityStats[$fragility])) {
//             $fragilityStats[$fragility] += $detail->quantity;
//         }
//     }

//     // Szacowana liczba palet
//     $bestUnit = null;
// $minCount = PHP_INT_MAX;
// $paletBasis = '';
// $volumeBasedCount = 0;
// $weightBasedCount = 0;
// $paletCount = 0;

// foreach ($sortedUnits as $unit) {
//     $type = $unit->storeunittype;
//     $unitVolume = ($type->size_x * $type->size_y * $type->size_z) / 1000000;
//     $unitWeight = $type->loadwgt;

//     if ($unitVolume <= 0 || $unitWeight <= 0) continue;

//     $volCount = ceil($totalVolume / $unitVolume);
//     $wgtCount = ceil($totalWeight / $unitWeight);
//     $needed = max($volCount, $wgtCount);

//     if ($needed < $minCount) {
//         $minCount = $needed;
//         $bestUnit = $type;

//         if ($volCount > $wgtCount) {
//             $paletBasis = 'objętości';
//         } elseif ($wgtCount > $volCount) {
//             $paletBasis = 'wagi';
//         } else {
//             $paletBasis = 'zarówno objętości, jak i wagi';
//         }

//         $volumeBasedCount = $volCount;
//         $weightBasedCount = $wgtCount;
//     }
// }

// $paletCount = $minCount;


//     //zapis do bazy
//     // Usuń poprzednie dane kompletacji (jeśli istnieją)
//     OrderPickAuto::where('order_id', $order->id)->delete();

//     // Zapisz nowe dane kompletacji
//     foreach ($usedUnits as $unit) {
//         $type = $unit->storeunittype;

//         $auto = OrderPickAuto::create([
//             'order_id' => $order->id,
//             'store_unit_id' => $unit->id,
//             'used_volume' => ($type->size_x * $type->size_y * $type->size_z) / 1000000,
//             'used_weight' => $type->loadwgt,
//         ]);

//         // pobieramy produkty przypisane do danego zamówienia
//         foreach ($order->order_details as $detail) {
//             DB::table('order_pick_auto_product')->insert([
//                 'order_pick_auto_id' => $auto->id,
//                 'product_id' => $detail->product_id,
//             ]);
//         }
//     }




//     return view('orderdetail.autopick', compact(
//         'order',
//         'orderdetails',
//         'totalVolume',
//         'totalWeight',
//         'storeunits',
//         'usedUnits',
//         'unitsUsedCount',
//         'usedVolumeTotal',
//         'usedWeightCapacityTotal',
//         'volumeFillPercent',
//         'weightFillPercent',
//         'totalItems',
//         'uniqueProducts',
//         'fragilityStats',
//         'volumeBasedCount',
//         'weightBasedCount',
//         'paletCount',
//         'paletBasis',
//         'missingProducts',
//         'heaviestDetail',
//         'reducedVolumeCount',
//         'reducedVolumeAmount',
//         'noUnitsAvailable',
//         'trimmedProducts'
//     ));
// }


// public function confirm(Order $order)
// {
//     OrderPickAuto::where('order_id', $order->id)->update(['confirmed' => true]);
//     //dd(OrderPickAuto::where('order_id', $order->id)->get());
//     return redirect()->route('orders.index')->with('success', 'Kompletacja została zatwierdzona.');
// }

public function indexConfirmed()
{
    $orders = OrderPickAuto::where('confirmed', true)
        ->select('order_id')
        ->distinct()
        ->with('order')
        ->get();

    return view('orderdetail.confirmed.index', compact('orders'));
}

public function showConfirmed(Order $order)
{
    $picks = OrderPickAuto::where('order_id', $order->id)
        ->where('confirmed', true)
        ->with('storeunit.storeunittype')
        ->get();

    return view('orderdetail.confirmed.show', compact('order', 'picks'));
}

}
