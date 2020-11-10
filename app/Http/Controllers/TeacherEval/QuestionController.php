<?php

namespace App\Http\Controllers\TeacherEval;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Models\TeacherEval\Question;
use App\Models\TeacherEval\Answer;
use App\Models\TeacherEval\EvaluationType;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\State;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $question = Question::where('state_id',State::where('code', '1')->first()->id)
        ->get();
        return response()->json(['data'=>$question ],200);
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        return response()->json(['data'=>$question], 200);
    }  
    public function store(Request $request){
        $data = $request->json()->all();

       $dataQuestion = $data['question'];
       $dataEvaluationType= $data['evaluation_type'];
       $dataType= $data['type'];
       $dataStatus = $data['status'];
       
        $question = new Question();
        $question->code = $dataQuestion['code'];
        $question->order = $dataQuestion['order'];
        $question->name = $dataQuestion['name'];
        $question->description = $dataQuestion['description'];
        
        $evaluationType = EvaluationType::findOrFail($dataEvaluationType['id']);
        $type = Catalogue::find($dataType['id']);
        $status = Catalogue::findOrFail($dataStatus['id']);
  
        $question->evaluationType()->associate($evaluationType);
        $question->type()->associate($type);
        $question->state()->associate(State::where('code', '1')->first());
        $question->status()->associate($status);

        $question->save();

        $answersIds = array();
        $answers = Answer::where('status_id', 11)
        ->get();
        foreach ($answers as $answer) {
            array_push($answersIds,$answer->id);
        }

        $question->answers()->attach( $answersIds); 
        
        return response()->json( ['data' => $question], 201);
    }
    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        $dataQuestion = $data['question'];
        $dataEvaluationType = $data['evaluation_type'];
        $dataType= $data['type'];
        $dataStatus = $data['status'];

        $question = Question::findOrFail($id);
        $question->code = $dataQuestion['code'];
        $question->order = $dataQuestion['order'];
        $question->name = $dataQuestion['name'];
        $question->description = $dataQuestion['description'];
        
        $type = Catalogue::find($dataType['id']);
        $evaluationType = EvaluationType::findOrFail($dataEvaluationType['id']);
        $status = Catalogue::findOrFail($dataStatus['id']);

        $question->evaluationType()->associate($evaluationType);
        $question->type()->associate($type);
        $question->status()->associate($status);
        
        $question->save();
        
        return response()->json( ['data' => $question], 201);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->state_id = '3';
        $question->save();

        return response()->json( ['data' => $question], 201);
    }

}

