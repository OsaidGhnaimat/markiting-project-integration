<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\gpt4ChatController;
use App\Http\Controllers\OpenAiController;
use App\Http\Controllers\ZyteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/gimini', [GeminiController::class, 'index'])->name('gimini');
Route::post('/giminipro', [GeminiController::class, 'generateContentWithImages'])->name('giminipro');


Route::post('/zyte', [ZyteController::class, 'osman']);

Route::get('/zyte-form', function () {
    return view('osman');
});

Route::get('/openai', [OpenAiController::class, 'index']);


Route::get('/assistant-form', [AssistantController::class, 'showAssistant'])->name('assistant');
Route::post('/assistant', [AssistantController::class, 'generateAssistantsResponse'])->name('assistant');


/////////////////////
Route::get('/gpt4-form', [gpt4ChatController::class, 'index']);
Route::post('/gpt4Chat', [gpt4ChatController::class, 'inegration'])->name('gpt4Chat');

