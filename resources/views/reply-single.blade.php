@extends('theme')

@section('content')

@php
    $lead = App\Models\Lead::where('email', $reply->from_address)->first();
@endphp

<p>Website: <a target="_blank" href="{{ $lead->company_website }}">{{ $lead->company_website }}</a></p>
<p>Email: <a target="_blank" href="{{ route('lead.show', $lead->id) }}">{{ $reply->from_address }}</a></p>

<p>Name: {{ $reply->from_name }}</p>
<p>To: {{ $reply->to }}</p>

<h3>{{  $reply->subject }}</h3>
<div>{!!  $reply->body !!}</div>

{{-- <a class="btn btn-danger mt-4" href="{{ route('delete.reply', $reply->id) }}">Delete</a> --}}
<a class="btn btn-secondary mt-4" href="{{ route('show.respond', $reply->id) }}">Respond</a>

@endsection
