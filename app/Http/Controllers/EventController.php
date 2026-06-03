<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventEffect;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function create()
    {
        return view('Events.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'image' => 'required|image',

            'Safety' => 'required|integer|min:-10|max:10',
            'Recreation' => 'required|integer|min:-10|max:10',
            'Environmental_Quality' => 'required|integer|min:-10|max:10',
            'Services' => 'required|integer|min:-10|max:10',
            'Mobility' => 'required|integer|min:-10|max:10',

            'typeEvent' => 'required|in:oneOff,recurring',

            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
        ]);

        // image upload
        $imagePath = $request->file('image')->store('events', 'public');

        // create event
        $event = Event::create([
            'name' => $validated['name'],
            'image_url' => $imagePath,

            'type' => $validated['typeEvent'] === 'oneOff'
                ? 'one-off'
                : 'recurring',

            'recurrence_pattern' => $validated['typeEvent'] === 'recurring'
                ? $validated['recurrence_pattern']
                : null,

            'dynamic' => $request->boolean('dynamic'),
        ]);

        // effects insert (IMPORTANT FIX)
        $effects = [
            'Safety' => $validated['Safety'],
            'Recreation' => $validated['Recreation'],
            'Environmental Quality' => $validated['Environmental_Quality'],
            'Services' => $validated['Services'],
            'Mobility' => $validated['Mobility'],
        ];

        $insertData = [];

        foreach ($effects as $category => $value) {
            $insertData[] = [
                'event_id' => $event->id,
                'category_name' => $category,
                'effect' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        EventEffect::insert($insertData);

        return redirect()
            ->back()
            ->with('success', 'Event created successfully.');
    }
}
