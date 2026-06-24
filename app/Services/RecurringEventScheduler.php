<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Recurring;
use Carbon\Carbon;

/**
 * Calculates occurrences for recurring events from the immutable start date.
 *
 * The event's next_date is only a cursor to the next occurrence. The original
 * start_date is kept so intervals such as "every 2 weeks" always remain
 * anchored to the date selected when the event was created.
 */
class RecurringEventScheduler
{
    public function firstOccurrence(Recurring $recurring, Carbon $startAt): ?Carbon
    {
        return $this->findOccurrence($recurring, $startAt, $startAt, true);
    }

    public function nextOccurrence(Event $event, Carbon $after): ?Carbon
    {
        if (!$event->recurring) {
            return null;
        }

        $anchor = $this->eventStartAt($event);

        return $this->findOccurrence($event->recurring, $anchor, $after, false);
    }

    public function occurrenceAt(Event $event, Carbon $date): Carbon
    {
        [$hours, $minutes, $seconds] = array_pad(
            array_map('intval', explode(':', (string) ($event->time ?: '00:00:00'))),
            3,
            0
        );

        return $date->copy()->setTime($hours, $minutes, $seconds);
    }

    private function eventStartAt(Event $event): Carbon
    {
        $date = $event->start_date ?: $event->next_date ?: now(config('app.timezone'))->toDateString();

        return $this->dateAtEventTime(Carbon::parse($date, config('app.timezone')), $event->time);
    }

    private function findOccurrence(
        Recurring $recurring,
        Carbon $anchor,
        Carbon $threshold,
        bool $inclusive
    ): ?Carbon {
        $frequency = $recurring->frequency;
        $amount = max(1, (int) $recurring->amount);

        return match ($frequency) {
            'daily' => $this->findDailyOccurrence($recurring, $anchor, $threshold, $inclusive, $amount),
            'weekly' => $this->findWeeklyOccurrence($recurring, $anchor, $threshold, $inclusive, $amount),
            'monthly' => $this->findMonthlyOccurrence($recurring, $anchor, $threshold, $inclusive, $amount),
            'yearly' => $this->findYearlyOccurrence($recurring, $anchor, $threshold, $inclusive, $amount),
            default => null,
        };
    }

    private function findDailyOccurrence(
        Recurring $recurring,
        Carbon $anchor,
        Carbon $threshold,
        bool $inclusive,
        int $amount
    ): ?Carbon {
        $candidate = $anchor->copy();

        while (!$this->matchesThreshold($candidate, $threshold, $inclusive)) {
            $candidate->addDays($amount);
        }

        return $this->isWithinSeries($recurring, $candidate) ? $candidate : null;
    }

    private function findWeeklyOccurrence(
        Recurring $recurring,
        Carbon $anchor,
        Carbon $threshold,
        bool $inclusive,
        int $amount
    ): ?Carbon {
        $weekdays = $recurring->weekly
            ->pluck('weekday')
            ->filter()
            ->map(fn (string $weekday) => strtolower($weekday))
            ->unique()
            ->values()
            ->all();

        if (empty($weekdays)) {
            $weekdays = [strtolower($anchor->englishDayOfWeek)];
        }

        $candidateDate = $threshold->copy()->startOfDay();
        $anchorWeek = $anchor->copy()->startOfWeek(Carbon::MONDAY);

        // A valid weekly date is always found within the selected cycle.
        for ($day = 0; $day <= ($amount * 7) + 7; $day++, $candidateDate->addDay()) {
            $candidate = $this->dateAtEventTime($candidateDate, $anchor->format('H:i:s'));

            if ($candidate->lt($anchor) || !$this->matchesThreshold($candidate, $threshold, $inclusive)) {
                continue;
            }

            $weeksSinceAnchor = $anchorWeek->diffInWeeks(
                $candidateDate->copy()->startOfWeek(Carbon::MONDAY),
                false
            );

            if ($weeksSinceAnchor < 0 || $weeksSinceAnchor % $amount !== 0) {
                continue;
            }

            if (!in_array(strtolower($candidate->englishDayOfWeek), $weekdays, true)) {
                continue;
            }

            return $this->isWithinSeries($recurring, $candidate) ? $candidate : null;
        }

        return null;
    }

