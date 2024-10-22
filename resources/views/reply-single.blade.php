@extends('theme')

@section('content')

@php
    $lead = App\Models\Lead::where('email', $reply->from_address)->first();
@endphp

@if ($lead)
    <p>Lead Details</p>
@endif


<p>Name: {{ $reply->from_name }}</p>
<p>To: {{ $reply->to }}</p>

<h3>{{  $reply->subject }}</h3>
<div>{!!  $reply->body !!}</div>

{{-- <a class="btn btn-danger mt-4" href="{{ route('delete.reply', $reply->id) }}">Delete</a> --}}
<a class="btn btn-secondary mt-4" href="{{ route('show.respond', $reply->id) }}">Respond</a>

@endsection
