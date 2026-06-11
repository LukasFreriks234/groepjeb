<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FunctionEdited;
use App\Notifications\FunctionCreated;
use Illuminate\Support\Facades\Auth;

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
        $function = Functions::with(['effects', 'relatedFunction'])->findOrFail($id);
        $effect = Effects::find($id);

        return view('Functions.show', compact('function', 'effect'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $categories = Category::all();
        $functions = Functions::all();
        $deletedFunctions = Functions::onlyTrashed()->get();

        return view('Functions.create', compact('categories', 'functions', 'deletedFunctions'));
    }

    public function restore(Request $request)
    {
       if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate(['deleted_function' => 'required|exists:functions,id']);

        $function = Functions::onlyTrashed()->findOrFail($request->deleted_function);

        $function->restore();

        return redirect()->route('functions.index');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:functions,name',
            'category' => 'required|exists:categories,category',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

            'Safety' => 'required|numeric|between:-10,10',
            'Recreation' => 'required|numeric|between:-10,10',
            'Environmental_Quality' => 'required|numeric|between:-10,10',
            'Services' => 'required|numeric|between:-10,10',
            'Mobility' => 'required|numeric|between:-10,10',

            'related_function' => 'nullable|exists:functions,id',
            'relationship_safety' => 'required_with:related_function|nullable|integer|between:-10,10',
            'relationship_recreation' => 'required_with:related_function|nullable|integer|between:-10,10',
            'relationship_environmental' => 'required_with:related_function|nullable|integer|between:-10,10',
            'relationship_services' => 'required_with:related_function|nullable|integer|between:-10,10',
            'relationship_mobility' => 'required_with:related_function|nullable|integer|between:-10,10',
        ]);

        $imagePath = $this->saveUploadedFunctionImage($request);

        $hasRelationship = !empty($request->related_function);

        $function = Functions::create([
            'name' => $request->name,
            'category' => $request->category,
            'image' => $imagePath,

            'related_function_id' => $hasRelationship ? $request->related_function : null,
            'relationship_safety' => $hasRelationship ? $request->relationship_safety : 0,
            'relationship_recreation' => $hasRelationship ? $request->relationship_recreation : 0,
            'relationship_environmental' => $hasRelationship ? $request->relationship_environmental : 0,
            'relationship_services' => $hasRelationship ? $request->relationship_services : 0,
            'relationship_mobility' => $hasRelationship ? $request->relationship_mobility : 0,
        ]);

        Effects::create([
            'id' => $function->id,
            'Safety' => $request->Safety,
            'Recreation' => $request->Recreation,
            'Environmental Quality' => $request->Environmental_Quality,
            'Services' => $request->Services,
            'Mobility' => $request->Mobility,
        ]);

        Notification::send(Auth::user(), new FunctionCreated($function));

        return redirect()->route('functions.index');
    }

    public function edit($id)
    {

        $function = Functions::with('effects')->findOrFail($id);
        $categories = Category::all();
        $functions = Functions::where('id', '!=', $id)->get();

        $isAdmin = auth()->user() && auth()->user()->role === 'admin';
        $isSpatialPlanner = auth()->user() && auth()->user()->role === 'spatial_planner';

        return view('Functions.edit', compact('function', 'categories', 'functions', 'isAdmin', 'isSpatialPlanner'));
    }

    public function update(Request $request, $id)
    {
        $function = Functions::with('effects')->findOrFail($id);

        $isAdmin = auth()->user() && auth()->user()->role === 'admin';

        $rules = [
            'Safety'                => 'required|numeric|between:-10,10',
            'Recreation'            => 'required|numeric|between:-10,10',
            'Environmental_Quality' => 'required|numeric|between:-10,10',
            'Services'              => 'required|numeric|between:-10,10',
            'Mobility'              => 'required|numeric|between:-10,10',
        ];

        if ($isAdmin) {
            $rules = array_merge($rules, [
                'name'                       => 'required|string|max:255|unique:functions,name,' . $id,
                'category'                   => 'required|exists:categories,category',
                'image'                      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'related_function'           => 'nullable|exists:functions,id',
                'relationship_safety'        => 'required_with:related_function|nullable|integer|between:-10,10',
                'relationship_recreation'    => 'required_with:related_function|nullable|integer|between:-10,10',
                'relationship_environmental' => 'required_with:related_function|nullable|integer|between:-10,10',
                'relationship_services'      => 'required_with:related_function|nullable|integer|between:-10,10',
                'relationship_mobility'      => 'required_with:related_function|nullable|integer|between:-10,10',
            ]);
        }

        $request->validate($rules);

        if ($isAdmin) {
            $hasRelationship = !empty($request->related_function);

            $functionData = [
                'name'                       => $request->name,
                'category'                   => $request->category,
                'related_function_id'        => $hasRelationship ? $request->related_function : null,
                'relationship_safety'        => $hasRelationship ? $request->relationship_safety : 0,
                'relationship_recreation'    => $hasRelationship ? $request->relationship_recreation : 0,
                'relationship_environmental' => $hasRelationship ? $request->relationship_environmental : 0,
                'relationship_services'      => $hasRelationship ? $request->relationship_services : 0,
                'relationship_mobility'      => $hasRelationship ? $request->relationship_mobility : 0,
            ];

            if ($request->hasFile('image')) {
                $functionData['image'] = $this->saveUploadedFunctionImage($request);
            }

            $function->update($functionData);
        }

        $function->effects()->update([
            'Safety'                => $request->Safety,
            'Recreation'            => $request->Recreation,
            'Environmental Quality' => $request->Environmental_Quality,
            'Services'              => $request->Services,
            'Mobility'              => $request->Mobility,
        ]);

        Notification::send(Auth::user(), new FunctionEdited($function));

        return redirect()->route('functions.show', $function->id);
    }

    public function destroy(Request $request, $id)
    {
        $function = Functions::findOrFail($id);

        $function->delete();

        return redirect()->route('functions.index');;
    }

    private function saveUploadedFunctionImage(Request $request)
    {
        $imageName = $request->file('image')->hashName();

        $relativeFolder = 'images/functions';
        $imageFolder = public_path($relativeFolder);

        if (!is_dir($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }

        if (!is_writable($imageFolder)) {
            chmod($imageFolder, 0777);
        }

        if (!is_writable($imageFolder)) {
            abort(500, 'De map public/images/functions is niet schrijfbaar. Zet het project buiten OneDrive of controleer de maprechten.');
        }

        $request->file('image')->move($imageFolder, $imageName);

        return $relativeFolder . '/' . $imageName;
    }
}