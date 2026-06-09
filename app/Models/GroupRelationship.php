<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'related_group_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function relatedGroup()
    {
        return $this->belongsTo(Group::class, 'related_group_id', 'id');
    }

    public function effects()
    {
        return $this->hasOne(GroupRelationshipEffects::class, 'group_relationship_id', 'id');
    }
}