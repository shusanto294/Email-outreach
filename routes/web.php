<?php

use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ApikeyController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\LeadlistController;
use App\Http\Controllers\DashboardController;
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

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified']);

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::post('/update-send-semails-setting', [SettingController::class, 'updateSendEmailsSetting'])->middleware(['auth', 'verified'])->name('settings.send-emails');

Route::get('/import', function () {
    return view('import');
})->middleware(['auth', 'verified'])->name('import.get');

Route::post('/import', [LeadController::class, 'import'])->middleware(['auth', 'verified'])->name('import.post');
Route::get('/leads', [LeadController::class, 'index'])->middleware(['auth', 'verified'])->name('leads.index');
Route::get('/lead/{id}', [LeadController::class, 'show'])->middleware(['auth', 'verified'])->name('lead.show');
Route::post('/lead/{id}/update', [LeadController::class, 'update'])->middleware(['auth', 'verified'])->name('lead.update');
Route::get('/lead/{id}/delete', [LeadController::class, 'delete'])->middleware(['auth', 'verified'])->name('lead.delete');
Route::post('/lead/search', [LeadController::class, 'search'])->middleware(['auth', 'verified'])->name('lead.search');
Route::get('/verify-lead', [LeadController::class, 'verify_lead'])->name('verify.lead');

Route::get('/lists', [LeadlistController::class, 'index'])->middleware(['auth', 'verified'])->name('lists.index');
Route::post('/add-list', [LeadlistController::class, 'create'])->middleware(['auth', 'verified'])->name('add-list.post');
Route::get('/list/{id}', [LeadlistController::class, 'show'])->middleware(['auth', 'verified'])->name('show.list');

Route::get('/list/{id}/no-ws', [LeadlistController::class, 'show_no_ws'])->middleware(['auth', 'verified'])->name('show.no_ws.list');
Route::get('/list/{id}/has-ws', [LeadlistController::class, 'show_has_ws'])->middleware(['auth', 'verified'])->name('show.has_ws.list');
Route::get('/list/{id}/download', [LeadlistController::class, 'download'])->middleware(['auth', 'verified'])->name('download.list');

Route::get('/list/{id}/no-ps', [LeadlistController::class, 'show_no_ps'])->middleware(['auth', 'verified'])->name('show.no_ps.list');
Route::get('/list/{id}/has-ps', [LeadlistController::class, 'show_has_ps'])->middleware(['auth', 'verified'])->name('show.has_ps.list');
Route::get('/list/{id}/verified', [LeadlistController::class, 'show_verified'])->middleware(['auth', 'verified'])->name('show.verified.list');
Route::get('/list/{id}/not-verified', [LeadlistController::class, 'show_not_verified'])->middleware(['auth', 'verified'])->name('show.not.verified.list');
Route::get('/list/{id}/add-to-campaign', [LeadlistController::class, 'add_to_campaign'])->middleware(['auth', 'verified'])->name('add-to-campaign.list');
Route::post('/add-to-campaign/{id}', [LeadlistController::class, 'leadlist_leads_change_campaign_id'])->middleware(['auth', 'verified'])->name('add-to-campaign.post');

Route::get('/campaigns', [CampaignController::class, 'index'])->middleware(['auth', 'verified'])->name('campaigns.index');
Route::post('/add-campaign', [CampaignController::class, 'create'])->middleware(['auth', 'verified'])->name('add-campaign.post');
Route::get('/campaign/{id}', [CampaignController::class, 'show'])->middleware(['auth', 'verified'])->name('campaign.single');
Route::post('/update-campaign/{id}', [CampaignController::class, 'update'])->middleware(['auth', 'verified'])->name('update-campaign.post');

Route::get('/campaign/{id}/leads', [CampaignController::class, 'showLeads'])->middleware(['auth', 'verified'])->name('campaign.show.leads');
Route::get('/campaign/{id}/sent', [CampaignController::class, 'showSent'])->middleware(['auth', 'verified'])->name('campaign.sent');
Route::get('/campaign/{id}/opened', [CampaignController::class, 'showOpened'])->middleware(['auth', 'verified'])->name('campaign.opened');
Route::get('/campaign/{id}/replied', [CampaignController::class, 'showReplied'])->middleware(['auth', 'verified'])->name('campaign.replied');

