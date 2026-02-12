<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answer';

    protected $fillable = [
        'answer',
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
