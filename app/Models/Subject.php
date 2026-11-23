<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_kkm',
    ];

    protected $casts = [
        'default_kkm' => 'integer',
    ];

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_subjects', 'subject_id', 'class_id');
    }
}