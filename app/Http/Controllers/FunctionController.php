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
        $function = Functions::with('effects')->findOrFail($id);
        $effect = Effects::find($id);

        return view('Functions.show', compact('function', 'effect'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('Functions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:functions,name',
            'category' => 'required|exists:categories,category',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'Safety' => 'required|numeric|between:-10,10',
            'Recreation' => 'required|numeric|between:-10,10',
            'Environmental_Quality' => 'required|numeric|between:-10,10',
            'Services' => 'required|numeric|between:-10,10',
            'Mobility' => 'required|numeric|between:-10,10',
        ]);

        $imageName = $request->file('image')->hashName();
        $imageFolder = public_path('images/functions');

        if (!is_dir($imageFolder)) {
            mkdir($imageFolder, 0755, true);
        }

        $request->file('image')->move($imageFolder, $imageName);

        $imagePath = 'images/functions/' . $imageName;

        $function = Functions::create([
            'name' => $request->name,
            'category' => $request->category,
            'image' => $imagePath,
        ]);

        Effects::create([
            'id' => $function->id,
            'Safety' => $request->Safety,
            'Recreation' => $request->Recreation,
            'Environmental Quality' => $request->Environmental_Quality,
            'Services' => $request->Services,
            'Mobility' => $request->Mobility,
        ]);

        return redirect()->route('functions.index');
    }

    public function edit($id)
    {
        $function = Functions::with('effects')->findOrFail($id);
        $categories = Category::all();

        return view('Functions.edit', compact('function', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $function = Functions::with('effects')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:functions,name,' . $id,
            'category' => 'required|exists:categories,category',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'Safety' => 'required|numeric|between:-10,10',
            'Recreation' => 'required|numeric|between:-10,10',
            'Environmental_Quality' => 'required|numeric|between:-10,10',
            'Services' => 'required|numeric|between:-10,10',
            'Mobility' => 'required|numeric|between:-10,10',
        ]);

        $functionData = [
            'name' => $request->name,
            'category' => $request->category,
        ];

        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->hashName();
            $imageFolder = public_path('images/functions');

            if (!is_dir($imageFolder)) {
                mkdir($imageFolder, 0755, true);
            }

            $request->file('image')->move($imageFolder, $imageName);

            $functionData['image'] = 'images/functions/' . $imageName;
        }

        $function->update($functionData);

        $function->effects()->update([
            'Safety' => $request->Safety,
            'Recreation' => $request->Recreation,
            'Environmental Quality' => $request->Environmental_Quality,
            'Services' => $request->Services,
            'Mobility' => $request->Mobility,
        ]);

        return redirect()->route('functions.show', $function->id);
    }
}