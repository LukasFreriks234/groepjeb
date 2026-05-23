<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;

class FunctionController extends Controller
{

    public function index()
    {
        $functions = Functions::all();
        $categories = Category::all();

        return view('Functions.index', compact('functions', 'categories'));
    }

    public function show($id)
    {
        $function = Functions::with('Effects')->findorFail($id);
        $effect = Effects::findOrFail($id);
        return view('Functions.show', [
            'function' => $function,
            'effect' => $effect
        ]);
    }
    public function edit($id)
    {
        $function = Functions::with('effects')->findOrFail($id);

        return view('Functions.edit', compact('function'));
    }

    public function update(Request $request, $id)
    {

        $function = Functions::with('effects')->findOrFail($id);

        $function->update([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        $request->validate([
            'Safety' => 'nullable|numeric',
            'Recreation' => 'nullable|numeric',
            'Environmental Quality' => 'nullable|numeric',
            'Services' => 'nullable|numeric',
            'Mobility' => 'nullable|numeric',
        ]);

        $function->effects->update([
            'Safety' => $request->Safety,
            'Recreation' => $request->Recreation,
            'Environmental Quality' => $request->input('Environmental Quality'),
            'Services' => $request->Services,
            'Mobility' => $request->Mobility,
        ]);



        return redirect()->route('functions.show', $function->id);
    }
}
