<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/create-list', [LeadlistController::class, 'api_create_list'])->name('api.create.list');
Route::post('/upload-leads', [LeadController::class, 'upload_leads'])->name('upload.leads');
Route::get('/get-lead-with-no-ps', [LeadController::class, 'get_lead_with_no_ps'])->name('get_lead_with_no_ps');