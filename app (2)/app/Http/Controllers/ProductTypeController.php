<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ProductType;


class ProductTypeController extends Controller
{
    public function index()
    {
        $producttypes = ProductType::paginate();
        $producttypeCount = ProductType::count();

        return view('producttypes.index', compact('producttypes', 'producttypeCount'));
    }

    public function create()
    {
        return view('producttypes.create');
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'code' => 'required|unique:products|max:50'
        ]);

        //dd($request);
        ProductType::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('producttypes.index')->with('success', 'Kategoria produktu dodana poprawnie');
    }

    public function destroy(ProductType $producttype)
    {
        $producttype->delete();
        return redirect()->route('producttypes.index')->with('success', 'Kategoria produktu usunięta');
    }
}
