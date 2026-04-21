<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
    ];

    public function functions()
    {
        return $this->hasMany(functions::class, 'category', 'id');
    }
}
