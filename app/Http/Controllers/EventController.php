<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventEffect;
use App\Models\Monthly;
use App\Models\Recurring;
use App\Models\Weekly;
use App\Models\GridCell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function create()
    {
        $cells = GridCell::orderBy('y_coordinate')
            ->orderBy('x_coordinate')
            ->get();

        return view('Events.create', compact('cells'));
    }


    public function store(Request $request)
    {

        if ($request->type === 'oneoff') {
            $request->validate([
                'startDateOneOff' => 'required',
                'startTimeOneOff' => 'required'
            ]);
        }

        if ($request->type === 'recurring') {
            $request->validate([
                'startDateRecurring' => 'required',
                'startTimeRecurring' => 'required'
            ]);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image',
            'typeEvent' => 'required|in:oneOff,recurring',
            'length' => 'required|integer|min:1',
            'lengthUnit' => 'required|in:hours,days,weeks',
            'route_cells' => 'nullable|string',
        ]);

        $event = DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | Upload image
            |--------------------------------------------------------------------------
            */

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('events', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | Create recurring record
            |--------------------------------------------------------------------------
            */

            $recurringId = null;

            if ($request->typeEvent === 'recurring') {

                $amount = match ($request->recurrencePattern) {
                    'daily' => $request->amountDay,
                    'weekly' => $request->amountWeek,
                    'monthly' => $request->amountMonth,
                    'yearly' => $request->amountYear,
                    default => 1,
                };

                $recurring = Recurring::create([
                    'frequency' => $request->recurrencePattern,
                    'amount' => $amount,
                    'end_date' => $request->endDate,
                ]);

                $recurringId = $recurring->id;

                /*
                |--------------------------------------------------------------------------
                | Weekly recurrence
                |--------------------------------------------------------------------------
                */

                if (
                    $request->recurrencePattern === 'weekly'
                    && $request->filled('weekdays')
                ) {

                    foreach ($request->weekdays as $weekday) {

                        Weekly::create([
                            'recurring_id' => $recurring->id,
                            'weekday' => $weekday,
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Monthly recurrence
                |--------------------------------------------------------------------------
                */

                if ($request->recurrencePattern === 'monthly') {

                    if (
                        $request->typeMonth === 'each'
                        && $request->filled('monthDays')
                    ) {

                        foreach ($request->monthDays as $day) {

                            Monthly::create([
                                'recurring_id' => $recurring->id,
                                'day_of_month' => $day,
                            ]);
                        }
                    }

                    if ($request->typeMonth === 'onThe') {

                        Monthly::create([
                            'recurring_id' => $recurring->id,
                            'ordinal_number' => $request->ordinalNumber,
                            'weekday' => $request->dayMonth,
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Create event
            |--------------------------------------------------------------------------
            */

            return Event::create([
                'name' => $request->name,
                'image_url' => $imagePath,

                'recurring_id' => $recurringId,

                'start_date' => $request->startDate,

                'time' => $request->startTime,

                'length' => $request->length,
                'length_unit' => $request->lengthUnit,

                'dynamic' => $request->boolean('dynamic'),
            ]);
        });

        if ($request->boolean('dynamic') && $request->filled('route_cells')) {

            $routeCells = json_decode($request->route_cells);

            foreach ($routeCells as $index => $gridCellId) {

                DB::table('event_grid_cells')->insert([
                    'event_id' => $event->id,
                    'grid_cell_id' => $gridCellId,
                    'route_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        return redirect()
            ->back()
            ->with('success', 'Event created successfully.');
    }
}
