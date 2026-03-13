<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';
    protected $primaryKey = 'id_question';
    public $timestamps = false;
    protected $fillable = ['survey_id', 'question_text', 'type_id'];

    public function options() {
        return $this->hasMany(QuestionOption::class, 'question_id', 'id_question');
    }
    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id', 'id_survey');
    }

    public function type()
    {
        return $this->belongsTo(AnswerType::class, 'type_id', 'id_type');
    }
}