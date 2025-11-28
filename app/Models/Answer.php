<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answer';

    protected $fillable = [
        'answer',
        'question_id',
        'iscorrect',
    ];

    // Mapping de la colonne iscorrect vers is_correct
    protected $casts = [
        'iscorrect' => 'boolean',
    ];

    // Accesseur pour is_correct
    public function getIsCorrectAttribute()
    {
        return $this->iscorrect;
    }

    // Mutateur pour is_correct
    public function setIsCorrectAttribute($value)
    {
        $this->attributes['iscorrect'] = $value;
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
