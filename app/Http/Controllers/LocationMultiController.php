<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\StoreArea;
use App\Models\Status;
use Locale;

class LocationMultiController extends Controller
{
    public function create()
    {
        return view('locations.createmulti',['store_areas' => StoreArea::all(),
                                        'status' => Status::getObject('LOC')]);
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'w1'=> 'required|integer',
            'w2'=> 'required|integer',
            'w3'=> 'required|integer',
            'od_1'=> 'required|Integer',
            'od_2'=> 'required|Integer',
            'od_3'=> 'required|Integer',
            'do_1'=> 'required|Integer',
            'do_2'=> 'required|Integer',
            'do_3'=> 'required|Integer',
            'size_x' => 'required|Decimal:2',
            'size_y' => 'required|Decimal:2',
            'size_z' => 'required|Decimal:2',
            'loadwgt' => 'required|Decimal:2',
            'storearea_id' => 'required',
            'status_id' => 'required'
        ]);

        $w1   = $validatedAttributes['w1'];
        $w2   = $validatedAttributes['w2'];
        $w3   = $validatedAttributes['w3'];
        $od_1 = $validatedAttributes['od_1'];
        $od_2 = $validatedAttributes['od_2'];
        $od_3 = $validatedAttributes['od_3'];
        $do_1 = $validatedAttributes['do_1'];
        $do_2 = $validatedAttributes['do_2'];
        $do_3 = $validatedAttributes['do_3'];

        $counter = 0;

        for ($i = $od_1; $i <= $do_1; $i++) {
            for ($j = $od_2; $j <= $do_2; $j++) {
                for ($k = $od_3; $k <= $do_3; $k++) {
                    $ean = str_pad($i, $w1, '0', STR_PAD_LEFT).'-'.str_pad($j, $w2, '0', STR_PAD_LEFT).'-'.str_pad($k, $w3, '0', STR_PAD_LEFT);

                    Location::create([
                        'ean' => $ean,
                        'pos_x' => $i,
                        'pos_y' => $j,
                        'pos_z' => $k,
                        'storearea_id' => $validatedAttributes['storearea_id'],
                        'size_x' => $validatedAttributes['size_x'],
                        'size_y' => $validatedAttributes['size_y'],
                        'size_z' => $validatedAttributes['size_z'],
                        'loadwgt' => $validatedAttributes['loadwgt'],
                        'status_id' => $validatedAttributes['status_id']
                    ]);
                    $counter ++;
                }
            }
        }
        return redirect()->route('locations.index')->with('success', 'Wygenerowane '.$counter.' nowych miejsc !');
    }
}
