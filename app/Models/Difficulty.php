<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Difficulty extends Model
{
    use HasFactory;

    protected $table = 'difficulty';

    protected $fillable = [
        'name',
        'point',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
