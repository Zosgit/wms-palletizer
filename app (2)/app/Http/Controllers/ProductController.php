<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductMetric;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderby('updated_at','desc')->paginate(100);
        $productCount = Product::count();

        return view('products.index', compact('products','productCount'));
    }

    public function create()
    {
        return view('products.create',['product_types' => ProductType::all(),
                                    'product_metrics'=>ProductMetric::all()]);
    }

    public function store(Request $request)
    {
         $validatedAttributes = $request->validate ([
            'code' => 'required|unique:products|max:50',
            'longdesc' => 'required|string|max:190',
            'producttype_id' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'weight' => 'required|Decimal:2',
            'metric_id' => 'required',
            'ean' => 'required'
        ]);
        $validatedAttributes['shipment'] = $request->get('shipment') == 'on' ? 1 : 0;
        $validatedAttributes['delivery'] = $request->get('delivery') == 'on' ? 1 : 0;

        Product::create($validatedAttributes);
        //dd($validatedAttributes);
        //Product::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('products.index')->with('success', 'Produkt dodany poprawnie!');
    }

    public function edit(Product $product)
    {
       // dd($products = Product::find($id));
        return view('products.edit', ['product' => $product,
                                    'product_types' => ProductType::all(),
                                    'product_metrics'=>ProductMetric::all()]);
    }

    public function update(Product $product, Request $request)
    {
       // dd($request);
        $validatedAttributes = $request->validate ([
            'longdesc' => 'required|string|max:190',
            'producttype_id' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'weight' => 'required|Decimal:2',
            'metric_id' => 'required',
            'ean' => 'required'
        ]);

        $validatedAttributes['shipment'] = $request->get('shipment') == 'on' ? 1 : 0;
        $validatedAttributes['delivery'] = $request->get('delivery') == 'on' ? 1 : 0;
        //dd($request);

        $product->update($validatedAttributes);
        return redirect()->route('products.index')->with('success', 'Produkt edytowa poprawnie!');

    }
    // należy dodać wpis sprawdzający czy asortyment jest na zapasie.
    //jeżeli jest to komunikat i nie usuwamy !!!
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produkt usunięty');
    }

    public function show($id)
    {
        return redirect()->route('products.index');
    }

}
