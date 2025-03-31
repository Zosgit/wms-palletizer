<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Firm;

class FirmController extends Controller
{
    public function index()
    {
        $firms = Firm::paginate();
        return view('firms.index', compact('firms'));
    }

    public function create()
    {
        return view('firms.create');
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'code' => 'required|unique:products|max:50',
            'longdesc' => 'required',
            'tax' => 'required',
            'street' => 'required',
            'postcode' => 'required',
            'city' => 'required',
            'notes' => 'required'
        ]);
        $validatedAttributes['shipment'] = $request->get('shipment') == 'on' ? 1 : 0;
        $validatedAttributes['delivery'] = $request->get('delivery') == 'on' ? 1 : 0;
        $validatedAttributes['owner'] = $request->get('owner') == 'on' ? 1 : 0;

       // dd($validatedAttributes);
       Firm::create($validatedAttributes);
       //Firm::updateOrCreate(['id' => $request->id], $request->except('id'));
        return redirect()->route('firms.index')->with('success', 'Kontrahent dodany poprawnie!');
    }

    public function edit(Firm $firm)
    {
       // dd($products = Product::find($id));
        return view('firms.edit', compact('firm'));
    }

    public function update(Firm $firm, Request $request)
    {
       // dd($request);
        $validatedAttributes = $request->validate ([
            'longdesc' => 'required',
            'tax' => 'required',
            'street' => 'required',
            'postcode' => 'required',
            'city' => 'required',
            'notes' => 'required'
        ]);
        $validatedAttributes['shipment'] = $request->get('shipment') == 'on' ? 1 : 0;
        $validatedAttributes['delivery'] = $request->get('delivery') == 'on' ? 1 : 0;
        $validatedAttributes['owner'] = $request->get('owner') == 'on' ? 1 : 0;

        $firm->update($validatedAttributes);
        return redirect()->route('firms.index')->with('success', 'Kontrahent edytowany poprawnie');

    }

    public function destroy(Firm $firm)
    {
        $firm->delete();
        return redirect()->route('firms.index')->with('success', 'Kontrahent usunięty');
    }
}
