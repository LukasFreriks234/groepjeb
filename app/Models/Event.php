<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'image_url',
        'recurring_id',
        'start_date',
        'time',
        'length',
        'length_unit',
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
        return $this->belongsToMany(GridCell::class, 'eventgrid_cells', 'event_id', 'grid_cell_id');
    }

    public function recurring()
    {
        return $this->belongsTo(Recurring::class);
    }

    public function getTypeAttribute()
    {
        return $this->recurring_id === null ? 'Oneoff' : 'Recurring';
    }
}
