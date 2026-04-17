<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
        'correct_text',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quizzes::class, 'quiz_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(Options::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answers::class, 'question_id');
    }

    public function correctOption(): ?Options
    {
        return $this->options->firstWhere('is_correct', true);
    }
}
