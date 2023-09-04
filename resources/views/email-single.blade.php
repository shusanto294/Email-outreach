@extends('theme')

@section('head')
    <style>
        h4 {
            background: #ddd;
            padding: 5px;
            font-size: 20px;
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
      }
      .email-subject {
        display: flex;
        align-items: center;
      }
      .email-subject i{
        margin-right: 10px;
      }
      .email-subject span{
        margin-right: 10px;
      }
    </style>
@endsection

@section('content')
    @php
        $lead = App\Models\Lead::find($email->lead_id);
    @endphp
    <h4>To</h4>
    <p> <a style="color: #212529;" href="{{ route('lead.show', $lead->id) }}">{{ $lead->name }}</a></p>
    <h4>Subject:</h4>
    <p class="email-subject">{!! $email->opened == 0 ? '<i style="opacity: .5;" class="fa-regular fa-eye-slash"></i>' : '<span class="opened">'.$email->opened.'</span>' !!} {{ $email->subject }}</p>

    <h4>Body:</h4>
    {!! $email->body !!}

    <a class="btn btn-secondary mt-4" href="{{ route('email.edit', $email->id) }}">Edit Email</a>
@endsection
