<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainRoad extends Model
{
    protected $fillable = ['grid_cell_id'];

    public function cell()
    {
        return $this->belongsTo(GridCell::class, 'grid_cell_id');
    }
}
