@extends('theme')

@section('content')

<p>From: {{ $reply->from_name }}</p>
<p>Email: {{ $reply->from_address }}</p>
<p>To: {{ $reply->to }}</p>

<h3>{{  $reply->subject }}</h3>
<div>{!!  $reply->body !!}</div>

<a class="btn btn-danger mt-4" href="{{ route('delete.reply', $reply->id) }}">Delete</a>

@endsection
