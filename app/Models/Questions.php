<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quizzes::class);
    }

    public function options()
    {
        return $this->hasMany(Options::class);
    }

    public function answers()
    {
        return $this->hasMany(Answers::class);
    }
}