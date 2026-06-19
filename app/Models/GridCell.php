<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridCell extends Model
{
    protected $fillable = ['x_coordinate', 'y_coordinate', 'is_available', 'destination_type'];
    
    public function cityFunction()
    {
        return $this->belongsTo(Functions::class, 'destination_type', 'id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_grid_cells', 'grid_cell_id', 'event_id')->withPivot('route_order');
    }

    public function mainRoad()
    {
        return $this->hasOne(MainRoad::class);
    }

    public function isBorder()
    {
        return 
            $this->x == 0 ||
            $this->x == 3 ||
            $this->y == 0 ||
            $this->y == 2;
    }
}