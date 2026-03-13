<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $table = 'question_option';
    protected $primaryKey = 'id_option';
    public $timestamps = false;

    protected $fillable = ['question_id', 'option_text'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id_question');
    }
}