<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\TeacherEval\EvaluationType;
use App\Models\TeacherEval\StudentResult;
use App\Models\Ignug\State;
use App\Models\Ignug\Student;
use App\Models\Ignug\SubjectTeacher;
use App\Models\TeacherEval\AnswerQuestion;

class StudentEvaluationController extends Controller
{
    
    public function index(){
        $studentResult= StudentResult::all();
        if (sizeof($studentResult)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluacion de Estudiante a Docentes no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $studentResult,
            'msg' => [
                'summary' => 'Evaluacion de Estudiante a Docentes',
                'detail' => 'Se consultó correctamente Evaluaciones de Estudiante a Docentes',
                'code' => '200',
            ]], 200);
    } 

    public function store(Request $request)
    {
       $data = $request->json()->all();

    //    $dataStudentResult= $data['student_result'];
       $dataSubjectTeacher = $data['subject_teacher'];
       $dataAnswerQuestions = $data['answer_questions'];
       $dataStudent= $data['student'];   

        foreach($dataAnswerQuestions as $answerQuestion)
        {
            
            $studentResult= new StudentResult();
            $state = State::where('code','1')->first();
            $subjectTeacher = SubjectTeacher::findOrFail($dataSubjectTeacher['id']);
            $student = Student::findOrFail($dataStudent['id']);
            
            $studentResult->state()->associate($state);
            $studentResult->subjectTeacher()->associate($subjectTeacher);
            $studentResult->student()->associate($student);
            $studentResult->answerQuestion()->associate(AnswerQuestion::findOrFail($answerQuestion['id']));
            $studentResult->save();

        }

    //     return response()->json([
    //     'data' => [
    //         'studentResult' => $studentResult
    //     ]
    // ], 201);
    if (!$studentResult) {
        return response()->json([
            'data' => null,
            'msg' => [
                'summary' => 'Evaluacion de Estudiante a Docentes no encontradas',
                'detail' => 'Intenta de nuevo',
                'code' => '404'
            ]], 404);
    }
    return response()->json(['data' => $studentResult,
        'msg' => [
            'summary' => 'Evaluacion de Estudiante a Docentes',
            'detail' => 'Se completo correctamente evaluacion',
            'code' => '200',
        ]], 200);
    }


     //Metodo para realizar los calculos y sacar la nota de docencia y gestion con el porcentaje aplicado.
    public function getResultStudent( $teacherId, $AnswerQuestions ){
        
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
        $teacher = Teacher::findOrFail($teacherId);
        $evaluationType = EvaluationType::findOrFail($evaluationTypeId);

        $evaluation->state()->associate($state);
        $evaluation->teacher()->associate($teacher);
        $evaluation->evaluationType ()->associate($evaluationType);

        $evaluation->save();
    }
    public function update(Request $request){
        return $request;
    }

    public function destroy($id){
        return $id;
    }

}
