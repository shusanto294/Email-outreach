<?php

namespace App\Mail;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class MyTestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $dynamicSubject;
    public $dynamicBody;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($campaign, $lead)
    {
        $subject = $campaign->subject;
        $body = $campaign->body;

        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0];

        $dynamicSubject = str_replace(["[firstname]", "[company]"], [$firstName, $lead->company], $subject);
        $dynamicBody = str_replace(["[firstname]", "[company]"], [$lead->name, $lead->company], $body);

        $this->dynamicSubject = $dynamicSubject;
        $this->dynamicBody = $dynamicBody;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {   
        return new Envelope(
            subject: $this->dynamicSubject
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'email'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    public function build()
    {
        $trackingPixelUrl = $this->generateTrackingPixelUrl($this->lead, $this->campaign);
    
        return $this->subject($this->campaign->subject)
                    ->view('email')
                    ->with([
                        'campaign' => $this->campaign,
                        'lead' => $this->lead,
                        'trackingPixelUrl' => $trackingPixelUrl,
                    ]);
    }
    

}
