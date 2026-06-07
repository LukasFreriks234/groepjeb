<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weekly extends Model
{
    /** @use HasFactory<\Database\Factories\WeeklyFactory> */
    use HasFactory;

    protected $fillable = [
        'weekday',
        'recurring_id'
    ];

    public function recurrings()
    {
        return $this->belongsToMany(Recurring::class, 'weekly_recurring', 'weekly_id', 'recurring_id');
    }
}
