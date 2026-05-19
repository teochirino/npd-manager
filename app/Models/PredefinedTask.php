<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PredefinedTask extends Model
{
    protected $table = 'tasks_predefined';

    protected $fillable = [
        'phase_id', 'name', 'department', 'default_duration', 'order'
    ];

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function projectTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'task_predefined_id');
    }
}