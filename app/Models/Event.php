<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name', 
        'image_url', 
        'type', 
        'recurrence_pattern', 
        'days_of_week', 
        'start_date', 
        'end_date', 
        'dynamic'
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'dynamic' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(
            Category::class, 
            'event_category', 
            'event_id', 
            'category_name', 
            'id', 
            'category'
        );
    }
    public function gridCells()
    {
        return $this->belongsToMany(GridCell::class, 'event_grid_cells', 'event_id', 'grid_cell_id')->withPivot('route_order');;
    }
}