    private function findMonthlyOccurrence(
        Recurring $recurring,
        Carbon $anchor,
        Carbon $threshold,
        bool $inclusive,
        int $amount
    ): ?Carbon {
        $monthStart = $threshold->copy()->startOfMonth();
        $anchorMonth = $anchor->copy()->startOfMonth();
        $monthsSinceAnchor = max(0, $anchorMonth->diffInMonths($monthStart, false));
        $firstOffset = $monthsSinceAnchor;

        if ($firstOffset % $amount !== 0) {
            $firstOffset += $amount - ($firstOffset % $amount);
        }

        // The longest gap caused by an invalid day (for example the 31st) is
        // shorter than one year. The cap also protects against malformed data.
        for ($offset = $firstOffset; $offset <= $firstOffset + max(24, $amount * 3); $offset += $amount) {
            $month = $anchorMonth->copy()->addMonthsNoOverflow($offset);
            $candidates = $this->monthlyCandidates($recurring, $month, $anchor->format('H:i:s'));

            foreach ($candidates as $candidate) {
                if ($candidate->lt($anchor) || !$this->matchesThreshold($candidate, $threshold, $inclusive)) {
                    continue;
                }

                return $this->isWithinSeries($recurring, $candidate) ? $candidate : null;
            }

            if ($this->isAfterSeriesEnd($recurring, $month)) {
                return null;
            }
        }

        return null;
    }

    private function findYearlyOccurrence(
        Recurring $recurring,
        Carbon $anchor,
        Carbon $threshold,
        bool $inclusive,
        int $amount
    ): ?Carbon {
        $year = max($anchor->year, $threshold->year);
        $remainder = ($year - $anchor->year) % $amount;

        if ($remainder !== 0) {
            $year += $amount - $remainder;
        }

        for ($attempt = 0; $attempt < 4; $attempt++, $year += $amount) {
            $monthStart = Carbon::create($year, $anchor->month, 1, 0, 0, 0, config('app.timezone'));
            $candidate = $monthStart->copy()->setDay(min($anchor->day, $monthStart->daysInMonth));
            $candidate = $this->dateAtEventTime($candidate, $anchor->format('H:i:s'));

            if ($candidate->lt($anchor) || !$this->matchesThreshold($candidate, $threshold, $inclusive)) {
                continue;
            }

            return $this->isWithinSeries($recurring, $candidate) ? $candidate : null;
        }

        return null;
    }

    /** @return array<int, Carbon> */
    private function monthlyCandidates(Recurring $recurring, Carbon $month, string $time): array
    {
        $dates = [];
        $monthlies = $recurring->monthly;

        foreach ($monthlies->whereNotNull('day_of_month') as $rule) {
            if ((int) $rule->day_of_month <= $month->daysInMonth) {
                $dates[] = $this->dateAtEventTime($month->copy()->setDay((int) $rule->day_of_month), $time);
            }
        }

        foreach ($monthlies->whereNull('day_of_month') as $rule) {
            $matchingDays = [];
            $cursor = $month->copy()->startOfMonth();

            while ($cursor->month === $month->month) {
                if ($this->matchesMonthlyWeekdayRule($cursor, (string) $rule->weekday)) {
                    $matchingDays[] = $cursor->copy();
                }

                $cursor->addDay();
            }

            $index = match ((string) $rule->ordinal_number) {
                'first' => 0,
                'second' => 1,
                'third' => 2,
                'fourth' => 3,
                'fifth' => 4,
                'next to last' => count($matchingDays) - 2,
                'last' => count($matchingDays) - 1,
                default => null,
            };

            if ($index !== null && isset($matchingDays[$index])) {
                $dates[] = $this->dateAtEventTime($matchingDays[$index], $time);
            }
        }

        usort($dates, fn (Carbon $left, Carbon $right) => $left <=> $right);

        return collect($dates)
            ->unique(fn (Carbon $date) => $date->format('Y-m-d H:i:s'))
            ->values()
            ->all();
    }

    private function matchesMonthlyWeekdayRule(Carbon $date, string $weekday): bool
    {
        return match (strtolower($weekday)) {
            'day' => true,
            'weekday' => $date->isWeekday(),
            'weekendday' => $date->isWeekend(),
            default => strtolower($date->englishDayOfWeek) === strtolower($weekday),
        };
    }

    private function dateAtEventTime(Carbon $date, ?string $time): Carbon
    {
        [$hours, $minutes, $seconds] = array_pad(
            array_map('intval', explode(':', (string) ($time ?: '00:00:00'))),
            3,
            0
        );

        return $date->copy()->setTimezone(config('app.timezone'))->setTime($hours, $minutes, $seconds);
    }

    private function matchesThreshold(Carbon $candidate, Carbon $threshold, bool $inclusive): bool
    {
        return $inclusive ? $candidate->gte($threshold) : $candidate->gt($threshold);
    }

    private function isWithinSeries(Recurring $recurring, Carbon $candidate): bool
    {
        if (!$recurring->end_date) {
            return true;
        }

        return $candidate->lte(Carbon::parse($recurring->end_date, config('app.timezone'))->endOfDay());
    }

    private function isAfterSeriesEnd(Recurring $recurring, Carbon $date): bool
    {
        if (!$recurring->end_date) {
            return false;
        }

        return $date->gt(Carbon::parse($recurring->end_date, config('app.timezone'))->endOfDay());
    }
}
