<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventgridCell extends Model
{
    protected $table = 'event_grid_cells';
    protected $fillable = ['event_id', 'grid_dynamics_id', 'route_order'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function gridCell()
    {
        return $this->belongsTo(GridDynamic::class,
        'grid_dynamics_id');
    }
}
