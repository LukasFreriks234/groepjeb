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

        return $effectTotals;
    }

    public static function calculateQualityOfLife($cells, $categories)
    {
        $effectTotals = self::calculateEffectTotals($cells, $categories);

        return array_sum($effectTotals);
    }
}