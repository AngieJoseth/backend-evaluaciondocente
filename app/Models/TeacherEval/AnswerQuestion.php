<?php

namespace App\Models\TeacherEval;

use Illuminate\Database\Eloquent\Model;

class AnswerQuestion extends Model
{
    protected $connection = 'pgsql-teacher-eval';
    protected $table = 'teacher_eval.answer_question';

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    
}
