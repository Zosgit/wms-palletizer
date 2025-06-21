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

    $volumeAlgorithm = $this->runVolumePackingAlgorithm($orderdetails, $storeunits);
    $weightAlgorithm = $this->runWeightPackingAlgorithm($orderdetails, $storeunits);

    // 🔢 Statystyki zamówienia
    $totalItems = $orderdetails->sum('quantity');
    $uniqueProducts = $orderdetails->pluck('product_id')->unique()->count();

    $totalVolume = 0;
    $totalWeight = 0;
    foreach ($orderdetails as $detail) {
        $product = $detail->product;
        if ($product && $product->size_x && $product->size_y && $product->size_z && $product->weight) {
            $volume = $product->size_x * $product->size_y * $product->size_z * $detail->quantity;
            $totalVolume += $volume;
            $totalWeight += $product->weight * $detail->quantity;
        }
    }
    $totalVolume = $totalVolume / 1000000; // cm³ -> m³

    // 📦 Statystyki opakowań
    $volumeUsed = array_sum(array_column($volumeAlgorithm, 'volume_used')) / 1000000;
    $weightUsed = array_sum(array_column($volumeAlgorithm, 'weight_used'));

    $volumeCapacity = 0;
    $weightCapacity = 0;
    $unitsUsedCount = count($volumeAlgorithm);
    $usedUnits = collect();

    foreach ($volumeAlgorithm as $entry) {
        $unit = $storeunits->firstWhere('id', $entry['storeunit_id']);
        if ($unit && $unit->storeunittype) {
            $usedUnits->push($unit);
            $t = $unit->storeunittype;
            $volumeCapacity += ($t->size_x * $t->size_y * $t->size_z) / 1000000;
            $weightCapacity += $t->loadwgt ?? 0;
        }
    }

    $volumeFillPercent = $volumeCapacity > 0 ? round(($volumeUsed / $volumeCapacity) * 100, 1) : 0;
    $weightFillPercent = $weightCapacity > 0 ? round(($weightUsed / $weightCapacity) * 100, 1) : 0;

    $usedVolumeTotal = $usedUnits->sum(function ($unit) {
        $t = $unit->storeunittype;
        return $t && $t->size_x && $t->size_y && $t->size_z
            ? ($t->size_x * $t->size_y * $t->size_z) / 1000000
            : 0;
    });


    return view('orderdetail.autopick', compact(
        'order',
        'orderdetails',
        'heaviestDetail',
        'volumeAlgorithm',
        'weightAlgorithm',
        'storeunits',
        'totalItems',
        'uniqueProducts',
        'totalVolume',
        'totalWeight',
        'volumeUsed',
        'weightUsed',
        'volumeCapacity',
        'weightCapacity',
        'unitsUsedCount',
        'usedUnits',
        'volumeFillPercent',
        'weightFillPercent',
        'usedVolumeTotal'
    ));
}


