<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;

class FunctionController extends Controller
{
    public function edit(){
        $functions = Functions::with('effects')->get();

        return view('Functions.edit', compact('functions'));
    }
}
