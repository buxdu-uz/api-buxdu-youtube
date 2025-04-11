<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Hemis\HemisController;
use App\Http\Controllers\Lessons\LessonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login',[LoginController::class,'login']);

Route::group(['middleware' => ['auth:sanctum']],function (){
    Route::post('logout',[LoginController::class,'logout']);
    Route::get('lessons',[LessonController::class,'index']);

    Route::group(['prefix' => 'admin','middleware' => ['role:admin']],function () {
        Route::get('faculties',[HemisController::class,'getAllFaculties']);
        Route::get('departments/{faculty_id}',[HemisController::class,'getAllDepartments']);
        Route::get('subjects',[HemisController::class,'getAllSubjects']);
        Route::post('lessons',[LessonController::class,'store']);
    });

    Route::group(['prefix' => 'teacher','middleware' => ['role:teacher']],function () {
        Route::get('faculties',[HemisController::class,'getAllFaculties']);
        Route::get('departments/{faculty_id}',[HemisController::class,'getAllDepartments']);
        Route::get('subjects',[HemisController::class,'getAllSubjects']);
    });
});
