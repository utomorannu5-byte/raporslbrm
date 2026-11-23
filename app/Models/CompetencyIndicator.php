<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'name',
        'sort_order',
    ];

    protected $casts = [
        'domain_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function domain()
    {
        return $this->belongsTo(CompetencyDomain::class, 'domain_id');
    }

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_indicators', 'indicator_id', 'class_id');
    }
}
