<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEffect extends Model
{
    protected $fillable = [
        'event_id', 
        'Safety', 
        'Recreation', 
        'Environmental Quality', 
        'Services', 
        'Mobility'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
