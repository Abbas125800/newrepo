<?php

namespace App\Models;

use App\Models\Attempts;
use App\Models\Questions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizzes extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'created_by',
        'is_published',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class);
    }

    public function attempts()
    {
        return $this->hasMany(Attempts::class);
    }
}
