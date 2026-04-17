<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
        'answer_text',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempts::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Questions::class, 'question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Options::class, 'option_id');
    }
}
