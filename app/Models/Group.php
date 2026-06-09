<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_system',
        'role',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function functions()
    {
        return $this->belongsToMany(
            Functions::class,
            'function_group',
            'group_id',
            'function_id'
        );
    }

    public function outgoingRelationships()
    {
        return $this->hasMany(GroupRelationship::class, 'group_id', 'id');
    }

    public function incomingRelationships()
    {
        return $this->hasMany(GroupRelationship::class, 'related_group_id', 'id');
    }
}