<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_question';

    public $timestamps = false;

    protected $fillable = [
        'quizz_id',
        'question_id',
    ];

    /**
     * Relations
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quizz_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
