<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Counter;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function reportstock(Request $request)
    {
        $stocks ='';
        $start = '';

        if ( $request->type != null)
            {
                $start = '1';

                if ($request->type == 'all') // mamy rodzaj
                    {
                        $stocks = DB::table('v_stock_group')->paginate(5000);
                    }
                else
                    {
                        $stocks = DB::table('v_stock')->paginate(5000);
                    }
            }
        $report = $request->type;

        return view('report.stock', compact('stocks','start','report'));
    }


    public function reportproduct($id, Request $request)
    {
        $product = Product::findorfail($id);
        $products = DB::table('v_product')->where('product_id',$id)->paginate(5000);
        return view('report.product', compact('product','products'));
    }


}
