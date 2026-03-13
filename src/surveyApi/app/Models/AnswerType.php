<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerType extends Model
{
    protected $table = 'answer_type';
    protected $primaryKey = 'id_type';
    public $timestamps = false;
}