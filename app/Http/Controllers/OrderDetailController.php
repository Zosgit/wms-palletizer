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
use App\Models\OrderPickAutoProduct;
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

// 1. Maksymalne wymiary dostępnych opakowań
$maxUnitSize = [
    'x' => 0,
    'y' => 0,
    'z' => 0,
];

foreach ($storeunits as $unit) {
    $type = $unit->storeunittype;
    if ($type) {
        $maxUnitSize['x'] = max($maxUnitSize['x'], $type->size_x);
        $maxUnitSize['y'] = max($maxUnitSize['y'], $type->size_y);
        $maxUnitSize['z'] = max($maxUnitSize['z'], $type->size_z);
    }
}

    // 🔢 Statystyki zamówienia
    $totalItems = $orderdetails->sum('quantity');
    $uniqueProducts = $orderdetails->pluck('product_id')->unique()->count();

    $totalVolume = 0;
    $totalWeight = 0;
    $trimmedProducts = [];


foreach ($orderdetails as $detail) {
    $product = $detail->product;

    if ($product && $product->size_x && $product->size_y && $product->size_z && $product->weight) {
        $originalVolume = $product->size_x * $product->size_y * $product->size_z * $detail->quantity;
        $totalVolume += $originalVolume;
        $totalWeight += $product->weight * $detail->quantity;

        if ($product->can_overhang == 1 &&
            ($product->size_x > $maxUnitSize['x'] ||
             $product->size_y > $maxUnitSize['y'] ||
             $product->size_z > $maxUnitSize['z'])) {

            $cut_x = min($product->size_x, $maxUnitSize['x']);
            $cut_y = min($product->size_y, $maxUnitSize['y']);
            $cut_z = min($product->size_z, $maxUnitSize['z']);

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
}
$totalVolume = $totalVolume / 1_000_000; // cm³ → m³

// 3. Redukcja objętości przez przycięcie
$reducedVolumeCount = count($trimmedProducts);
$reducedVolumeAmount = 0;

foreach ($trimmedProducts as $item) {
    $full = $item['original']['x'] * $item['original']['y'] * $item['original']['z'];
    $cut  = $item['trimmed']['x'] * $item['trimmed']['y'] * $item['trimmed']['z'];
    $reducedVolumeAmount += ($full - $cut);
}
$reducedVolumeAmount = round($reducedVolumeAmount / 1_000_000, 4); // cm³ → m³

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
        'usedVolumeTotal',
        'reducedVolumeAmount',
        'reducedVolumeCount',
        'trimmedProducts'
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
        $maxX = $unit->storeunittype->size_x ?? 0;

        $packed = [];
        $usedVolume = 0;
        $usedWeight = 0;

        foreach ($remaining as $item) {
            $product = $item->detail->product;
            $qty = $item->remaining_qty;

            $size_x = $product->size_x ?? 0;
            $size_y = $product->size_y ?? 0;
            $size_z = $product->size_z ?? 0;
            $productWeight = $product->weight ?? 0;

            if ($product->can_overhang === 'tak') {
                $size_x = min($size_x, $maxX);
            }

            if ($size_x == 0 || $size_y == 0 || $size_z == 0 || $productWeight == 0) {
                continue;
            }

            $productVolume = $size_x * $size_y * $size_z;

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






public function confirm(Order $order)
{
    OrderPickAuto::where('order_id', $order->id)->update(['confirmed' => true]);
    //dd(OrderPickAuto::where('order_id', $order->id)->get());
    return redirect()->route('orders.index')->with('success', 'Kompletacja została zatwierdzona.');
}

public function indexConfirmed()
{
    $orders = OrderPickAuto::where('confirmed', true)
    ->select('order_id')
    ->distinct()
    ->with('order')
    ->orderByDesc('order_id')
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

public function storeConfirmedPacking(Request $request, Order $order)
{
    $choice = $request->input('algorithm_choice');

    if (!in_array($choice, ['volume', 'weight'])) {
        return back()->with('error', 'Nie wybrano algorytmu.');
    }

    $dataJson = $choice === 'volume'
        ? $request->input('volume_algorithm')
        : $request->input('weight_algorithm');

    $data = json_decode(html_entity_decode($dataJson), true);

    if (!is_array($data)) {
        return back()->with('error', 'Dane algorytmu są niepoprawne.');
    }

    // 🧹 USUŃ STARE KOMPLETACJE
    OrderPickAuto::where('order_id', $order->id)->delete();

    foreach ($data as $entry) {
        if (!isset($entry['storeunit_id'])) continue;

        $storeUnit = StoreUnit::find($entry['storeunit_id']);
        if (!$storeUnit) continue;

        $orderPickAuto = OrderPickAuto::create([
            'order_id' => $order->id,
            'store_unit_id' => $storeUnit->id, // klucz zgodny z DB
            'used_volume' => $entry['volume_used'] ?? 0,
            'used_weight' => $entry['weight_used'] ?? 0,
            'confirmed' => true,
            'algorithm_type' => $choice,
        ]);

        if (!empty($entry['products'])) {
            foreach ($entry['products'] as $p) {
                $detail = OrderDetail::find($p['detail_id'] ?? null);
                if ($detail) {
                    OrderPickAutoProduct::create([
                        'order_pick_auto_id' => $orderPickAuto->id,
                        'product_id' => $detail->product_id,
                        'quantity' => $p['quantity'] ?? 1,
                    ]);
                }
            }
        }
    }

    return redirect()->route('orders.index')->with('success', 'Kompletacja została zapisana.');
}


}
