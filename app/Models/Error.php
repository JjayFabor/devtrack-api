<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Error extends Model
{
    /** @use HasFactory<\Database\Factories\ErrorFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'code_snippet',
        'cause',
        'resolution',
        'severity',
        'status',
        'task_id',
        'project_id',
    ];

    protected $casts = [
        'code_snippet' => 'array',
        'cause' => 'array',
        'resolution' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
