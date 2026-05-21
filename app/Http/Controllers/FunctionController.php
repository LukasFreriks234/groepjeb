<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;

class FunctionController extends Controller
{
    public function edit($id)
    {
        $function = Functions::with('effects')->findOrFail($id);
        $categories = Category::all();

        return view('Functions.edit', compact('function', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $function = Functions::with('effects')->findOrFail($id);
        
        $function->update([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        $request->validate([
            'Safety'                => 'required|numeric',
            'Recreation'            => 'required|numeric',
            'Environmental_Quality' => 'required|numeric', 
            'Services'              => 'required|numeric',
            'Mobility'              => 'required|numeric',
        ]);

        $function->effects->update([
            'Safety'                 => $request->Safety,
            'Recreation'             => $request->Recreation,
            'Environmental Quality'  => $request->Environmental_Quality, 
            'Services'               => $request->Services,
            'Mobility'               => $request->Mobility,
        ]);

        return redirect()->route('functions.show', $function->id);
    }
}
