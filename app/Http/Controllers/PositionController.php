<?php

namespace App\Http\Controllers;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        //$position = Position::paginate();
        return view('position.index');
    }

    public function create()
    {
        return view('position.create');
    }
}
