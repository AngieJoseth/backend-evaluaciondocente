<?php

namespace App\Http\Controllers\TeacherEval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherEval\Answer;
use App\Models\Ignug\Catalogue;
use App\Models\Ignug\State;
use Illuminate\Database\Eloquent\Builder;

class AnswerController extends Controller
{
    public function index()
    {
        $state = State::where('code','1')->first();
        $answers = Answer::with('status')->where('state_id',$state->id)->get();

        return response()->json( ['data'=>$answers], 200);
    }

    public function show($id)
    {
        $answer = Answer::findOrFail($id);
        return response()->json(['data'=>$answer], 200);
    }  

    public function store(Request $request){
        $data = $request->json()->all();

        $dataAnswer = $data['answer'];
        $dataStatus= $data['status'];
       
        $answer = new Answer();
        $answer->code = $dataAnswer['code'];
        $answer->order = $dataAnswer['order'];
        $answer->name = $dataAnswer['name'];
        $answer->value = $dataAnswer['value'];

        $state = State::where('code','1')->first();
        $status = Catalogue::find($dataStatus['id']);
  
        $answer->state()->associate($state);
        $answer->status()->associate($status);
        $answer->save();

        return response()->json( ['data'=>$answer], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        $dataAnswer = $data['answer'];
        $dataStatus= $data['status'];

        $answer = Answer::findOrFail($id);
        $answer->code = $dataAnswer['code'];
        $answer->order = $dataAnswer['order'];
        $answer->name = $dataAnswer['name'];
        $answer->value = $dataAnswer['value'];

        $status = Catalogue::find($dataStatus['id']);

        $answer->status()->associate($status);
        
        $answer->save();
        
        return response()->json( ['data'=>$answer], 201);
    }

    public function destroy($id)
    {
        $answer = Answer::findOrFail($id);

        $answer->state_id = '3';
        $answer->save();

        return response()->json( ['data'=>$answer], 201);
    }


}
