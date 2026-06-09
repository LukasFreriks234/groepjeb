<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupRelationshipEffects extends Model
{
    use HasFactory;

    protected $table = 'group_relationship_effects';

    protected $fillable = [
        'group_relationship_id',
        'bonus_effect',
        'penalty_effect',
    ];

    public function relationship()
    {
        return $this->belongsTo(GroupRelationship::class, 'group_relationship_id', 'id');
    }
}