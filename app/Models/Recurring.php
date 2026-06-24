<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurring extends Model
{
    /** @use HasFactory<\Database\Factories\RecurringFactory> */
    use HasFactory;
    protected $fillable = [
        'end_date',
        'frequency',
        'amount',
    ];

    public function weekly()
    {
        return $this->hasMany(Weekly::class, 'recurring_id');
    }

    public function monthly()
    {
        return $this->hasMany(Monthly::class, 'recurring_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'recurring_id');
    }
}
