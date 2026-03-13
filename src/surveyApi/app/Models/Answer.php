<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answer';
    protected $primaryKey = 'id_answer';
    public $timestamps = false;

    protected $fillable = ['user_id', 'question_id', 'option_id', 'text_answer'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id_question');
    }
}