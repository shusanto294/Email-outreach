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
  <p>To: <a href="{{ route('lead.show', $email->lead_id) }}">{{ $email->sent_to }}</a></p>
  <p>From: {{ $email->sent_from }}</p>

    <h4>Subject:</h4>
    <p class="email-subject"> {{ $email->subject }}</p>

    <h4>Body:</h4>
    {!! $email->body !!}

    <div>
      {{-- <a class="btn btn-secondary mt-4" href="{{ route('email.edit', $email->id) }}">Edit Email</a>
      <a class="btn btn-danger mt-4" href="{{ route('email.delete', $email->id) }}">Delete</a> --}}
      
    </div>
@endsection