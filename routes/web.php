<?php

use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ListCampaignRelationshipController;


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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/import', function () {
    return view('import');
})->middleware(['auth', 'verified'])->name('import.get');

Route::post('/import', [LeadController::class, 'import'])->middleware(['auth', 'verified'])->name('import.post');
Route::get('/leads', [LeadController::class, 'index'])->middleware(['auth', 'verified'])->name('leads.index');

Route::get('/campaigns', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('campaigns.index');
Route::post('/add-campaign', [CampaignController::class, 'create'])->middleware(['auth', 'verified'])->name('add-campaign.post');
Route::get('/campaign/{id}', [CampaignController::class, 'show'])->middleware(['auth', 'verified'])->name('campaign.single');
Route::post('/update-campaign/{id}', [CampaignController::class, 'update'])->middleware(['auth', 'verified'])->name('update-campaign.post');


// Route::get('/send-email', function () {
//     $data = 'Data';
//     $email = Mail::to('shusanto294@gmail.com')->send(new MyTestEmail($data));
//     var_dump($email);
// });

Route::get('/send-email', [EmailController::class, 'send']);
Route::get('/track-email/{id}', [EmailController::class, 'trackEmail'])->name('track.email');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
