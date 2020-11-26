<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ignug\State;
use App\Models\Ignug\Teacher;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\SchoolPeriod;
use App\Models\TeacherEval\AnswerQuestion;
use App\Models\TeacherEval\Answer;
use App\Models\TeacherEval\Question;
use App\Models\TeacherEval\SelfResult;
use App\Models\TeacherEval\EvaluationType;
use App\Models\TeacherEval\Evaluation;

class SelfEvaluationController extends Controller
{
    public function index(){
        $selfResult = SelfResult::all();

        if (sizeof($selfResult)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'AutoEvaluaciones no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $selfResult,
            'msg' => [
                'summary' => 'AutoEvaluaciones',
                'detail' => 'Se consultó correctamente las autoEvaluaciones',
                'code' => '200',
            ]], 200);
    } 

    public function store(Request $request){
        
        $data = $request->json()->all();
        
        $dataTeacher = $data['teacher'];
        $dataAnswerQuestions = $data['answer_questions'];
        $teacher = Teacher::where('user_id',$dataTeacher['id'])->first();
        $state = State::where('code', '1')->first();
        

        foreach ($dataAnswerQuestions as $eachAnswerQuestion) {
            
            $selfResult = new SelfResult();

            //Tomo el id de evaluationType para realizar validacion si ya existe el teacher y tipo de evaluacion en evaluacion.
            $evaluationTypeId = AnswerQuestion::where('id',$eachAnswerQuestion['id'])->first()->question()->first()->evaluation_type_id;
            $teacherHasEvaluation = Evaluation::where('teacher_id',$teacher->id)
            ->where('evaluation_type_id',$evaluationTypeId)
            ->first();

            if(!$teacherHasEvaluation){
            $answerQuestion = AnswerQuestion::findOrFail($eachAnswerQuestion['id']);
                 $selfResult->state()->associate($state);
                 $selfResult->teacher()->associate($teacher);
                 $selfResult->answerQuestion()->associate($answerQuestion);
                 $selfResult->save();
             }else{
                $selfResult = null;
             }
            
        }
        if(!$teacherHasEvaluation){
            $this->getResultSelf($teacher->id,$dataAnswerQuestions );
        }

        if (!$selfResult) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'AutoEvaluación no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $selfResult,
            'msg' => [
                'summary' => 'AutoEvaluaciones',
                'detail' => 'Se creó correctamente las autoEvaluaciones',
                'code' => '201',
            ]], 201);
    }

    //Metodo para realizar los calculos y sacar la nota de docencia y gestion con el porcentaje aplicado.
    public function getResultSelf( $teacherId, $AnswerQuestions ){
        
        $resultEvaluation = 0;
        foreach($AnswerQuestions as $eachAnswerQuestion){

            $answerQuestion = AnswerQuestion::where('id',$eachAnswerQuestion['id'])->first();
            $value = $answerQuestion->answer()->first()->value;
            $evaluationTypeId = $answerQuestion->question()->first()->evaluation_type_id;
            $evaluationTypeParent = EvaluationType::where('id',$evaluationTypeId)->first();
            $percentage = $evaluationTypeParent->parent()->first()->percentage;
            
            $resultEvaluation += ($value*$percentage)/100;

        }
        $this->createEvaluation($teacherId,$evaluationTypeId,$resultEvaluation);
    }

    //Metodo para guardar en la tabla evaluations.
    public function createEvaluation( $teacherId, $evaluationTypeId, $resultEvaluation ){

            $evaluation = new Evaluation();
    
            $evaluation->result = $resultEvaluation;

            $state = State::where('code','1')->first();
            $catalogueStatus = Catalogue::where('type','STATUS')->Where('code','1')->first();
            $teacher = Teacher::findOrFail($teacherId);
            $evaluationType = EvaluationType::findOrFail($evaluationTypeId);
            $schoolPeriod = SchoolPeriod::where('code',1)->first();

            $evaluation->state()->associate($state);
            $evaluation->status()->associate($catalogueStatus);
            $evaluation->teacher()->associate($teacher);
            $evaluation->evaluationType()->associate($evaluationType);
            $evaluation->schoolPeriod()->associate($schoolPeriod);
    
            $evaluation->save();
    }

    public function update(Request $request){
        return $request;
    }

    public function destroy($id){
        return $id;
    }
}
