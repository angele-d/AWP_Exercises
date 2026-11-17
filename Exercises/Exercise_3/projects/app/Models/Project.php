<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'done_jobs',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'done_jobs' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
    ];

    // Manager (creator) of the project
    public function manager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Squad members (users assigned to the project)
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }
}
