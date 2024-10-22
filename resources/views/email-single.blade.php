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

    @if ($lead)
    <h4>To</h4>
      <p>Name: <a target="_blank" style="color: #212529;" href="{{ route('lead.show', $lead->id) }}">{{ $lead->name }}</a></p>
      <p>Comapny: <a target="_blank" style="color: #212529;" href="{{ $lead->company_website }}">{{ $lead->company }}</a></p>

    @else
      <p>To: {{ $email->reciver_name }}</p>
      <p>Email: {{ $email->sent_to }}</p>
    @endif

    <h4>Subject:</h4>
    <p class="email-subject">{!! $email->opened_count == 0 ? '<i style="opacity: .5;" class="fa-regular fa-eye-slash"></i>' : '<span class="opened">'.$email->opened_count.'</span>' !!} {{ $email->subject }}</p>

    <h4>Body:</h4>
    {!! $email->body !!}

    <div>
      {{-- <a class="btn btn-secondary mt-4" href="{{ route('email.edit', $email->id) }}">Edit Email</a>
      <a class="btn btn-danger mt-4" href="{{ route('email.delete', $email->id) }}">Delete</a> --}}
      
    </div>
@endsection