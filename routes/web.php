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

Route::get('/', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('home.index');
Route::get('/dashboard', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard.index');

Route::get('/import', function () {
    return view('import');
})->middleware(['auth', 'verified'])->name('import.get');

Route::post('/import', [LeadController::class, 'import'])->middleware(['auth', 'verified'])->name('import.post');
Route::get('/leads', [LeadController::class, 'index'])->middleware(['auth', 'verified'])->name('leads.index');
Route::get('/lead/{id}', [LeadController::class, 'show'])->middleware(['auth', 'verified'])->name('lead.show');
Route::post('/lead/{id}/update', [LeadController::class, 'update'])->middleware(['auth', 'verified'])->name('lead.update');
Route::post('/lead/search', [LeadController::class, 'search'])->middleware(['auth', 'verified'])->name('lead.search');

Route::get('/campaigns', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('campaigns.index');
Route::post('/add-campaign', [CampaignController::class, 'create'])->middleware(['auth', 'verified'])->name('add-campaign.post');
Route::get('/campaign/{id}', [CampaignController::class, 'show'])->middleware(['auth', 'verified'])->name('campaign.single');
Route::post('/update-campaign/{id}', [CampaignController::class, 'update'])->middleware(['auth', 'verified'])->name('update-campaign.post');

Route::get('/campaign/{id}/leads', [CampaignController::class, 'showLeads'])->middleware(['auth', 'verified'])->name('campaign.leads');
// Route::get('/campaign/{id}/sent', [CampaignController::class, 'showSent'])->middleware(['auth', 'verified'])->name('campaign.sent');
// Route::get('/campaign/{id}/opened', [CampaignController::class, 'showOpened'])->middleware(['auth', 'verified'])->name('campaign.opened');

Route::get('/campaign/{id}/sent', [EmailController::class, 'showSent'])->middleware(['auth', 'verified'])->name('campaign.sent');
Route::get('/campaign/{id}/opened', [EmailController::class, 'showOpened'])->middleware(['auth', 'verified'])->name('campaign.opened');


Route::get('/send-email', [EmailController::class, 'send']);
Route::get('/track-email/{id}', [EmailController::class, 'trackEmail'])->name('track.email');


Route::get('/emails', [EmailController::class, 'index'])->middleware(['auth', 'verified'])->name('emails.index');
Route::get('/email/{id}', [EmailController::class, 'show'])->middleware(['auth', 'verified'])->name('email.single');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
