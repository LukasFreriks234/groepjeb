<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Effects extends Model
{
    protected $fillable = [
        'id',
        'Veiligheid',
        'Recreatie',
        'Milieukwaliteit',
        'Voorzieningen',
        'Mobiliteit',
    ];
    
    public $timestamps = false;

    public function function()
    {
        return $this->belongsTo(Functions::class, 'id', 'id');
    }
}
