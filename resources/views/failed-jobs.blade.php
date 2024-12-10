@extends('theme')

@section('head')
    <style>
        li{
            border-bottom: 1px solid black;
        }
    </style>
@endsection

@section('content')

@foreach ($jobs as $job)
<h1>Failed Jobs</h1>
@if (session('status'))
    <p>{{ session('status') }}</p>
@endif
<ul>
    @forelse ($jobs as $job)
        <li>
            Job ID: {{ $job->id }}<br>
            Connection: {{ $job->connection }}<br>
            Queue: {{ $job->queue }}<br>
            Payload: {{ $job->payload }}<br>
            Exception: {{ $job->exception }}<br>
            Failed At: {{ $job->failed_at }}
        </li>
    @empty
        <p>No failed jobs found.</p>
    @endforelse
</ul>
@endsection

