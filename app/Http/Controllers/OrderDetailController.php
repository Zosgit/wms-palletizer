<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Status;
use App\Models\Firm;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    public function index($id)
    {
        $order = Order::findorfail($id);
        $status_id = $order->status_id;
        $order_id = $order->id;
        $owner_id = $order->owner_id;

        $orderdetails = OrderDetail::where('order_id',$id)->paginate(100);

        if ($status_id == 502)
        {
            return view('orderdetail.index',compact('order','orderdetails'));
        }

        return view('orderdetail.index',compact('order','orderdetails'));

    }

    public function create($id ,Request $request)
    {
        $order = Order::findorfail($id);
        $owner_id = $order->owner_id;


        if ($request->search != null)
        {
            $stocks = DB::table('v_reservation')->where('prod_code','like','%'.$request->search.'%')
                                            ->where('owner_id',$owner_id)
                                            ->where('sum_stock','>',0)
                                            ->where('status_id',302)->paginate(5000);
        }
        else
        {
            $stocks = DB::table('v_reservation')->where('owner_id',$owner_id)
                                                ->where('sum_stock','>',0)
                                                ->where('status_id',302)->paginate(5000);
        }

        return view('orderdetail.create',compact('order','stocks'));

    }

    public function save(Request $request)
    {

        $validatedAttributes = $request->validate([
            'order_id'          => 'required',
            'quantity'          => 'required',
            'logical_area_id'   => 'required',
            'product_id'        => 'required',
            'prod_code'         => 'required',
            'prod_desc'         => 'required'
        ]);

        OrderDetail::create($validatedAttributes);

        return redirect()->route('orderdetail.create',['id'=> $request->order_id])->with('success', 'Produkt dodany poprawnie!');


    }
    public function distroy($id)
    {

    }

    // wysyłam do kompletacji
    public function sendpick($id)
    {
        $order = Order::findorfail($id);
        $order->status_id = 503;
        $order->save();
        return redirect()->route('orders.index')->with('success', 'Dokument przekazany do kompletacji');
    }
}
