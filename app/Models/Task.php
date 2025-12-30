<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
protected $fillable = [
    'name',
    'user_id',
    'calendar_id',
    'notes',
    'done',
    'due_date',
    'sub_tasks',
    'attachments',
];

protected $casts = [
    'done' => 'boolean',
    'sub_tasks' => 'json',
    'attachments' => 'json',
];

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function calendar(): BelongsTo
{
    return $this->belongsTo(Calendar::class);
}
}
