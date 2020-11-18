<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherEval\Question;
use App\Models\TeacherEval\EvaluationType;

class QuestionByEvaluationTypeController extends Controller
{
    public function selfEvaluation(){
        $evaluationTypeDocencia = EvaluationType::where('code','3')->first();
        $evaluationTypeGestion = EvaluationType::where('code','4')->first();

        $question = Question::with('answers')
        ->where('evaluation_type_id',$evaluationTypeDocencia->id)
        ->orWhere('evaluation_type_id',$evaluationTypeGestion->id)
        ->get();

        if (sizeof($question)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Preguntas no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $question,
            'msg' => [
                'summary' => 'Preguntas',
                'detail' => 'Se consultó correctamente Preguntas',
                'code' => '200',
            ]], 200);
    } 
}
