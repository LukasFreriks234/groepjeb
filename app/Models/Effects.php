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
        $effects = self::all()->keyBy('id');
 
        $effectTotals = [];
 
        foreach ($categories as $category) {
            $effectTotals[$category->category] = 0;
        }
 
        // NORMALE EFFECTS OPTELLEN
        foreach ($cells as $cell) {
            if (!$cell->is_available && $cell->cityFunction) {
                $functionId = $cell->cityFunction->id;
                $effect = $effects->get($functionId);
 
                if ($effect) {
                    foreach ($categories as $category) {
                        $columnName = $category->category;
 
                        $effectTotals[$columnName] += (int) ($effect->getAttribute($columnName) ?? 0);
                    }
                }
            }
        }
 
        // RELATIONSHIP EFFECTS OPTELLEN
        self::addRelationshipEffects($cells, $categories, $effectTotals);
 
        return $effectTotals;
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
 
        // Track which cell pairs have already received the same-category bonus
        // to avoid counting the same pair twice (A→B and B→A).
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
 
                // SAME-CATEGORY BONUS: +2 to that category when two functions
                // of the same category are placed next to each other.
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
 
                // RELATIONSHIP EFFECTS (existing logic)
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