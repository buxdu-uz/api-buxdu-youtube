<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Hemis\HemisController;
use App\Http\Controllers\Lessons\LessonController;
use App\Http\Controllers\PDF\FileController;
use App\Http\Controllers\Teachers\TeacherController;
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
Route::post('login/hemis',[HemisController::class,'checkHemisAuth'])->middleware('cors');

Route::group(['middleware' => ['auth:sanctum']],function (){
    Route::post('logout',[LoginController::class,'logout']);
    Route::get('lessons',[LessonController::class,'index']);
//GENERATE PDF
    Route::post('generate',[FileController::class,'generatePDF']);
    Route::group(['prefix' => 'admin','middleware' => ['role:admin']],function () {
        Route::get('faculties',[HemisController::class,'getAllFaculties']);
        Route::get('teachers/{department_id}',[TeacherController::class,'getAllTeacher']);
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



////oAuth2
//Route::get('/auth/hemis', [HemisController::class, 'redirectToProvider']);
//Route::get('/callback', [HemisController::class, 'handleCallback']);
