@extends('theme')

@section('head')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
      a.icon-link {
          text-decoration: none;
          margin-right: 10px;
          font-size: 20px;
      }
      a.icon-link.website{
          text-decoration: none;
          margin-right: 0px;
          font-size: 16px;
      }
      .leads-header {
        display: flex;
        margin-bottom: 30px;
      }
      .leads-header .search-form, .leads-header .import-button{
        flex: 1
      }
      .search-form {
          display: felx;
      }

      form.lead-search-form {
          display: flex;
          max-width: 300px;
      }

      form.lead-search-form button {
          margin-left: 20px;
      }
      .search-form {
          display: felx;
      }

      form.lead-search-form {
          display: flex;
          max-width: 400px;
      }

      form.lead-search-form button {
          margin-left: 20px;
      }

      .import-button {
          display: flex;
          justify-content: flex-end;
      }
      table a{
        text-decoration: none;
        color: #333;
      }
      table a:hover{
        text-decoration: underline !Important;
      }
      table a:visited{
        color: green;
      }
      .opened {
          background: green;
          width: 25px;
          color: #fff;
          text-align: center;
          border-radius: 50%;
          height: 25px;
          font-size: 16px;
          display: flex;
          justify-content: center;
          align-items: center;
          margin: 0 auto;
      }
      #sendEmailsForm {
    display: flex;
    justify-content: flex-end;
}    .switch {
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

p.dynamic-variables span {
    padding: 5px;
}
table a{
  text-decoration: none;
  color: #000;
}
table a:hover{
  text-decoration: underline;
}
form

form#sendEmailsForm p {
    margin-right: 20px;
    font-weight: bold;
}
    </style>
@endsection

@section('content')

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


@if(count($emails) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#ID</i></th>
        <th scope="col">To</i></th>
        <th scope="col">Subject</i></th>
        <th scope="col">Sent</i></th>
        <th scope="col" style="text-align: center;">Opened</i></th>
      </tr>
    </thead>
    <tbody>
        @foreach ($emails as $email)
            <tr>
              <td>{{ $email->id }}</td>
              <td>
                @php
                    $lead = App\Models\Lead::find($email->lead_id);
                @endphp
                <a href="{{ route('lead.show', $email->lead_id) }}">{{ $lead->name }}</a>
              </td>
              <td>
                <a href="{{ route('email.single', $email->id) }}">{{ $email->subject }}</a>
              </td>
              <td>
                {!! $email->sent == 0 ? '' : '<i class="fa-solid fa-check"></i>' !!}
              </td>
              <td style="text-align: center;">
                {!! $email->opened == 0 ? '' : '<div class="opened">'.$email->opened.'</div>' !!}
              </td>
            </tr>
        @endforeach
    </tbody>
  </table>

@endif


<div class="mt-5">
    {{ $emails->links() }}
</div>

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