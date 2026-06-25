<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventEffect;
use App\Models\GridDynamic;
use App\Models\Monthly;
use App\Models\Recurring;
use App\Models\Weekly;
use App\Services\RecurringEventScheduler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    public function store(Request $request, RecurringEventScheduler $scheduler)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image'],
            'typeEvent' => ['required', Rule::in(['oneOff', 'recurring'])],
            'length' => ['required', 'integer', 'min:1'],
            'lengthUnit' => ['required', Rule::in(['hours', 'days', 'weeks'])],
            'route_cells' => ['nullable', 'string'],
            'speed' => ['nullable', 'integer', 'min:1'],
            'startDate' => ['required_if:typeEvent,recurring', 'nullable', 'date'],
            'startTime' => ['required_if:typeEvent,recurring', 'nullable', 'date_format:H:i'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'recurrencePattern' => ['required_if:typeEvent,recurring', 'nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'amountDay' => ['nullable', 'integer', 'min:1', 'max:10'],
            'amountWeek' => ['nullable', 'integer', 'min:1', 'max:10'],
            'amountMonth' => ['nullable', 'integer', 'min:1', 'max:10'],
            'amountYear' => ['nullable', 'integer', 'min:1', 'max:10'],
            'weekdays' => ['nullable', 'array'],
            'weekdays.*' => [Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'typeMonth' => ['nullable', Rule::in(['each', 'onThe'])],
            'monthDays' => ['nullable', 'array'],
            'monthDays.*' => ['integer', 'between:1,31'],
            'ordinalNumber' => ['nullable', Rule::in(['first', 'second', 'third', 'fourth', 'fifth', 'next to last', 'last'])],
            'dayMonth' => ['nullable', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'day', 'weekday', 'weekendday'])],
        ]);

        if ($validated['typeEvent'] === 'recurring') {
            $this->validateRecurrenceDetails($request, $validated);
        }

        DB::transaction(function () use ($request, $validated, $scheduler) {
            $imagePath = $this->saveUploadedFunctionImage($request);
            $recurring = null;
            $startDate = $validated['startDate'] ?? null;
            $nextDate = $startDate;

            if ($validated['typeEvent'] === 'recurring') {
                $amount = $this->recurrenceAmount($validated);

                $recurring = Recurring::create([
                    'frequency' => $validated['recurrencePattern'],
                    'amount' => $amount,
                    'end_date' => $validated['endDate'] ?? null,
                ]);

                $this->storeRecurringRules($recurring, $request, $validated);

                $startAt = $this->dateTimeInAppTimezone(
                    $validated['startDate'],
                    $validated['startTime']
                );
                $firstOccurrence = $scheduler->firstOccurrence($recurring->load(['weekly', 'monthly']), $startAt);
                $nextDate = $firstOccurrence?->toDateString();
            }

            $event = Event::create([
                'name' => $validated['name'],
                'image_url' => $imagePath,
                'recurring_id' => $recurring?->id,
                'start_date' => $startDate,
                'next_date' => $nextDate,
                'time' => $validated['startTime'] ?? null,
                'length' => $validated['length'],
                'length_unit' => $validated['lengthUnit'],
                'dynamic' => $request->boolean('dynamic'),
                'speed' => $request->boolean('dynamic') ? ($validated['speed'] ?? null) : null,
                'is_global' => $request->boolean('is_global'),
                // A recurring event only becomes active when the simulation
                // reaches its occurrence. One-off events retain prior behavior.
                'active' => $validated['typeEvent'] !== 'recurring',
            ]);

            $this->storeEventEffects($event, $request);

            if ($request->boolean('dynamic') && $request->filled('route_cells')) {
                $routeCells = json_decode($request->route_cells, true);

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

    public function saveNextDate(Request $request)
    {
        $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'next_date' => ['required', 'date'],
        ]);

        $event = Event::findOrFail($request->integer('event_id'));
        $event->next_date = Carbon::parse($request->input('next_date'), config('app.timezone'))->toDateString();
        $event->save();

        return response()->json([
            'success' => true,
            'next_date' => $event->next_date?->toDateString(),
        ]);
    }

    /**
     * Advances the schedule cursor and returns whether the event is currently
     * active at the simulation time supplied by the browser.
     */
    public function checkRecurring(Request $request, RecurringEventScheduler $scheduler)
    {
        $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'current_datetime' => ['required', 'date'],
            'minutes_per_tick' => ['required', 'integer', 'min:0'],
        ]);

        $event = Event::with(['recurring.weekly', 'recurring.monthly'])
            ->findOrFail($request->integer('event_id'));

        if (!$event->recurring) {
            return response()->json([
                'triggered' => false,
                'is_recurring' => false,
                'active' => (bool) $event->active,
                'event_id' => $event->id,
            ]);
        }

        $current = $this->simulationDateTime($request->input('current_datetime'));
        $previous = $current->copy()->subMinutes($request->integer('minutes_per_tick'));
        $triggered = false;

        DB::transaction(function () use ($event, $scheduler, $current, $previous, &$triggered) {
            $nextAt = $this->nextOccurrenceDateTime($event, $scheduler);

            // Protect old data: a missing cursor is rebuilt from start_date.
            if (!$nextAt && $event->start_date) {
                $nextAt = $scheduler->firstOccurrence(
                    $event->recurring,
                    $this->dateTimeInAppTimezone($event->start_date->toDateString(), $event->time)
                );
            }

            while ($nextAt && $nextAt->lte($current)) {
                $endAt = $this->eventEnd($event, $nextAt);
                $wasReachedThisTick = $nextAt->gt($previous) || $endAt->gt($previous);

                if ($endAt->gt($current)) {
                    $event->active = true;
                    $event->end_date = $endAt;
                    $triggered = $triggered || $wasReachedThisTick;
                }

                $nextAt = $scheduler->nextOccurrence($event, $nextAt);
            }

            if ($event->end_date && $event->end_date->lte($current)) {
                $event->active = false;
            }

            $event->next_date = $nextAt?->toDateString();
            $event->save();
        });

        $event->refresh();

        return response()->json([
            'triggered' => $triggered,
            'is_recurring' => true,
            'active' => (bool) $event->active,
            'event_id' => $event->id,
            'next_date' => $event->next_date?->toDateString(),
            'end_date' => $event->end_date?->format('Y-m-d H:i:s'),
            'current' => $current->format('Y-m-d H:i:s'),
            'previous' => $previous->format('Y-m-d H:i:s'),
        ]);
    }

    public function checkRecurringExpired(Request $request)
    {
        $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'current_datetime' => ['required', 'date'],
        ]);

        $event = Event::findOrFail($request->integer('event_id'));
        $current = $this->simulationDateTime($request->input('current_datetime'));
        $expired = (bool) ($event->end_date && $event->end_date->lte($current));

        if ($expired && $event->active) {
            $event->active = false;
            $event->save();
        }

        return response()->json([
            'untoggle' => $expired,
            'active' => (bool) $event->active,
        ]);
    }

    public function updateActive(Request $request)
    {
        $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        Event::whereKey($request->integer('event_id'))->update(['active' => false]);

        return response()->noContent();
    }

    private function validateRecurrenceDetails(Request $request, array $validated): void
    {
        if ($validated['recurrencePattern'] === 'weekly' && empty($validated['weekdays'])) {
            $request->validate(['weekdays' => ['required', 'array', 'min:1']]);
        }

        if ($validated['recurrencePattern'] === 'monthly') {
            $request->validate([
                'typeMonth' => ['required', Rule::in(['each', 'onThe'])],
            ]);

            if ($request->input('typeMonth') === 'each') {
                $request->validate(['monthDays' => ['required', 'array', 'min:1']]);
            }

            if ($request->input('typeMonth') === 'onThe') {
                $request->validate([
                    'ordinalNumber' => ['required'],
                    'dayMonth' => ['required'],
                ]);
            }
        }
    }

    private function recurrenceAmount(array $validated): int
    {
        return max(1, (int) match ($validated['recurrencePattern']) {
            'daily' => $validated['amountDay'] ?? 1,
            'weekly' => $validated['amountWeek'] ?? 1,
            'monthly' => $validated['amountMonth'] ?? 1,
            'yearly' => $validated['amountYear'] ?? 1,
        });
    }

    private function storeRecurringRules(Recurring $recurring, Request $request, array $validated): void
    {
        if ($validated['recurrencePattern'] === 'weekly') {
            foreach ($validated['weekdays'] as $weekday) {
                Weekly::create([
                    'recurring_id' => $recurring->id,
                    'weekday' => $weekday,
                ]);
            }
        }

        if ($validated['recurrencePattern'] !== 'monthly') {
            return;
        }

        if ($request->input('typeMonth') === 'each') {
            foreach ($validated['monthDays'] as $day) {
                Monthly::create([
                    'recurring_id' => $recurring->id,
                    'day_of_month' => $day,
                ]);
            }

            return;
        }

        Monthly::create([
            'recurring_id' => $recurring->id,
            'ordinal_number' => $validated['ordinalNumber'],
            'weekday' => $validated['dayMonth'],
        ]);
    }

    private function nextOccurrenceDateTime(Event $event, RecurringEventScheduler $scheduler): ?Carbon
    {
        if (!$event->next_date) {
            return null;
        }

        return $scheduler->occurrenceAt(
            $event,
            Carbon::parse($event->next_date, config('app.timezone'))
        );
    }

    private function eventEnd(Event $event, Carbon $start): Carbon
    {
        return match ($event->length_unit) {
            'hours' => $start->copy()->addHours((int) $event->length),
            'days' => $start->copy()->addDays((int) $event->length),
            'weeks' => $start->copy()->addWeeks((int) $event->length),
            default => $start->copy(),
        };
    }

    private function simulationDateTime(string $value): Carbon
    {
        // The browser sends a local simulation value in Y-m-d H:i:s. Older
        // clients can still send ISO strings; both formats are accepted.
        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value, config('app.timezone'));
        } catch (\Throwable) {
            return Carbon::parse($value)->setTimezone(config('app.timezone'));
        }
    }

    private function dateTimeInAppTimezone(string $date, ?string $time): Carbon
    {
        return Carbon::parse(
            $date . ' ' . ($time ?: '00:00:00'),
            config('app.timezone')
        );
    }

    private function storeEventEffects(Event $event, Request $request): void
    {
        foreach (Category::all() as $category) {
            $categoryName = $category->category;

            EventEffect::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'category_name' => $categoryName,
                ],
                [
                    'effect' => $this->getEffectValueFromRequest($request, $categoryName),
                ]
            );
        }
    }

    private function getEffectValueFromRequest(Request $request, string $categoryName): int
    {
        $normalizedCategoryName = $this->normalizeCategoryName($categoryName);

        foreach (['effects', 'event_effects', 'effect'] as $arrayInput) {
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

        foreach ([$categoryName, $normalizedCategoryName, 'effect_' . $normalizedCategoryName] as $inputName) {
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

    private function saveUploadedFunctionImage(Request $request): string
    {
        $imageName = $request->file('image')->hashName();
        $relativeFolder = 'images/events';
        $imageFolder = public_path($relativeFolder);

        if (!is_dir($imageFolder)) {
            mkdir($imageFolder, 0777, true);
        }

        if (!is_writable($imageFolder)) {
            chmod($imageFolder, 0777);
        }

        if (!is_writable($imageFolder)) {
            abort(500, 'De map public/images/events is niet schrijfbaar. Controleer de maprechten.');
        }

        $request->file('image')->move($imageFolder, $imageName);

        return $relativeFolder . '/' . $imageName;
    }
}
