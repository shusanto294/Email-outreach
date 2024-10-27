@extends('theme')

@section('head')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<style>
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }
    
    .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
    }
    
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
    }
    
    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }
    
    input:checked + .slider {
      background-color: #2196F3;
    }
    
    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }
    
    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }
    
    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }
    
    .slider.round:before {
      border-radius: 50%;
    }

    </style>
@endsection

@section('content')

    @php
    $reliesNotSeen = App\Models\Reply::where('seen', '<', 1)->count();
    @endphp

    <div class="inline-space-between">
        @if ($reliesNotSeen)
            <a class="new-replies-count" style="color: #000;" href="/inbox">{{ $reliesNotSeen }} new {{ $reliesNotSeen > 1 ? "replies" : "reply" }}</a>
        @endif
    </div>

    {{-- <div class="row infoboxes mb-5">
        <div class="column">
            <div class="info-box">
                <div class="number">0</div>
                <div class="text">Leads added</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">0</div>
                <div class="text">Verified</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">0</div>
                <div class="text">Personalized</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">0</div>
                <div class="text">Sent </div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">0</div>
                <div class="text">Replied</div>
                
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-lg-6 mb-3">
          <p><b>Email sending on/off</b></p>
        </div>
        <div class="col-lg-6">
          <form action="{{ route('settings.send-emails') }}" method="POST" class="email-switch" id="sendEmailsForm">
            @csrf
            {{-- <p>Send emails</p> --}}
            <label class="switch">
            @php
                $sendEmailsSetting = App\Models\Setting::where('key', 'send_emails')->first();
                $sendEmails = 'off';
                if($sendEmailsSetting){
                    $sendEmails = $sendEmailsSetting->value;
                }
            @endphp
            <input type="checkbox" name="send_emails" id="sendEmails" {{ $sendEmails == 'on' ? 'checked' : '' }}>
            <span class="slider round"></span>
            </label>
        </form>
        </div>
    </div>

    @php
        $testEmailsTo = App\Models\Setting::where('key', 'send_test_emails_to')->first();
        $subjectLinePrompt = App\Models\Setting::where('key', 'subject_line_prompt')->first();
        $personalizationPrompt = App\Models\Setting::where('key', 'personalization_prompt')->first();
        $dailySendingLimit = App\Models\Setting::where('key', 'daily_sending_limit')->first();
    @endphp
    <form action="{{ route('update.settings') }}" method="POST" class="col-lg-12">
        @csrf


        <label for="personalization_prompt" class="mb-2">Personalization prompt :</label>
        <textarea rows="5" type="email" name="personalization_prompt" id="personalization_prompt" placeholder="Write your personalization ai prompt here ..." class="form-control mb-3">{{ $personalizationPrompt ? $personalizationPrompt->value : '' }}</textarea>

        <label for="subject_line_prompt" class="mb-2">Subject line prompt :</label>
        <textarea rows="5" type="email" name="subject_line_prompt" id="subject_line_prompt" placeholder="Write your subject line prompt here ..." class="form-control mb-3">{{ $subjectLinePrompt ? $subjectLinePrompt->value : '' }}</textarea>

        <label for="send_test_emails_to" class="mb-2">Send test emails to :</label>
        <textarea rows="5" type="email" name="send_test_emails_to" id="send_test_emails_to" placeholder="user@example.com, user@example2.com" class="form-control mb-3">{{ $testEmailsTo ? $testEmailsTo->value : '' }}</textarea>

        <label for="daily_sending_limit" class="mb-2">Daily Sending Limit :</label>
        <input type="number" name="daily_sending_limit" id="daily_sending_limit" placeholder="500" class="form-control mb-3" value="{{ $dailySendingLimit ? $dailySendingLimit->value : '' }}">


        <button type="submit"  class="btn btn-secondary">Save Settings</button>
    </form>


    <div class="row mt-5">
        <p>Current time: {{ now()->setTimezone(config('app.timezone'))->format('g:i A') }}</p>
        <p>Current date: {{ now()->setTimezone(config('app.timezone'))->format('d F Y') }}</p>
    </div>
    

    <div class="mt-5">
        @if ($totalJobs)
            <p>{{ $totalJobs }} Jobs are in waiting to perform background operations</p>
        @endif
    
        @if ($totalFailedJobs)
            <p class="text-danger">{{ $totalFailedJobs }} Jobs failed their operation</p>
        @endif
    </div>


    @if ($totalFailedJobs)
        <div class="row mb-3">
            <div style="widows: 100%; text-align: right;"> 
                <a class="btn btn-danger" href="/delete-failed-jobs">Delete Failed jobs</a>
            </div>
        </div>
    @endif



    @foreach ($failedJobs as $failedJob)
        <div class="card mb-3">
            <div class="card-body">
                <p class="card-text">{{ $failedJob->exception }}</p>
            </div>
        </div>
    @endforeach


@endsection


@section('footer')
    <script>
        $(document).ready(function(){
            $("#sendEmails").change(function() {
                if (this.checked) {
                    console.log('Checkbox is checked');
                }else{
                    console.log('Checkbox is unchecked');
                }
                $("#sendEmailsForm").submit();
            });
        });
    </script>
@endsection