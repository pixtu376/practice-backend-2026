<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{

    use HasFactory, Notifiable;
    protected $table = 'survey';
    protected $primaryKey = 'id_survey';
    protected $fillable = ['title', 'description', 'status', 'creator_id'];
    protected $guarded = [];

    public function questions() {
        return $this->hasMany(Question::class, 'survey_id', 'id_survey');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
}