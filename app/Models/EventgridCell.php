<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventgridCell extends Model
{
    protected $table = 'eventgrid_cells';
    protected $fillable = ['event_id', 'grid_cell_id'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function gridCell()
    {
        return $this->belongsTo(GridCell::class, 'grid_cell_id');
    }
}
