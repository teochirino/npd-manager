<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phase extends Model
{
    protected $table = 'phases';

    protected $fillable = [
        'name', 'gate_key', 'gate_name', 'color', 'bg_color', 'text_color', 'order'
    ];

    public function predefinedTasks(): HasMany
    {
        return $this->hasMany(PredefinedTask::class);
    }
}