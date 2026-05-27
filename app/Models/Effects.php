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

    private static $pollutingFunctions = ['Road', 'Shop', 'Gas Station'];

    private static $sensitiveCategoryMap = [
        'Park'     => 'Recreation',
        'School'   => 'Services',
        'Hospital' => 'Services',
    ];

    public static function calculateEffectTotals($cells, $categories)
    {
        $effects = self::all()->keyBy('id');

        $effectTotals = [];
        foreach ($categories as $category) {
            $effectTotals[$category->category] = 0;
        }

        $functionProcessed = [];

        foreach ($cells as $cell) {
            if (!$cell->is_available && $cell->cityFunction) {
                $functionId = $cell->cityFunction->id;
                $effect     = $effects->get($functionId);

                if (!$effect) {
                    continue;
                }

                $functionProcessed[$functionId] = ($functionProcessed[$functionId] ?? 0) + 1;
                $occurrence = $functionProcessed[$functionId];

                foreach ($categories as $category) {
                    $columnName = $category->category;
                    $baseValue  = (int) ($effect->getAttribute($columnName) ?? 0);

                    if ($baseValue <= 0) {
                        $effectTotals[$columnName] += $baseValue;
                        continue;
                    }

                    $adjustedValue = match($occurrence) {
                        1       => $baseValue,
                        2       => (int) ceil($baseValue / 2),
                        default => 0,
                    };

                    $effectTotals[$columnName] += $adjustedValue;
                }
            }
        }

        self::addRelationshipEffects($cells, $categories, $effectTotals);
        self::addNegativeProximityPenalties($cells, $effectTotals);

        return $effectTotals;
    }

    private static function addRelationshipEffects($cells, $categories, &$effectTotals)
    {
        $relationshipColumns = [
            'Safety'                => 'relationship_safety',
            'Recreation'            => 'relationship_recreation',
            'Environmental Quality' => 'relationship_environmental',
            'Services'              => 'relationship_services',
            'Mobility'              => 'relationship_mobility',
        ];

        $occupiedCells = $cells->filter(function ($cell) {
            return !$cell->is_available && $cell->cityFunction;
        })->values();

        $bonusedPairs = [];

        foreach ($occupiedCells as $cell) {
            $function = $cell->cityFunction;

            foreach ($occupiedCells as $neighborCell) {
                if ($cell->id === $neighborCell->id) {
                    continue;
                }

                if (!self::cellsAreNeighbors($cell, $neighborCell)) {
                    continue;
                }

                $cellCategory     = $cell->cityFunction->category         ?? null;
                $neighborCategory = $neighborCell->cityFunction->category ?? null;

                if ($cellCategory && $cellCategory === $neighborCategory) {
                    $pairKey = implode('-', [min($cell->id, $neighborCell->id), max($cell->id, $neighborCell->id)]);

                    if (!in_array($pairKey, $bonusedPairs)) {
                        $bonusedPairs[] = $pairKey;

                        if (array_key_exists($cellCategory, $effectTotals)) {
                            $effectTotals[$cellCategory] += 2;
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
                    $categoryName       = $category->category;
                    $relationshipColumn = $relationshipColumns[$categoryName] ?? null;

                    if ($relationshipColumn) {
                        $effectTotals[$categoryName] += (int) ($function->getAttribute($relationshipColumn) ?? 0);
                    }
                }
            }
        }
    }

    private static function addNegativeProximityPenalties($cells, &$effectTotals)
    {
        $occupiedCells = $cells->filter(
            fn($cell) => !$cell->is_available && $cell->cityFunction
        )->values();

        $penalisedPairs = [];

        foreach ($occupiedCells as $cell) {
            $functionName = $cell->cityFunction->name ?? null;

            if (!array_key_exists($functionName, self::$sensitiveCategoryMap)) {
                continue;
            }

            $affectedCategory = self::$sensitiveCategoryMap[$functionName];
            if (!array_key_exists($affectedCategory, $effectTotals)) {
                continue;
            }

            foreach ($occupiedCells as $neighborCell) {
                if ($cell->id === $neighborCell->id) {
                    continue;
                }

                if (!self::cellsAreNeighbors($cell, $neighborCell)) {
                    continue;
                }

                $neighborName = $neighborCell->cityFunction->name ?? null;

                if (!in_array($neighborName, self::$pollutingFunctions)) {
                    continue;
                }

                $pairKey = implode('-', [min($cell->id, $neighborCell->id), max($cell->id, $neighborCell->id)]);

                if (!in_array($pairKey, $penalisedPairs)) {
                    $penalisedPairs[]                = $pairKey;
                    $effectTotals[$affectedCategory] -= 2;
                }
            }
        }
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