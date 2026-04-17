<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Quizzes extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'starts_at',
        'ends_at',
        'created_by',
        'is_published',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Questions::class, 'quiz_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempts::class, 'quiz_id');
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function hasStarted(): bool
    {
        return $this->starts_at !== null && now()->greaterThanOrEqualTo($this->starts_at);
    }

    public function hasEnded(): bool
    {
        return $this->ends_at !== null && now()->greaterThanOrEqualTo($this->ends_at);
    }

    public function isAvailableForStudents(): bool
    {
        return $this->is_published
            && ! $this->isCancelled()
            && $this->hasStarted()
            && ! $this->hasEnded();
    }
}
