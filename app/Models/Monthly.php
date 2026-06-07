<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monthly extends Model
{
    /** @use HasFactory<\Database\Factories\MonthlyFactory> */
    use HasFactory;

    protected $fillable = [
        'recurring_id',
        'day_of_month',
        'ordinal_number',
        'weekday'
    ];
}
