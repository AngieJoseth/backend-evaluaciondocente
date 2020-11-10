<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherEval\QuestionController;
use App\Http\Controllers\TeacherEval\EvaluationTypeController;
use App\Http\Controllers\TeacherEval\PairEvaluationController;
use App\Http\Controllers\TeacherEval\SelfEvaluationController;
use App\Http\Controllers\TeacherEval\EvaluationController;
use App\Http\Controllers\TeacherEval\AnswerController;



Route::apiResource('evaluation_types',EvaluationTypeController::class);
Route::apiResource('questions', QuestionController::class);
Route::apiResource('answers', AnswerController::class);

Route::apiResource('evaluations', EvaluationController::class);
Route::apiResource('detail_evaluations', App\Http\Controllers\TeacherEval\DetailEvaluationController::class);
Route::apiResource('student_evaluations', App\Http\Controllers\TeacherEval\StudentEvaluationController::class);
Route::apiResource('self_evaluations', SelfEvaluationController::class);
Route::apiResource('pair_evaluations', PairEvaluationController::class)->except(['store']);
Route::post('pair_evaluations/teachers',[PairEvaluationController::class,'storeTeacherEvalutor']);
Route::post('pair_evaluations/authorities',[PairEvaluationController::class,'storeAuthorityEvalutor']);

Route::get('catalogues', [CatalogueController::class, 'index']);





