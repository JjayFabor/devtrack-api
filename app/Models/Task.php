<?php

namespace App\Models;

use App\Models\Error;
use App\Models\Project;
use App\Models\LearningLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'deadline',
        'is_recurring',
        'project_id',
    ];


    protected $dates = [
        'deadline',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function learningLogs()
    {
        return $this->hasMany(LearningLog::class);
    }

    public function errors()
    {
        return $this->hasMany(Error::class);
    }
}