private function runVolumePackingAlgorithm($orderdetails, $storeunits)
{
    $result = [];
    $remaining = $orderdetails->map(function ($item) {
        return (object) [
            'detail' => $item,
            'remaining_qty' => $item->quantity,
        ];
    })->values();

    $storeunits = $storeunits->sortBy(function ($unit) {
        return ($unit->storeunittype->size_x ?? 0) *
               ($unit->storeunittype->size_y ?? 0) *
               ($unit->storeunittype->size_z ?? 0);
    });

    foreach ($storeunits as $unit) {
        $unitVolume = ($unit->storeunittype->size_x ?? 0) *
                      ($unit->storeunittype->size_y ?? 0) *
                      ($unit->storeunittype->size_z ?? 0);
        $unitWeightLimit = $unit->storeunittype->loadwgt ?? INF;

        $packed = [];
        $usedVolume = 0;
        $usedWeight = 0;

        foreach ($remaining as $item) {
            $product = $item->detail->product;
            $qty = $item->remaining_qty;

            $productVolume = ($product->size_x ?? 0) * ($product->size_y ?? 0) * ($product->size_z ?? 0);
            $productWeight = $product->weight ?? 0;

            if ($productVolume == 0 || $productWeight == 0) {
                continue;
            }

            $maxQty = min(
                floor(($unitVolume - $usedVolume) / $productVolume),
                floor(($unitWeightLimit - $usedWeight) / $productWeight),
                $qty
            );

            if ($maxQty > 0) {
                $packed[] = [
                    'detail_id' => $item->detail->id,
                    'product_id' => $product->id,
                    'quantity' => $maxQty,
                ];

                $usedVolume += $productVolume * $maxQty;
                $usedWeight += $productWeight * $maxQty;
                $item->remaining_qty -= $maxQty;
            }
        }

        if (!empty($packed)) {
            $result[] = [
                'storeunit_id' => $unit->id,
                'products' => $packed,
                'volume_used' => $usedVolume,
                'weight_used' => $usedWeight,
            ];
        }
    }

    return $result;
}

private function runWeightPackingAlgorithm($orderdetails, $storeunits)
{
    $result = [];
    $remaining = $orderdetails->map(function ($item) {
        return (object) [
            'detail' => $item,
            'remaining_qty' => $item->quantity,
        ];
    })->values();

    $storeunits = $storeunits->sortBy(function ($unit) {
        return $unit->storeunittype->loadwgt ?? INF;
    });

    foreach ($storeunits as $unit) {
        $unitVolume = ($unit->storeunittype->size_x ?? 0) *
                      ($unit->storeunittype->size_y ?? 0) *
                      ($unit->storeunittype->size_z ?? 0);
        $unitWeightLimit = $unit->storeunittype->loadwgt ?? INF;

        $packed = [];
        $usedVolume = 0;
        $usedWeight = 0;

        foreach ($remaining as $item) {
            $product = $item->detail->product;
            $qty = $item->remaining_qty;

            $productVolume = ($product->size_x ?? 0) * ($product->size_y ?? 0) * ($product->size_z ?? 0);
            $productWeight = $product->weight ?? 0;

            if ($productVolume == 0 || $productWeight == 0) {
                continue;
            }

            $maxQty = min(
                floor(($unitVolume - $usedVolume) / $productVolume),
                floor(($unitWeightLimit - $usedWeight) / $productWeight),
                $qty
            );

            if ($maxQty > 0) {
                $packed[] = [
                    'detail_id' => $item->detail->id,
                    'product_id' => $product->id,
                    'quantity' => $maxQty,
                ];

                $usedVolume += $productVolume * $maxQty;
                $usedWeight += $productWeight * $maxQty;
                $item->remaining_qty -= $maxQty;
            }
        }

        if (!empty($packed)) {
            $result[] = [
                'storeunit_id' => $unit->id,
                'products' => $packed,
                'volume_used' => $usedVolume,
                'weight_used' => $usedWeight,
            ];
        }
    }

    return $result;
}





// public function confirm(Order $order)
// {
//     OrderPickAuto::where('order_id', $order->id)->update(['confirmed' => true]);
//     //dd(OrderPickAuto::where('order_id', $order->id)->get());
//     return redirect()->route('orders.index')->with('success', 'Kompletacja została zatwierdzona.');
// }

// public function indexConfirmed()
// {
//     $orders = OrderPickAuto::where('confirmed', true)
//         ->select('order_id')
//         ->distinct()
//         ->with('order')
//         ->get();

//     return view('orderdetail.confirmed.index', compact('orders'));
// }

// public function showConfirmed(Order $order)
// {
//     $picks = OrderPickAuto::where('order_id', $order->id)
//         ->where('confirmed', true)
//         ->with('storeunit.storeunittype')
//         ->get();

//     return view('orderdetail.confirmed.show', compact('order', 'picks'));
// }

}
