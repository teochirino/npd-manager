<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'project_id', 'name', 'engineer_id', 'final_date',
        'progress', 'status', 'active_phase', 'phase_date', 'category', 'metadata'
    ];

    protected $casts = [
        'final_date' => 'date',
        'phase_date' => 'date',
        'metadata' => 'array'
    ];

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function gateApprovals(): HasMany
    {
        return $this->hasMany(GateApproval::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ProjectNote::class);
    }

    public function daysUntilPhaseDate(): ?int
    {
        if (!$this->phase_date || $this->status === 'Terminado') {
            return null;
        }
        return now()->diffInDays($this->phase_date, false);
    }

    public function isAtRisk(): bool
    {
        $days = $this->daysUntilPhaseDate();
        return $days !== null && $days <= 7 && $days >= 0;
    }
}