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
          margin-left: 15px;
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
.opened-count-and-time{
  display: flex;
  justify-content: center;
}

form.inline-form {
    display: flex;
    grid-gap: 20px;
    margin-bottom: 30px;
    align-items: center;
}

form.inline-form p{
    text-wrap: nowrap;
    margin-bottom: 0;
}
/* form.inline-form select{
    width: 40%;
}
form.inline-form button{
    width: 30%;
} */
</style>
@endsection

@section('content')

@include('alerts')


@if(count($emails) > 0 )

@php
    $lists = App\Models\Leadlist::orderBy('id', 'desc')->get();
    $campaignID = 0;
    foreach ($emails as $email) {
      $campaignID = $email->campaign_id;
      break;
    }
@endphp

@if (Route::is('campaign.not_opened'))
  <form class="inline-form" action="{{ route('campaign.move_not_opened', $campaignID ) }}" method="post" enctype="multipart/form-data">
    @csrf
    <p><b>Move leads to</b></p>
    <select type="select" class="form-control" name="list_id">
        @foreach($lists as $list)
            <option value="{{ $list->id }}">{{ $list->name }}</option>
        @endforeach
    </select>
    <input type="submit" value="Move Now" class="btn btn-secondary">
  </form>
@endif

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#ID</th>
        <th scope="col">Subject</th>
        <th scope="col">Reciver Name</th>
        <th scope="col">Reciver Email</th>
        
        <th scope="col">Sent</th>

      </tr>
    </thead>
    <tbody>
        @foreach ($emails as $email)
            <tr>
              <td>{{ $email->id }}</td>
              <td>
                <a href="{{ route('email.single', $email->id) }}">{{ $email->subject }}</a>
              </td>
              <td>{{ $email->reciver_name }}</td>
              <td>{{ $email->sent_to }}</td>
              <td>
                {!! $email->sent ? \Carbon\Carbon::parse($email->sent)->format('h:i A') : ''  !!}
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