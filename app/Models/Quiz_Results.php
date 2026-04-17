<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Quiz_Results extends Model
{
    use HasFactory;

    protected $table = 'quiz_results';

    protected $fillable = [
        'attempt_id',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'percentage',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempts::class, 'attempt_id');
    }
}
