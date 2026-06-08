<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Functions;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('functions')->get();

        return view('groups.index', compact('groups'));
    }

    public function add()
    {
        $functions = Functions::all();
        $groups = Group::all();

        return view('groups.add', compact('functions', 'groups'));
    }

    public function store(Request $request)
    {
        $group = Group::create([
            'name' => $request->name,
            'is_system' => false,

            'safety' => $request->safety,
            'recreation' => $request->recreation,
            'environmental_quality' => $request->environmental_quality,
            'services' => $request->services,
            'mobility' => $request->mobility,
        ]);

        if ($request->filled('function_id')) {
            $group->functions()->attach($request->function_id);
        }

        return redirect()->route('groups.index');
    }

    public function edit(Group $group)
    {
        $functions = Functions::all();
        $groups = Group::all();

        return view(
            'groups.edit',
            compact(
                'group',
                'functions',
                'groups'
            )
        );
    }

    public function update(Request $request, Group $group)
    {
        $group->update([
            'name' => $request->name,

            'safety' => $request->safety,
            'recreation' => $request->recreation,
            'environmental_quality' => $request->environmental_quality,
            'services' => $request->services,
            'mobility' => $request->mobility,
        ]);

        if ($request->filled('function_id')) {
            $group->functions()->sync([
                $request->function_id
            ]);
        }

        return redirect()->route('groups.index');
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $group->update([
            'name' => $request->name,
        ]);

        return redirect()->route('groups.index');
    }
}