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
        $function =
            Functions::with('effects')
            ->findOrFail($id);

        $categories = Category::all();

        // alle functies ophalen
        $functions = Functions::all();

        return view(
            'Functions.edit',

            compact(
                'function',
                'categories',
                'functions'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $function =
            Functions::with('effects')
            ->findOrFail($id);

        // VALIDATIE
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'Safety' => 'required|numeric',

            'Recreation' => 'required|numeric',
            'Environmental_Quality' => 'required|numeric',
            'Services' => 'required|numeric',
            'Mobility' => 'required|numeric',

            // RELATIONSHIP EFFECTS
            'relationship_safety' => 'required|integer|min:-10|max:10',
            'relationship_recreation' => 'required|integer|min:-10|max:10',
            'relationship_environmental' => 'required|integer|min:-10|max:10',
            'relationship_services' => 'required|integer|min:-10|max:10',
            'relationship_mobility' => 'required|integer|min:-10|max:10',
        ]);

        // FUNCTION OPSLAAN
        $function->update([
            'name' => $request->name,
            'category' => $request->category,

            // RELATIONSHIP
            'related_function_id' => $request->related_function,

            // RELATIONSHIP EFFECTS
            'relationship_safety' => $request->relationship_safety,
            'relationship_recreation' => $request->relationship_recreation,
            'relationship_environmental' => $request->relationship_environmental,
            'relationship_services' => $request->relationship_services,
            'relationship_mobility' => $request->relationship_mobility,
        ]);


        // EFFECTS OPSLAAN
        $function->effects()->update([
            'Safety' => $request->Safety,
            'Recreation' => $request->Recreation,
            'Environmental Quality' => $request->Environmental_Quality,
            'Services' => $request->Services,
            'Mobility' => $request->Mobility,
        ]);

        return redirect(
            '/overview/' . $function->id
        );
    }
}