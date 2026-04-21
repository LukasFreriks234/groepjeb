<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class functions extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'image',
        'effect',
        'category',
    ];

    public function category()
    {
        return $this->belongsTo(categories::class, 'category', 'category');
    }
}
