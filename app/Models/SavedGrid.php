<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedGrid extends Model
{
    protected $table = 'saved_grid';

    protected $fillable = [
        'grid_cell_id',
        'item_type',
        'function_id',
        'event_id',
        'recurring_id',
        'occurs_at',
        'route_order',
    ];

    protected $casts = [
        'occurs_at' => 'datetime',
    ];

    public function gridCell()
    {
        return $this->belongsTo(GridCell::class);
    }

    public function cityFunction()
    {
        return $this->belongsTo(Functions::class, 'function_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function recurring()
    {
        return $this->belongsTo(Recurring::class);
    }
}