<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'category';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'category',
    ];

    public function functions()
    {
        return $this->hasMany(functions::class, 'category', 'category');
    }
}
