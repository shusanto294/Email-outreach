<?php

namespace App\Http\Controllers;

use App\Models\Mailbox;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;
use App\Http\Requests\StoreMailboxRequest;
use App\Http\Requests\UpdateMailboxRequest;

class MailboxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mailboxes = Mailbox::paginate(50);
        return view('mailbox', [
            'mailboxes' => $mailboxes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Mailbox::create($request->all());
        return redirect()->back()->with('success', 'Mailbox added successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMailboxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMailboxRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mailbox  $mailbox
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mailbox = Mailbox::find($id);
        return view('mailbox-single', [
            'mailbox' => $mailbox
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mailbox  $mailbox
     * @return \Illuminate\Http\Response
     */
    public function edit(Mailbox $mailbox)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMailboxRequest  $request
     * @param  \App\Models\Mailbox  $mailbox
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $mailbox = Mailbox::find($id);
        $mailbox->update($request->all());
        return redirect()->back()->with('success', 'Mailbox updated  successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mailbox  $mailbox
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {   $mailbox = Mailbox::find($id);
        $mailbox->delete();
        return redirect()->back()->with('success', 'Mailbox deleted successfully');
    }

    function sendTestmail($id) {
        $mailbox = Mailbox::find($id);
        config(['mail.mailers.smtp.host' => $mailbox->mail_smtp_host]);
        config(['mail.mailers.smtp.port' => $mailbox->mail_smtp_port]);
        config(['mail.mailers.smtp.username' => $mailbox->mail_username]);
        config(['mail.mailers.smtp.password' => $mailbox->mail_password]);
        config(['mail.from.address' => $mailbox->mail_username]);
        config(['mail.from.name' => $mailbox->mail_from_name]);
    
        $emailAddresses = Setting::where('key', 'send_test_emails_to')->first();

        if($emailAddresses){
            $cleanedString = str_replace(', ', ',', $emailAddresses->value);
            $recipients = explode(",", $cleanedString);

            $uniqueId = time() . mt_rand(1000, 9999);
            $subject = 'Test email - '. $uniqueId;
            $body = 'This is a test email generated from the outreach softwere to check the delivaribility - '. $uniqueId;
        
            Mail::html([], function (Message $message) use ($recipients, $subject, $body) {
                $textPart = new TextPart($body, 'utf-8');
                $message->to($recipients)
                        ->subject($subject)
                        ->setBody($textPart);
            });
    
            echo 'Eails sent from: ' . $mailbox->mail_username;
            echo '<br><br>';
            echo 'Emails sent to: ' . $emailAddresses->value;
        }else{
            echo 'No test emails set';
        }

    }

    public function upload(){
        return view('upload-mailboxes');
    }

    public function upload_mailboxes(Request $request)
    {
        // Fetch mailboxes data from the request
        $mailboxes = $request->input('data');
        
        // Retrieve existing mailboxes based on mail_username
        $existingMailboxes = Mailbox::whereIn('mail_username', array_column($mailboxes, 'mail_username'))->get();
        $existingEmails = $existingMailboxes->pluck('mail_username')->toArray();
        
        // Filter out the new mailboxes that don't already exist in the database
        $newMailboxes = array_filter($mailboxes, function($mailbox) use ($existingEmails) {
            return !in_array($mailbox['mail_username'], $existingEmails);
        });
        
        // Map the new mailboxes to the appropriate format for insertion
        $newMailBoxes = array_map(function($mailbox) {
            return [
                'mail_from_name' => $mailbox['mail_from_name'],
                'mail_username' => $mailbox['mail_username'],
                'mail_password' => $mailbox['mail_password'],
                'mail_smtp_host' => $mailbox['mail_smtp_host'],
                'mail_imap_host' => $mailbox['mail_imap_host'],
                'mail_smtp_port' => $mailbox['mail_smtp_port'],
                'mail_imap_port' => $mailbox['mail_imap_port'],
                'status' => $mailbox['status']
            ];
        }, $newMailboxes);
        
        // Insert the new leads into the Lead model
        Mailbox::insert($newMailBoxes);
        
        // Return the number of leads that were imported and the number of leads that were skipped
        return response()->json([
            'imported' => count($newMailBoxes),
            'skipped' => count($mailboxes) - count($newMailBoxes)
        ]);
    }

    
}
