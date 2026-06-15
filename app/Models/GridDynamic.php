<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridDynamic extends Model
{
    protected $fillable = ['x_coordinate', 'y_coordinate', 'is_available'];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_grid_cells', 'grid_dynamics_id', 'event_id')->withPivot('route_order');
    }
}