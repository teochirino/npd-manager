<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sku extends Model
{
    protected $table = 'skus';

    protected $fillable = [
        'project_id', 'sku_id', 'name', 'code', 'checklist'
    ];

    protected $casts = [
        'checklist' => 'array'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getCompletionPercentage(): int
    {
        if (!$this->checklist) {
            return 0;
        }
        $total = count($this->checklist);
        $completed = count(array_filter($this->checklist));
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function isCompleto(): bool
    {
        return $this->getCompletionPercentage() === 100;
    }
}