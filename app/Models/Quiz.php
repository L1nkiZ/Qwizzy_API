<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quiz';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relations
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_question', 'quizz_id', 'question_id');
    }

    public function userQuizzes()
    {
        return $this->hasMany(UserQuiz::class, 'quiz_id');
    }
}
