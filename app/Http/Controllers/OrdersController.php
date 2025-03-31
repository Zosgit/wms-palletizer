<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Counter;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{

    public function index(Request $request)
    {

        if ( $request->type != null)
            {
                if ($request->search != null and $request->type != null) // mamy rodzaj + wartość
                    {
                        $orders = DB::table('v_orders')->where($request->type,'LIKE','%'.$request->search.'%')->orderby('updated_at','desc')->paginate(100);
                    }
                else
                    {
                        // szukamy pustych
                        $orders = DB::table('v_orders')->wherenull($request->type,'like',$request->search)->orderby('updated_at','desc')->paginate(100);
                    }
            }
        else // pokazuje wszystko
            {
                $orders = DB::table('v_orders')->orderby('updated_at','desc')->paginate(100);
            }

        return view('orders.index', compact('orders'));
    }


    public function create()
    {
        return view('orders.create',['status' => Status::getObject('ORDER'),
                                        'firms'=>Firm::getDelivery(),
                                        'owners'=>Firm::getOwner(),
                                        'locations'=>Location::getLocationDelivery()
                                    ]);
    }


    public function store(Request $request)
    {
        $validatedAttributes = $request->validate ([
            'external_nr' => 'required|max:50',
            'firm_id' => 'required',
            'owner_id' => 'required',
            'location_id' => 'required',
            'remarks' => '',

        ]);

        $location_id = $validatedAttributes['location_id'];

        $validatedAttributes['order_nr'] = Counter::getNumber('ORDER');

        $order = Order::create($validatedAttributes);

        Location::SetStatus($location_id,204,'Blokuję miejsce dla wydania: '.$validatedAttributes['order_nr']);

        return redirect()->route('orderdetail.index', ['id' => $order->id]);
    }


    public function show(Shipment $shipment)
    {
        dd($shipment);
        return view('shipments.index');
    }


    public function edit(Shipment $shipment)
    {
        //
    }


    public function update(Request $request, Shipment $shipment)
    {
        //
    }


    public function destroy(Shipment $shipment)
    {
        //
    }

}
