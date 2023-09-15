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

    /* Custom css */

    .info-box {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 20px;
    }
    .info-box .number {
        font-size: 40px;
        margin: 0;
        font-weight: 700;
    }
    .email-switch{
        text-align: right;
        margin-bottom: 50px;
    }
    </style>
@endsection

@section('content')

    {{-- <form action="{{ route('settings.send-emails') }}" method="POST" class="email-switch" id="sendEmailsForm">
        @csrf
        <p>Send emails</p>
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
    </form> --}}


    <div class="row mb-5">
        <div class="col-lg-3">
            <div class="info-box">
                @php
                    $totalLeadCount = App\Models\Lead::count();
                @endphp
                <div class="number">{{ $totalLeadCount }}</div>
                <div class="text">Total Leads</div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-box">
                @php
                    $totalEmailNotSentCount = App\Models\Email::where('sent', 0)->count();
                @endphp
                <div class="number">{{ $totalEmailNotSentCount }}</div>
                <div class="text">Emails to be sent</div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-box">
                @php
                    $totalEmailSentCount = App\Models\Email::where('sent', 1)->count();
                @endphp
                <div class="number">{{ $totalEmailSentCount }}</div>
                <div class="text">Emails sent</div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-box">
                @php
                    $totalEmailOpenedSentCount = App\Models\Email::where('opened', '>', 0)->count();
                @endphp
                <div class="number">{{ $totalEmailOpenedSentCount }}</div>
                <div class="text">Emails opened</div>
            </div>
        </div>
    </div>

    @php
        $mailFromName = App\Models\Setting::where('key', 'MAIL_FROM_NAME')->first();
        $mailHost = App\Models\Setting::where('key', 'MAIL_HOST')->first();
        $mailPort = App\Models\Setting::where('key', 'MAIL_PORT')->first();
        $mailUsername = App\Models\Setting::where('key', 'MAIL_USERNAME')->first();
        $mailPassword = App\Models\Setting::where('key', 'MAIL_PASSWORD')->first();
        $mailFromAddress = App\Models\Setting::where('key', 'MAIL_FROM_ADDRESS')->first();
        $testEmailsTo = App\Models\Setting::where('key', 'TEST_EMAILS_TO')->first();
    @endphp

    <form action="{{ route('update.settings') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-6">
                <input type="text" name="MAIL_HOST" placeholder="MAIL HOST" class="form-control mb-3" value="{{ $mailHost ? $mailHost->value : '' }}">
                <input type="email" name="MAIL_USERNAME" placeholder="MAIL USERNAME" class="form-control mb-3" value="{{ $mailUsername ? $mailUsername->value : '' }}">
                <input type="password" name="MAIL_PASSWORD" placeholder="MAIL PASSWORD" class="form-control mb-3" value="{{ $mailPassword ? $mailPassword->value : '' }}">
                <button type="submit"  class="btn btn-secondary">Save Mail Settings</button>
                <a href="/test-email" class="btn btn-secondary">Send test email</a>
            </div>
            <div class="col-lg-6">
                <input type="text" name="MAIL_PORT" placeholder="MAIL PORT" class="form-control mb-3" value="{{ $mailPort ? $mailPort->value : '' }}">
                <input type="text" name="MAIL_FROM_NAME" placeholder="MAIL FROM NAME" class="form-control mb-3" value="{{ $mailFromName ? $mailFromName->value : '' }}">
                <input type="email" name="TEST_EMAILS_TO" placeholder="TEST EMAILS TO" class="form-control mb-3" value="{{ $testEmailsTo ? $testEmailsTo->value : '' }}">
                
            </div>
        </div>
    </form>

@if(session('message'))
    <div class="alert alert-success mt-4">
        {{ session('message') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
@endif

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