<?php

namespace App\Http\Controllers;
use App\Models\ProductSet;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductSetController extends Controller
{
    public function index()
    {
        $sets = ProductSet::with('products')->paginate(50);
        return view('productsets.index', compact('sets'));
    }

    public function create()
    {
        $products = Product::all();
        return view('productsets.create', compact('products'));
    }

    public function store(Request $request)
{
    // Walidacja danych
    $validatedAttributes = $request->validate([
        'code' => 'required|string|max:100|unique:product_sets,code',
        'products' => 'required|array',
        'products.*' => 'exists:products,id',
    ]);

    // Tworzenie nowego kompletu
    $set = ProductSet::create([
        'code' => $validatedAttributes['code']
    ]);
    //dd($validatedAttributes);
    // Przypisanie produktów do kompletu (relacja wiele-do-wielu)
    $set->products()->attach($validatedAttributes['products']);

    // Przekierowanie z komunikatem
    return redirect()->route('productsets.index')->with('success', 'Komplet został dodany poprawnie!');
}


    // Opcjonalnie: podgląd konkretnego kompletu
    public function show(ProductSet $productset)
    {
        $productset->load('products');
        return view('productsets.show', compact('productset'));
    }

    // Opcjonalnie: usuwanie kompletu
    // public function destroy(ProductSet $productset)
    // {
    //     $productset->products()->detach();
    //     $productset->delete();
    //     return redirect()->route('productsets.index')->with('success', 'Komplet został usunięty.');
    // }
}
