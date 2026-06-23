<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventEffect;
use App\Models\Monthly;
use App\Models\Recurring;
use App\Models\Weekly;
use App\Models\GridCell;
use App\Models\GridDynamic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('name')->get();

        return view('Events.index', compact('events'));
    }

    public function create()
    {
        $cells = GridDynamic::orderBy('y_coordinate')
            ->orderBy('x_coordinate')
            ->get();

        $categories = Category::all();

        return view('Events.create', compact('cells', 'categories'));
    }

    public function store(Request $request)
    {
        if ($request->type === 'oneoff') {
            $request->validate([
                'startDateOneOff' => 'required',
                'startTimeOneOff' => 'required',
            ]);
        }

        if ($request->type === 'recurring') {
            $request->validate([
                'startDateRecurring' => 'required',
                'startTimeRecurring' => 'required',
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image',
            'typeEvent' => 'required|in:oneOff,recurring',
            'length' => 'required|integer|min:1',
            'lengthUnit' => 'required|in:hours,days,weeks',
            'route_cells' => 'nullable|string',
            'speed' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $imagePath = $this->saveUploadedFunctionImage($request);

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

            $event = Event::create([
                'name' => $request->name,
                'image_url' => $imagePath,
                'recurring_id' => $recurringId,
                'start_date' => $request->startDate,
                'time' => $request->startTime,
                'length' => $request->length,
                'length_unit' => $request->lengthUnit,
                'dynamic' => $request->boolean('dynamic'),
                'speed' => $request->boolean('dynamic') ? $request->speed : null,
                'is_global' => $request->boolean('is_global'),
            ]);

            $this->storeEventEffects($event, $request);

            if ($request->boolean('dynamic') && $request->filled('route_cells')) {
                $routeCells = json_decode($request->route_cells);

                if (is_array($routeCells)) {
                    foreach ($routeCells as $index => $gridCellId) {
                        DB::table('event_grid_cells')->insert([
                            'event_id' => $event->id,
                            'grid_dynamics_id' => $gridCellId,
                            'route_order' => $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    private function storeEventEffects(Event $event, Request $request)
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            $categoryName = $category->category;
            $effectValue = $this->getEffectValueFromRequest($request, $categoryName);

            EventEffect::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'category_name' => $categoryName,
                ],
                [
                    'effect' => $effectValue,
                ]
            );
        }
    }

    private function getEffectValueFromRequest(Request $request, string $categoryName): int
    {
        $normalizedCategoryName = $this->normalizeCategoryName($categoryName);

        $arrayInputs = [
            'effects',
            'event_effects',
            'effect',
        ];

        foreach ($arrayInputs as $arrayInput) {
            $values = $request->input($arrayInput, []);

            if (is_array($values)) {
                if (array_key_exists($categoryName, $values)) {
                    return (int) $values[$categoryName];
                }

                if (array_key_exists($normalizedCategoryName, $values)) {
                    return (int) $values[$normalizedCategoryName];
                }
            }
        }

        $directInputs = [
            $categoryName,
            $normalizedCategoryName,
            'effect_' . $normalizedCategoryName,
        ];

        foreach ($directInputs as $inputName) {
            if ($request->has($inputName)) {
                return (int) $request->input($inputName);
            }
        }

        return 0;
    }

    private function normalizeCategoryName(string $categoryName): string
    {
        $normalized = strtolower($categoryName);
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized);

        return trim($normalized, '_');
    }
}