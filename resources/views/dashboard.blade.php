@extends('theme')

@section('head')
    <style>
        @media(min-width: 1000px){
            .row.infoboxes.mb-5 {
                grid-template-columns: 1fr 1fr 1fr 1fr ;
            }
        }

    </style>
@endsection

@section('content')

    @php
    $reliesNotSeen = App\Models\Reply::where('seen', '<', 1)->count();
    @endphp

    <div class="inline-space-between mb-3">
        @if ($reliesNotSeen)
            <a class="new-replies-count" style="color: #000;" href="/inbox">{{ $reliesNotSeen }} new {{ $reliesNotSeen > 1 ? "replies" : "reply" }}</a>
        @endif
    </div>

    <div class="row mb-3">
        <p>{{ now()->setTimezone(config('app.timezone'))->format('g:i A') }}</p>
        <p>{{ now()->setTimezone(config('app.timezone'))->format('l, d F Y') }}</p>
    </div>

    <b><p>Last 30 days</p></b>
    <div class="row infoboxes mb-5">
        <div class="column">
            <div class="info-box">
                <div class="number">{{ $leadsAdded }}</div>
                <div class="text">Leads added</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">{{ $emailsSent }}</div>
                <div class="text">Sent</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">{{ $clicked }}</div>
                <div class="text">Clicked</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">{{ $replied }}</div>
                <div class="text">Replied </div>
            </div>
        </div>
    </div>


    @php
        $testEmailsTo = App\Models\Setting::where('key', 'send_test_emails_to')->first();
        $subjectLinePrompt = App\Models\Setting::where('key', 'subject_line_prompt')->first();
        $personalizationPrompt = App\Models\Setting::where('key', 'personalization_prompt')->first();
        $dailySendingLimit = App\Models\Setting::where('key', 'daily_sending_limit')->first();
        $sendPerMinute = App\Models\Setting::where('key', 'send_per_minute')->first();
        $calenderLink = App\Models\Setting::where('key', 'calender_link')->first();

        $sendEmailsSetting = App\Models\Setting::where('key', 'send_emails')->first();
        $sendEmails = 'off';
        if($sendEmailsSetting){
            $sendEmails = $sendEmailsSetting->value;
        }
    @endphp
    <form action="{{ route('update.settings') }}" method="POST" class="col-lg-12">
        @csrf

        <div class="mb-3">
            <label for="send_emails" class="mb-2">Send Emails</label>

            <div>
                <input type="radio" name="send_emails" id="sendEmailsOn" value="on" {{ $sendEmails == 'on' ? 'checked' : '' }}>
                <label for="sendEmailsOn">On</label>
            </div>
            <div>
                <input type="radio" name="send_emails" id="sendEmailsOff" value="off" {{ $sendEmails == 'off' ? 'checked' : '' }}>
                <label for="sendEmailsOff">Off</label>
            </div>
        </div>


        <label for="daily_sending_limit" class="mb-2">Send per minute :</label>
        <input type="number" name="send_per_minute" id="send_per_minute" placeholder="2" class="form-control mb-3" value="{{ $sendPerMinute ? $sendPerMinute->value : '' }}">

        <label for="daily_sending_limit" class="mb-2">Daily Sending Limit :</label>
        <input type="number" name="daily_sending_limit" id="daily_sending_limit" placeholder="500" class="form-control mb-3" value="{{ $dailySendingLimit ? $dailySendingLimit->value : '' }}">

        <label for="calender_link" class="mb-2">Calender link:</label>
        <input type="text" name="calender_link" id="calender_link" placeholder="https://calendly.com/username/meeting-name" class="form-control mb-3" value="{{ $calenderLink ? $calenderLink->value : '' }}">

        <label for="send_test_emails_to" class="mb-2">Send test emails to :</label>
        <textarea rows="5" type="email" name="send_test_emails_to" id="send_test_emails_to" placeholder="user@example.com, user@example2.com" class="form-control mb-3">{{ $testEmailsTo ? $testEmailsTo->value : '' }}</textarea>

        <label for="personalization_prompt" class="mb-2">Personalization prompt :</label>
        <textarea rows="5" type="email" name="personalization_prompt" id="personalization_prompt" placeholder="Write your personalization ai prompt here ..." class="form-control mb-3">{{ $personalizationPrompt ? $personalizationPrompt->value : '' }}</textarea>

        <label for="subject_line_prompt" class="mb-2">Subject line prompt :</label>
        <textarea rows="5" type="email" name="subject_line_prompt" id="subject_line_prompt" placeholder="Write your subject line prompt here ..." class="form-control mb-3">{{ $subjectLinePrompt ? $subjectLinePrompt->value : '' }}</textarea>


        <button type="submit"  class="btn btn-secondary">Save Settings</button>
    </form>



    

    <div class="mt-5">
        @if ($totalJobs)
            <a target="_blank" href="/queue-jobs"><p>{{ $totalJobs }} Jobs are in waiting to perform background operations</p></a>
        @endif
    
        @if ($totalFailedJobs)
            <a target="_blank" class="text-danger" href="/failed-jobs"><p>{{ $totalFailedJobs }} Jobs failed their operation</p></a>
        @endif
    </div>


@endsection

