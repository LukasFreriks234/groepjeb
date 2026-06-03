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
        return $this->belongsToMany(Event::class, 'eventgrid_cells', 'grid_cell_id', 'event_id');
    }
}