Route::get('/campaign/{id}/delete', [CampaignController::class, 'delete'])->middleware(['auth', 'verified'])->name('campaign.delete');
Route::get('/campaign/{id}/duplicate', [CampaignController::class, 'duplicate'])->middleware(['auth', 'verified'])->name('campaign.duplicate');
Route::get('/campaign/{id}/regerate-emails', [CampaignController::class, 'regerate_emails'])->middleware(['auth', 'verified'])->name('campaign.regerate_emails');

Route::get('/send-email', [EmailController::class, 'send']);
Route::get('/test-email/{mailboxID}', [EmailController::class, 'testEmail'])->name('test.email');
Route::get('/track-email/{uid}', [EmailController::class, 'trackEmail'])->name('track.email');

Route::get('/emails', [EmailController::class, 'index'])->middleware(['auth', 'verified'])->name('emails.index');
Route::get('/sent', [EmailController::class, 'responded'])->middleware(['auth', 'verified'])->name('emails.replies');
Route::get('/sent/{id}', [EmailController::class, 'show'])->middleware(['auth', 'verified'])->name('email.single');
Route::get('/sent/{id}/edit', [EmailController::class, 'edit'])->middleware(['auth', 'verified'])->name('email.edit');
Route::post('/sent/{id}/update', [EmailController::class, 'update'])->middleware(['auth', 'verified'])->name('email.update');
Route::get('/sent/delete/{id}', [EmailController::class, 'delete'])->middleware(['auth', 'verified'])->name('email.delete');

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
// Route::get('/replies', [ReplyController::class, 'index'])->middleware(['auth', 'verified'])->name('replies.index');
// Route::get('/check-replies', [ReplyController::class, 'checkRepliesFromAllInbox'])->name('replies.check.from.all.inboxes');
// Route::get('/check-replies/{mailboxID}', [ReplyController::class, 'checkReplies'])->name('replies.check');
// Route::get('/delete-reply/{id}', [ReplyController::class, 'delete'])->middleware(['auth', 'verified'])->name('delete.reply');
// Route::get('/reply/{id}', [ReplyController::class, 'show'])->middleware(['auth', 'verified'])->name('show.reply');
// Route::get('/reply/{id}/respond', [ReplyController::class, 'respond'])->middleware(['auth', 'verified'])->name('show.respond');
// Route::post('/reply/{id}/send-reply', [ReplyController::class, 'send_reply'])->middleware(['auth', 'verified'])->name('send.reply');

Route::get('/inbox', [ReplyController::class, 'index'])->middleware(['auth', 'verified'])->name('replies.index');
Route::get('/check-replies', [ReplyController::class, 'checkRepliesFromAllInbox'])->name('replies.check.from.all.inboxes');
Route::get('/check-replies/{mailboxID}', [ReplyController::class, 'checkReplies'])->name('replies.check');
Route::get('/delete-reply/{id}', [ReplyController::class, 'delete'])->middleware(['auth', 'verified'])->name('delete.reply'); 
Route::get('/inbox/{id}', [ReplyController::class, 'show'])->middleware(['auth', 'verified'])->name('show.reply');
Route::get('/inbox/{id}/respond', [ReplyController::class, 'respond'])->middleware(['auth', 'verified'])->name('show.respond');
Route::post('/inbox/{id}/send-reply', [ReplyController::class, 'send_reply'])->middleware(['auth', 'verified'])->name('send.reply');

Route::get('/personalize', [LeadController::class, 'personalize'])->name('personalize');
Route::get('/skip-lead-personalization', [LeadController::class, 'skip_lead_personalization'])->name('skip_lead_personalization');

// Open AI api keys

Route::get('/open-ai', [ApikeyController::class, 'index'])->middleware(['auth', 'verified'])->name('openai.index');
Route::post('/open-ai/add-new-key', [ApikeyController::class, 'add_new_key'])->middleware(['auth', 'verified'])->name('add_new_key');
Route::get('/open-ai/delete-api-key/{id}', [ApikeyController::class, 'delete'])->middleware(['auth', 'verified'])->name('delete_api_key');

require __DIR__.'/auth.php';
