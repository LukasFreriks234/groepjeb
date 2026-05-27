<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Functions extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'image',
        'category',
        'related_function_id',
        'relationship_safety',
        'relationship_recreation',
        'relationship_environmental',
        'relationship_services',
        'relationship_mobility',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'category');
    }

    public function effects()
    {
        return $this->hasOne(Effects::class, 'id', 'id');
    }

    public function relatedFunction()
    {
        return $this->belongsTo(Functions::class, 'related_function_id', 'id');
    }
}