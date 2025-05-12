<?php

namespace App\Http\Controllers;
use App\Models\Position;
use App\Models\Product;
use App\Models\StoreUnit;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        //$position = Position::paginate();
        return view('position.index');
    }

    public function create()
    {
        $products = Product::all();
        $storeunits = StoreUnit::all();

        return view('position.create', ['products' => Product::all(), 'storeunits' => StoreUnit::all(),]);
    }

    public function store()
    {
        $position = Position::create([
    'name' => $validated['name'],
    'storeunit_id' => $validated['storeunit_id'],
    'picklist_id' => $request->input('picklist_id') // jeśli jest
]);

    }

    public function createFromPick($id)
{
    // Pobieramy produkty z tej kompletacji
    $orderDetails = OrderDetail::where('picklist_id', $id)->get();

    // Wyciągamy unikalne produkty z kompletacji
    $productIds = $orderDetails->pluck('product_id')->unique();
    $products = Product::whereIn('id', $productIds)->get();

    // Lista dostępnych opakowań
    $storeunits = StoreUnitType::all();

    // Opcjonalnie: możesz przekazać `picklist_id` do późniejszego zapisu
    return view('position.create', compact('products', 'storeunits', 'id'));
}
}
