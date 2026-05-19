<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateApproval extends Model
{
    protected $table = 'gate_approvals';

    protected $fillable = [
        'project_id', 'gate_key', 'status', 'items_checked', 'approved_date', 'approved_by', 'comments'
    ];

    protected $casts = [
        'items_checked' => 'array',
        'approved_date' => 'date'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isAprobado(): bool
    {
        return $this->status === 'aprobado';
    }
}