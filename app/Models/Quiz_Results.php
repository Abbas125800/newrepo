<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz_Results extends Model

{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'percentage',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempts::class);
    }
}
