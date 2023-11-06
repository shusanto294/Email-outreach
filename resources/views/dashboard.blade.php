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

    @media(max-width: 500px){
        .btn.btn-secondary{
            width: 100%;
            margin-top: 20px;
        }
        .info-box{
            margin-bottom: 20px;
        }
    }
    </style>
@endsection

@section('content')

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
                    $totalEmailNotSentCount = App\Models\Email::where('sent', '=' , null)->count();
                @endphp
                <div class="number">{{ $totalEmailNotSentCount }}</div>
                <div class="text">Emails to be sent</div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-box">
                @php
                    $totalEmailSentCount = App\Models\Email::where('sent', '!=' , null)->count();
                @endphp
                <div class="number">{{ $totalEmailSentCount }}</div>
                <div class="text">Emails sent</div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="info-box">
                @php
                    $totalEmailsOpened = App\Models\Email::where('opened', '!=', 0)->count();
                @endphp
                <div class="number">{{ $totalEmailsOpened }}</div>
                <div class="text">Emails opened</div>
            </div>
        </div>
    </div>

    @php
        $testEmailsTo = App\Models\Setting::where('key', 'send_test_emails_to')->first();
        $ignoreRepliesKeywords = App\Models\Setting::where('key', 'ignore_replies_keywords')->first();
    @endphp
    <form action="{{ route('update.settings') }}" method="POST" class="col-lg-12">
        @csrf
        <label for="send_test_emails_to" class="mb-2">Send test emails to :</label>
        <textarea rows="5" type="email" name="send_test_emails_to" id="send_test_emails_to" placeholder="user@example.com, user@example2.com" class="form-control mb-3">{{ $testEmailsTo ? $testEmailsTo->value : '' }}</textarea>
        
        <label for="ignore_replies_keywords" class="mb-2">Ignore replies which has these keywords :</label>
        <textarea rows="5" type="email" name="ignore_replies_keywords" id="ignore_replies_keywords" placeholder="Kyword1, keyword2" class="form-control mb-3">{{ $ignoreRepliesKeywords ? $ignoreRepliesKeywords->value : '' }}</textarea>
        <button type="submit"  class="btn btn-secondary">Save Settings</button>
    </form>

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