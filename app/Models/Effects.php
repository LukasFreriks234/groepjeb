<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Effects extends Model
{
    protected $table = 'effects';

    protected $fillable = [
        'id',
        'Safety',
        'Recreation',
        'Environmental Quality',
        'Services',
        'Mobility',
    ];

    public $timestamps = false;

    public function cityFunction()
    {
        return $this->belongsTo(Functions::class, 'id', 'id');
    }

    public static function calculateEffectTotals($cells, $categories)
    {
        if (method_exists($cells, 'loadMissing')) {
            $cells->loadMissing(['cityFunction.groups', 'events.effects']);
        }

        $effects = self::all()->keyBy('id');

        $effectTotals = [];

        foreach ($categories as $category) {
            $effectTotals[$category->category] = 0;
        }

        self::addFunctionEffects($cells, $categories, $effects, $effectTotals);
        self::addEventEffects($cells, $categories, $effectTotals);
        self::addGlobalEventEffects($categories, $effectTotals);
        self::addRelationshipEffects($cells, $categories, $effectTotals);

        return $effectTotals;
    }

    private static function addGlobalEventEffects($categories, &$effectTotals)
    {
        $globalEvents = Event::with('effects')->where('is_global', true)->get();

        foreach ($globalEvents as $event) {
            foreach ($categories as $category) {
                $categoryName = $category->category;

                $eventEffect = $event->effects->firstWhere('category_name', $categoryName);

                if (!$eventEffect) {
                    continue;
                }

                $effectTotals[$categoryName] += (int) $eventEffect->effect;
            }
        }
    }

    private static function addFunctionEffects($cells, $categories, $effects, &$effectTotals)
    {
        foreach ($cells as $cell) {
            if ($cell->is_available || !$cell->cityFunction) {
                continue;
            }

            $functionId = $cell->cityFunction->id;
            $effect = $effects->get($functionId);

            if (!$effect) {
                continue;
            }

            foreach ($categories as $category) {
                $columnName = $category->category;
                $baseValue = (int) ($effect->getAttribute($columnName) ?? 0);

                $effectTotals[$columnName] += $baseValue;
            }
        }
    }

    private static function addEventEffects($cells, $categories, &$effectTotals)
    {
        foreach ($cells as $cell) {
            if (!$cell->relationLoaded('events')) {
                continue;
            }

            foreach ($cell->events as $event) {

                if (!$event->active) {
                    continue;
                }
                
                if (!$event->relationLoaded('effects')) {
                    continue;
                }

                foreach ($categories as $category) {
                    $categoryName = $category->category;

                    $eventEffect = $event->effects->firstWhere('category_name', $categoryName);

                    if (!$eventEffect) {
                        continue;
                    }

                    $effectTotals[$categoryName] += (int) $eventEffect->effect;
                }
            }
        }
    }

    private static function addRelationshipEffects($cells, $categories, &$effectTotals)
    {
        $relationshipColumns = [
            'Safety' => 'relationship_safety',
            'Recreation' => 'relationship_recreation',
            'Environmental Quality' => 'relationship_environmental',
            'Services' => 'relationship_services',
            'Mobility' => 'relationship_mobility',
        ];

        $occupiedCells = $cells->filter(function ($cell) {
            return !$cell->is_available && $cell->cityFunction;
        })->values();

        $bonusedPairs = [];
        $groupEffectPairs = [];
        $functionGroupsCache = [];

        $groupsByRole = self::groupIdsByRole();
        $sensitiveGroupIds = $groupsByRole['sensitive'] ?? [];
        $pollutingGroupIds = $groupsByRole['polluting'] ?? [];

        $allRelationships = GroupRelationship::with('effects')->get();

        foreach ($occupiedCells as $cell) {
            $function = $cell->cityFunction;
            $functionGroupIds = self::getFunctionGroupIds($function, $functionGroupsCache);

            foreach ($occupiedCells as $neighborCell) {
                if ($cell->id === $neighborCell->id) {
                    continue;
                }

                if (!self::cellsAreNeighbors($cell, $neighborCell)) {
                    continue;
                }

                $cellCategory = $cell->cityFunction->category ?? null;
                $neighborCategory = $neighborCell->cityFunction->category ?? null;

                if ($cellCategory && $cellCategory === $neighborCategory) {
                    $pairKey = self::pairKey($cell->id, $neighborCell->id);

                    if (!in_array($pairKey, $bonusedPairs)) {
                        $bonusedPairs[] = $pairKey;

                        if (array_key_exists($cellCategory, $effectTotals)) {
                            $effectTotals[$cellCategory] += 2;
                        }
                    }
                }

                $neighborGroupIds = self::getFunctionGroupIds($neighborCell->cityFunction, $functionGroupsCache);
                $relationships = self::getGroupRelationshipsInCollection($functionGroupIds, $neighborGroupIds, $allRelationships);

                foreach ($relationships as $relationship) {
                    if (!$relationship || !$relationship->effects) {
                        continue;
                    }

                    $pairKey = self::pairKey($cell->id, $neighborCell->id) . '-' . $relationship->id;

                    if (in_array($pairKey, $groupEffectPairs)) {
                        continue;
                    }

                    $groupEffectPairs[] = $pairKey;

                    $bonus = (int) ($relationship->effects->bonus_effect ?? 0);
                    $penalty = (int) ($relationship->effects->penalty_effect ?? 0);

                    $relationshipEffects = [
                        'Safety' => $relationship->effects->safety ?? 0,
                        'Recreation' => $relationship->effects->recreation ?? 0,
                        'Environmental Quality' => $relationship->effects->environmental_quality ?? 0,
                        'Services' => $relationship->effects->services ?? 0,
                        'Mobility' => $relationship->effects->mobility ?? 0,
                    ];

                    foreach ($relationshipEffects as $categoryName => $value) {
                        if (array_key_exists($categoryName, $effectTotals)) {
                            $effectTotals[$categoryName] += (int) $value;
                        }
                    }

                    $isSensitivePollutingPair = (
                        (in_array($relationship->group_id, $sensitiveGroupIds) && in_array($relationship->related_group_id, $pollutingGroupIds)) ||
                        (in_array($relationship->group_id, $pollutingGroupIds) && in_array($relationship->related_group_id, $sensitiveGroupIds))
                    );

                    if ($isSensitivePollutingPair) {
                        $affectedCategory = self::hasAnyGroup($functionGroupIds, $sensitiveGroupIds)
                            ? ($function->category ?? null)
                            : ($neighborCell->cityFunction->category ?? null);

                        if ($affectedCategory && array_key_exists($affectedCategory, $effectTotals)) {
                            if ($bonus !== 0) {
                                $effectTotals[$affectedCategory] += $bonus;
                            }

                            if ($penalty !== 0) {
                                $effectTotals[$affectedCategory] -= $penalty;
                            }
                        }
                    } else {
                        foreach ($categories as $category) {
                            $categoryName = $category->category;

                            if (!array_key_exists($categoryName, $effectTotals)) {
                                continue;
                            }

                            if ($bonus !== 0) {
                                $effectTotals[$categoryName] += $bonus;
                            }

                            if ($penalty !== 0) {
                                $effectTotals[$categoryName] -= $penalty;
                            }
                        }
                    }
                }

                if (!$function->related_function_id) {
                    continue;
                }

                if ($neighborCell->cityFunction->id != $function->related_function_id) {
                    continue;
                }

                foreach ($categories as $category) {
                    $categoryName = $category->category;
                    $relationshipColumn = $relationshipColumns[$categoryName] ?? null;

                    if ($relationshipColumn) {
                        $effectTotals[$categoryName] += (int) ($function->getAttribute($relationshipColumn) ?? 0);
                    }
                }
            }
        }
    }

    private static function groupIdsByRole(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $cache = [
            'polluting' => Group::query()->where('role', 'polluting')->pluck('id')->toArray(),
            'sensitive' => Group::query()->where('role', 'sensitive')->pluck('id')->toArray(),
        ];

        return $cache;
    }

    private static function getFunctionGroupIds($function, array &$cache): array
    {
        $functionId = $function->id ?? null;

        if (!$functionId) {
            return [];
        }

        if (!array_key_exists($functionId, $cache)) {
            $cache[$functionId] = $function->groups()->pluck('id')->toArray();
        }

        return $cache[$functionId];
    }

    private static function hasAnyGroup(array $functionGroupIds, array $candidateGroupIds): bool
    {
        if (empty($functionGroupIds) || empty($candidateGroupIds)) {
            return false;
        }

        return !empty(array_intersect($functionGroupIds, $candidateGroupIds));
    }

    private static function getGroupRelationshipsInCollection(array $groupIdsA, array $groupIdsB, $allRelationships): array
    {
        $matched = [];

        foreach ($allRelationships as $relationship) {
            $groupA = $relationship->group_id;
            $groupB = $relationship->related_group_id;

            if (
                (in_array($groupA, $groupIdsA) && in_array($groupB, $groupIdsB)) ||
                (in_array($groupA, $groupIdsB) && in_array($groupB, $groupIdsA))
            ) {
                $matched[] = $relationship;
            }
        }

        return $matched;
    }

    private static function pairKey(int $cellIdA, int $cellIdB): string
    {
        return implode('-', [min($cellIdA, $cellIdB), max($cellIdA, $cellIdB)]);
    }

    private static function cellsAreNeighbors($cellA, $cellB)
    {
        $xDifference = abs($cellA->x_coordinate - $cellB->x_coordinate);
        $yDifference = abs($cellA->y_coordinate - $cellB->y_coordinate);

        return ($xDifference + $yDifference) === 1;
    }

    public static function calculateQualityOfLife($cells, $categories)
    {
        $effectTotals = self::calculateEffectTotals($cells, $categories);

        return array_sum($effectTotals);
    }
}