<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    protected $table = 'project_tasks';

    protected $fillable = [
        'project_id', 'task_predefined_id', 'status', 'start_date', 'due_date', 'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function predefinedTask(): BelongsTo
    {
        return $this->belongsTo(PredefinedTask::class, 'task_predefined_id');
    }
}