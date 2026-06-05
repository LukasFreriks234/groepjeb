<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();

        return view('groups.index', compact('groups'));
    }

    public function add()
    {
        return view('groups.add');
    }

    public function store(Request $request)
    {
        Group::create([
            'name' => $request->name,
            'is_system' => false,
        ]);

        return redirect()->route('groups.index');
    }
}