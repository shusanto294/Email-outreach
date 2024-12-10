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
<h1>Queue Jobs</h1>
<ul>
    @forelse ($jobs as $job)
        <li>
            Job ID: {{ $job->id }}<br>
            Queue: {{ $job->queue }}<br>
            Payload: {{ $job->payload }}<br>
            Attempts: {{ $job->attempts }}<br>
            Reserved At: {{ $job->reserved_at }}<br>
            Available At: {{ $job->available_at }}<br>
            Created At: {{ $job->created_at }}
        </li>
    @empty
        <p>No jobs found.</p>
    @endforelse
</ul>
@endsection

