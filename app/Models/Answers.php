<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempts::class);
    }

    public function question()
    {
        return $this->belongsTo(Questions::class);
    }

    public function option()
    {
        return $this->belongsTo(Options::class);
    }
}