<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Effects extends Model
{
    protected $fillable = [
        'id',
        'Safety',
        'Recreation',
        'Environment',
        'Facilities',
        'Mobility',
    ];
    
    public $timestamps = false;

    public function function()
    {
        return $this->belongsTo(Functions::class, 'id', 'id');
    }
}
