<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use App\Models\Ignug\State;
use App\Models\Ignug\Teacher;
use App\Models\Ignug\Catalogue;
use App\Models\TeacherEval\Evaluation;
use App\Models\TeacherEval\EvaluationType;
use App\Models\TeacherEval\DetailEvaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $state = State::where('code','1')->first();
        $evaluations = Evaluation::with('teacher','evaluationType','status','detailEvaluations')
        ->where('state_id',$state->id)->get();

        if (sizeof($evaluations)=== 0) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluaciones no encontradas',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $evaluations,
            'msg' => [
                'summary' => 'Evaluaciones',
                'detail' => 'Se consulto correctamente evaluaciones',
                'code' => '200',
            ]], 200);
    }

    public function show($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        if (!$evaluation) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluación no encontrada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $evaluation,
            'msg' => [
                'summary' => 'Evaluación',
                'detail' => 'Se consulto correctamente la evaluación',
                'code' => '200',
            ]], 200);
    }

    public function store(Request $request)
    {
        $data = $request->json()->all();

        $dataEvaluationType = $data['evaluation_type'];
        $dataTeacher = $data['teacher'];
        $dataStatus = $data['status'];
       
        $evaluation = new Evaluation();

        $teacher = Teacher::findOrFail($dataTeacher['id']);
        $evaluationType = EvaluationType::findOrFail($dataEvaluationType['id']);
        $status = Catalogue::findOrFail($dataStatus['id']);

        $evaluation->teacher()->associate($teacher);
        $evaluation->evaluationType()->associate($evaluationType);
        $evaluation->state()->associate(State::where('code', '1')->first());
        $evaluation->status()->associate($status);
        $evaluation->save();

        if (!$evaluation) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluación no creada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $evaluation,
            'msg' => [
                'summary' => 'Evaluación',
                'detail' => 'Se creo correctamente la evaluación',
                'code' => '201',
            ]], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();
        
        $dataEvaluationType = $data['evaluation_type'];
        $dataTeacher = $data['teacher'];
        $dataEvaluators = $data['evaluators'];
        $dataStatus = $data['status'];
        
        $evaluation = Evaluation::findOrFail($id);
        $teacher = Teacher::findOrFail($dataTeacher['id']);
        $evaluationType = EvaluationType::findOrFail($dataEvaluationType['id']);
        $status = Catalogue::findOrFail($dataStatus['id']);

        $evaluation->teacher()->associate($teacher);
        $evaluation->evaluationType()->associate($evaluationType);
        $evaluation->status()->associate($status);
        $evaluation->save();

        foreach($dataEvaluators as $evaluator)
        {
            $detailEvaluation = DetailEvaluation::where('evaluation_id', $id)->first();
            $detailEvaluation->detailEvaluationable()->associate(Teacher::findOrFail($evaluator['id']));
            $detailEvaluation->save();   
        }

        if (!$detailEvaluation) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluador no actualizada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $detailEvaluation,
            'msg' => [
                'summary' => 'Evaluador',
                'detail' => 'Se actualizo correctamente el evaluador',
                'code' => '201',
            ]], 201);
    }

    public function destroy($id)
    {
        $evaluation = Evaluation::findOrFail($id);

        $evaluation->state_id = '3';
        $evaluation->save();

        $detailEvaluations = DetailEvaluation::where('evaluation_id', $id)->get();
        foreach ($detailEvaluations as $detailEvaluation) {
            $detailEvaluation->state_id = '3';
            $detailEvaluation->save();
        }

        if (!$evaluation) {
            return response()->json([
                'data' => null,
                'msg' => [
                    'summary' => 'Evaluación no eliminada',
                    'detail' => 'Intenta de nuevo',
                    'code' => '404'
                ]], 404);
        }
        return response()->json(['data' => $evaluation,
            'msg' => [
                'summary' => 'Evaluación',
                'detail' => 'Se elimino correctamente la evaluación',
                'code' => '201',
            ]], 201);
    }
}
