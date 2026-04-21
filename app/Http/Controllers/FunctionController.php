<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FunctionController extends Controller
{
    public function index()
    {
        // ! USER = TABLE, # = VIEW
        $functions = User::all();
        return view('#', compact('functions'));
    }
}
