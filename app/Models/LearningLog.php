<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LearningLog extends Model
{
    /** @use HasFactory<\Database\Factories\LearningLogFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'topic',
        'summary',
        'duration',
        'resources',
        'project_id',
        'task_id',
    ];

    protected $casts = [
        'resources' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
