<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventEffect;
use App\Models\Monthly;
use App\Models\Recurring;
use App\Models\Weekly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function create()
    {
        return view('Events.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image',
            'typeEvent' => 'required|in:oneOff,recurring',
            'length' => 'required|integer|min:1',
            'lengthUnit' => 'required|in:hours,days,weeks',
        ]);

        DB::transaction(function () use ($request) {

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

            Event::create([
                'name' => $request->name,
                'image_url' => $imagePath,

                'recurring_id' => $recurringId,

                'start_date' => $request->typeEvent === 'oneOff'
                    ? $request->startDateOneOff
                    : $request->startDateRecurring,

                'time' => $request->typeEvent === 'oneOff'
                    ? $request->startTimeOneOff
                    : $request->startTimeRecurring,

                'length' => $request->length,
                'length_unit' => $request->lengthUnit,

                'dynamic' => $request->boolean('dynamic'),
            ]);
        });
        return redirect()
            ->back()
            ->with('success', 'Event created successfully.');
    }
}
