<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('functions')->get();

        return view('Groups.index', compact('groups'));
    }
}