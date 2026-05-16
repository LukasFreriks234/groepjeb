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
        'Environment',
        'Facilities',
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

        $categoryMap = [
            'Environmental Quality' => 'Milieukwaliteit',
            'Mobility' => 'Mobiliteit',
            'Recreation' => 'Recreatie',
            'Safety' => 'Veiligheid',
            'Services' => 'Voorzieningen',
        ];

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
                        $englishCategory = $category->category;
                        $dutchColumn = $categoryMap[$englishCategory] ?? null;

                        if ($dutchColumn) {
                            $effectTotals[$englishCategory] += (int) ($effect->getAttribute($dutchColumn) ?? 0);
                        }
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