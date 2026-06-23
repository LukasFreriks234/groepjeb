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

    public function gridDynamic()
    {
        return $this->hasOne(GridDynamic::class);
    }

    public function events()
    {
        return $this->hasManyThrough(
            Event::class,
            EventgridCell::class,
            'grid_dynamics_id', 
            'id',               
            'id',               
            'event_id'         
        );
    }

    public function mainRoad()
    {
        return $this->hasOne(MainRoad::class, 'grid_cell_id');
    }

    public function isBorder()
    {
        return
            $this->x_coordinate == 0 ||
            $this->x_coordinate == 3 ||
            $this->y_coordinate == 0 ||
            $this->y_coordinate == 2;
    }
}