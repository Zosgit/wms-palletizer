<?php

namespace App\Http\Controllers;
use App\Models\StoreUnitType;
use Illuminate\Http\Request;

class StoreUnitTypeController extends Controller
{
    public function index()
    {
        $storeunittypes = StoreUnitType::paginate();
        $storeunittypeCount = StoreUnitType::count();

        return view('storeunittypes.index', compact('storeunittypes','storeunittypeCount'));
    }

    public function create()
    {
        return view('storeunittypes.create');
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'code' => 'required|unique:store_unit_types|max:50',
            'prefix' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'loadwgt' => 'required|Decimal:2',
            'suwgt' => 'required|Decimal:2'
        ]);

        //StoreUnitType::create($validatedAttributes);

        //dd($request);
        StoreUnitType::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('storeunittypes.index')->with('success', 'opakowanie dodane poprawnie!');
    }

    public function edit(StoreUnitType $storeunittype)
    {
       // dd($products = Product::find($id));
       return view('storeunittypes.edit', compact('storeunittype'));
    }

    public function update(StoreUnitType $storeunittype, Request $request)
    {
       // dd($request);
        $validatedAttributes = $request->validate ([
            'prefix' => 'required',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'loadwgt' => 'required|Decimal:2',
            'suwgt' => 'required|Decimal:2'
        ]);

        //dd($request);

        $product->update($validatedAttributes);
        return redirect()->route('storeunittypes.index')->with('success', 'Opakowanie edytowe poprawnie!');

    }

    public function destroy(StoreUnitType $storeunittype)
    {
        $storeunittype->delete();
        return redirect()->route('storeunittypes.index')->with('success', 'Opakowanie usunięte');
    }
}

