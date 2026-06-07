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
}
