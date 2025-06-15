<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'properties'
    ];

    protected $casts = [
        'properties' => 'array'
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
