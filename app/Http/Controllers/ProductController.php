<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductMetric;
use DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $products = DB::table('v_products')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','desc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $products = DB::table('v_products')->wherenull($request->type)->orderby('updated_at','desc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $products = DB::table('v_products')->orderby('updated_at','desc')->paginate(100);
            }

        return view('products.index', compact('products'));
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
            'material_type' => 'required',
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

    public function edit($id)
    {
        $product = Product::findorfail($id);

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
            'material_type' => 'required',
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
    public function destroy($id)
    {
        $product = product::findorfail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produkt usunięty');
    }


}
