<?php

namespace App\Http\Controllers;
use App\Models\ProductSet;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductSetController extends Controller
{
    public function index()
    {
        return view('productsets.index');
    }

    public function create()
    {
        $products = Product::all(); // lub paginacja, jeśli jest ich dużo
        return view('productsets.create', compact('products'));
    }

}
