<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate();
        $userCount = User::count();

        return view('users.index', compact('users','userCount'));
    }
}
