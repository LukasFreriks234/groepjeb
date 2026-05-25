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

        if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('images'), $filename);

        $function->image = 'images/' . $filename;
    }
        
        $function->update([
            'name' => $request->name,
            'category' => $request->category,
            'image' => $function->image,
        ]);

        $request->validate([
            'Safety'                => 'required|numeric|between:-10,10',
            'Recreation'            => 'required|numeric|between:-10,10',
            'Environmental_Quality' => 'required|numeric|between:-10,10', 
            'Services'              => 'required|numeric|between:-10,10',
            'Mobility'              => 'required|numeric|between:-10,10',
        ]);

        $function->effects()->update([
            'Safety'                 => $request->Safety,
            'Recreation'             => $request->Recreation,
            'Environmental Quality'  => $request->Environmental_Quality, 
            'Services'               => $request->Services,
            'Mobility'               => $request->Mobility,
        ]);

        return redirect()->route('functions.show', $function->id);
    }
}
