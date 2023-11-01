<?php

use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\LeadlistController;
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

// Route::get('/', [LeadlistController::class, 'index'])->middleware(['auth', 'verified'])->name('home.index');
// Route::get('/dashboard', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard.index');

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/update-send-semails-setting', [SettingController::class, 'updateSendEmailsSetting'])->middleware(['auth', 'verified'])->name('settings.send-emails');


Route::get('/import', function () {
    return view('import');
})->middleware(['auth', 'verified'])->name('import.get');

Route::post('/import', [LeadController::class, 'import'])->middleware(['auth', 'verified'])->name('import.post');
Route::get('/leads', [LeadController::class, 'index'])->middleware(['auth', 'verified'])->name('leads.index');
Route::get('/lead/{id}', [LeadController::class, 'show'])->middleware(['auth', 'verified'])->name('lead.show');
Route::post('/lead/{id}/update', [LeadController::class, 'update'])->middleware(['auth', 'verified'])->name('lead.update');
Route::post('/lead/search', [LeadController::class, 'search'])->middleware(['auth', 'verified'])->name('lead.search');
Route::get('/verify-lead', [LeadController::class, 'verify_lead'])->name('verify.lead');

Route::get('/lists', [LeadlistController::class, 'index'])->middleware(['auth', 'verified'])->name('lists.index');
Route::post('/add-list', [LeadlistController::class, 'create'])->middleware(['auth', 'verified'])->name('add-list.post');
Route::get('/list/{id}', [LeadlistController::class, 'show'])->middleware(['auth', 'verified'])->name('show.list');
Route::get('/list/{id}/no-ps', [LeadlistController::class, 'show_no_ps'])->middleware(['auth', 'verified'])->name('show.no_ps.list');
Route::get('/list/{id}/verified', [LeadlistController::class, 'show_verified'])->middleware(['auth', 'verified'])->name('show.verified.list');
Route::get('/list/{id}/not-verified', [LeadlistController::class, 'show_not_verified'])->middleware(['auth', 'verified'])->name('show.not.verified.list');
Route::get('/list/{id}/add-to-campaign', [LeadlistController::class, 'add_to_campaign'])->middleware(['auth', 'verified'])->name('add-to-campaign.list');
Route::post('/add-to-campaign/{id}', [LeadlistController::class, 'create_emails'])->middleware(['auth', 'verified'])->name('add-to-campaign.post');

Route::get('/campaigns', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('campaigns.index');
Route::post('/add-campaign', [CampaignController::class, 'create'])->middleware(['auth', 'verified'])->name('add-campaign.post');
Route::get('/campaign/{id}', [CampaignController::class, 'show'])->middleware(['auth', 'verified'])->name('campaign.single');
Route::post('/update-campaign/{id}', [CampaignController::class, 'update'])->middleware(['auth', 'verified'])->name('update-campaign.post');

//Route::get('/campaign/{id}/leads', [CampaignController::class, 'showLeads'])->middleware(['auth', 'verified'])->name('campaign.leads');
Route::get('/campaign/{id}/emails', [CampaignController::class, 'showEmails'])->middleware(['auth', 'verified'])->name('campaign.show.emails');
Route::get('/campaign/{id}/sent', [CampaignController::class, 'showSent'])->middleware(['auth', 'verified'])->name('campaign.sent');
Route::get('/campaign/{id}/opened', [CampaignController::class, 'showOpened'])->middleware(['auth', 'verified'])->name('campaign.opened');

// Route::get('/campaign/{id}/sent', [EmailController::class, 'showSent'])->middleware(['auth', 'verified'])->name('campaign.sent');
// Route::get('/campaign/{id}/opened', [EmailController::class, 'showOpened'])->middleware(['auth', 'verified'])->name('campaign.opened');

Route::get('/send-email', [EmailController::class, 'send']);
Route::get('/test-email/{mailboxID}', [EmailController::class, 'testEmail'])->name('test.email');
Route::get('/track-email/{id}', [EmailController::class, 'trackEmail'])->name('track.email');

Route::get('/emails', [EmailController::class, 'index'])->middleware(['auth', 'verified'])->name('emails.index');
Route::get('/email/{id}', [EmailController::class, 'show'])->middleware(['auth', 'verified'])->name('email.single');
Route::get('/email/{id}/edit', [EmailController::class, 'edit'])->middleware(['auth', 'verified'])->name('email.edit');
Route::post('/email/{id}/update', [EmailController::class, 'update'])->middleware(['auth', 'verified'])->name('email.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Settings
Route::post('/update-settings', [SettingController::class, 'update'])->middleware(['auth', 'verified'])->name('update.settings');


//Mailboxes
Route::get('/mailboxes', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox.index');
Route::post('/mailbox/create', [MailboxController::class, 'create'])->middleware(['auth', 'verified'])->name('mailbox.create');
Route::get('/mailbox/{id}', [MailboxController::class, 'show'])->middleware(['auth', 'verified'])->name('mailbox.show');
Route::post('/mailbox/{id}/update', [MailboxController::class, 'update'])->middleware(['auth', 'verified'])->name('mailbox.update');
Route::get('/mailbox/{id}/delete', [MailboxController::class, 'delete'])->middleware(['auth', 'verified'])->name('mailbox.delete');
Route::get('/check-deliveribility/{id}', [MailboxController::class, 'checkDeliveribility'])->name('mailbox.check.deliveribility');

//Replies
Route::get('/replies', [ReplyController::class, 'index'])->middleware(['auth', 'verified'])->name('replies.index');
Route::get('/check-replies', [ReplyController::class, 'checkRepliesFromAllInbox'])->name('replies.check.from.all.inboxes');
Route::get('/check-replies/{mailboxID}', [ReplyController::class, 'checkReplies'])->name('replies.check');
Route::get('/delete-reply/{id}', [ReplyController::class, 'delete'])->middleware(['auth', 'verified'])->name('delete.reply');
Route::get('/reply/{id}', [ReplyController::class, 'show'])->middleware(['auth', 'verified'])->name('show.reply');


require __DIR__.'/auth.php';
