<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ProductMetric;

class ProductMetricController extends Controller
{
    public function index()
    {
        $productmetrics = ProductMetric::orderby('code')->paginate();
        return view('productmetrics.index', compact('productmetrics'));
    }

    public function create()
    {
        return view('productmetrics.create');
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'code' => 'required|unique:products|max:50',
            'longdesc' => 'required',
            'amount' => 'required'
        ]);

        //dd($request);
        ProductMetric::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('productmetrics.index')->with('success', 'Jednostka dodana poprawnie');
    }

    public function edit(ProductMetric $productmetric)
    {
       // dd($products = Product::find($id));
        return view('productmetrics.edit', compact('productmetric'));
    }

    public function update(ProductMetric $productmetric, Request $request)
    {
       // dd($request);
        $validatedAttributes = $request->validate ([
            'longdesc' => 'required',
            'amount' => 'required'
        ]);

        $productmetric->update($validatedAttributes);
        return redirect()->route('productmetrics.index')->with('success', 'Jednostka edytowana poprawnie');

    }

    public function destroy(ProductMetric $productmetric)
    {
        $productmetric->delete();
        return redirect()->route('productmetrics.index')->with('success', 'Jednostka usunięta');
    }
}
