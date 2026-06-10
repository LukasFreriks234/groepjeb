<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Functions;
use App\Models\GroupRelationship;
use App\Models\GroupRelationshipEffects;

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
        ]);

        if ($request->filled('function_ids')) {
            $group->functions()->attach(
                $request->function_ids
            );
        }

        if ($request->filled('related_group')) {

            $relationship = GroupRelationship::create([
                'group_id' => $group->id,
                'related_group_id' => $request->related_group,
            ]);

            GroupRelationshipEffects::create([
                'group_relationship_id' => $relationship->id,

                'bonus_effect' => $request->bonus_effect ?? 0,
                'penalty_effect' => $request->penalty_effect ?? 0,

                'safety' => $request->relationship_safety ?? 0,
                'recreation' => $request->relationship_recreation ?? 0,
                'environmental_quality' => $request->relationship_environmental ?? 0,
                'services' => $request->relationship_services ?? 0,
                'mobility' => $request->relationship_mobility ?? 0,
            ]);
        }

        return redirect()->route('groups.index');
    }

    public function edit(Group $group)
    {
        $functions = Functions::all();
        $groups = Group::all();

        $relationship = GroupRelationship::where(
            'group_id',
            $group->id
        )->first();

        $selectedRelationship = null;

        $bonusEffect = 0;
        $penaltyEffect = 0;

        $relationshipSafety = 0;
        $relationshipRecreation = 0;
        $relationshipEnvironmental = 0;
        $relationshipServices = 0;
        $relationshipMobility = 0;

        if ($relationship) {

            $selectedRelationship =
                $relationship->related_group_id;

            $effect =
                GroupRelationshipEffects::where(
                    'group_relationship_id',
                    $relationship->id
                )->first();

            if ($effect) {

                $bonusEffect =
                    $effect->bonus_effect ?? 0;

                $penaltyEffect =
                    $effect->penalty_effect ?? 0;

                $relationshipSafety =
                    $effect->safety ?? 0;

                $relationshipRecreation =
                    $effect->recreation ?? 0;

                $relationshipEnvironmental =
                    $effect->environmental_quality ?? 0;

                $relationshipServices =
                    $effect->services ?? 0;

                $relationshipMobility =
                    $effect->mobility ?? 0;
            }
        }

        return view(
            'Groups.edit',
            compact(
                'group',
                'functions',
                'groups',
                'selectedRelationship',

                'bonusEffect',
                'penaltyEffect',

                'relationshipSafety',
                'relationshipRecreation',
                'relationshipEnvironmental',
                'relationshipServices',
                'relationshipMobility'
            )
        );
    }
    
    public function update(Request $request, Group $group)
    {
        $group->update([
            'name' => $request->name,
        ]);

        if ($request->filled('function_ids')) {
            $group->functions()->sync(
                $request->function_ids
            );
        }
        else {
            $group->functions()->detach();
        }

        GroupRelationship::where(
            'group_id',
            $group->id
        )->delete();

        if ($request->filled('related_group')) {

            $relationship = GroupRelationship::create([
                'group_id' => $group->id,
                'related_group_id' => $request->related_group,
            ]);

            GroupRelationshipEffects::create([
                'group_relationship_id' => $relationship->id,

                'bonus_effect' => $request->bonus_effect ?? 0,
                'penalty_effect' => $request->penalty_effect ?? 0,

                'safety' => $request->relationship_safety ?? 0,
                'recreation' => $request->relationship_recreation ?? 0,
                'environmental_quality' => $request->relationship_environmental ?? 0,
                'services' => $request->relationship_services ?? 0,
                'mobility' => $request->relationship_mobility ?? 0,
            ]);
        }

        return redirect()->route('groups.index');
    }
}