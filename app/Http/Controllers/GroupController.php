<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Functions;
use App\Models\GroupRelationship;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('functions')->get();

        return view('Groups.index', compact('groups'));
    }

    public function add()
    {
        $functions = Functions::all();
        $groups = Group::all();

        return view('Groups.add', compact('functions', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:groups,name'
        ]);

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

        if ($request->filled('related_group')) {

            GroupRelationship::create([
                'group_id' => $group->id,
                'related_group_id' => $request->related_group,
            ]);
        }

        return redirect()->route('groups.index');
    }

    public function edit(Group $group)
    {
        $functions = Functions::all();
        $groups = Group::all();

        $selectedRelationship =
            GroupRelationship::where(
                'group_id',
                $group->id
            )->value('related_group_id');

        return view(
            'Groups.edit',
            compact(
                'group',
                'functions',
                'groups',
                'selectedRelationship'
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

        GroupRelationship::where(
            'group_id',
            $group->id
        )->delete();

        if ($request->filled('related_group')) {
            GroupRelationship::create([
                'group_id' => $group->id,
                'related_group_id' => $request->related_group,
            ]);
        }

        return redirect()->route('groups.index');
    }
}