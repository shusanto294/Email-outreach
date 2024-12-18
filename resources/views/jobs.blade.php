@extends('theme')

@section('head')
<style>
    h1 {
        margin-bottom: 20px;
    }
    ul.jobs {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    ul.jobs li {
        border-bottom: 1px solid black;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    ul.jobs pre {
        background-color: #f8f9fa;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow-x: auto;
        font-family: monospace;
        margin: 10px 0;
    }
    .jobs-header{
        display: flex;
        justify-content: space-between;
    }
</style>
@endsection

@section('content')

@include('alerts')

<div class="jobs-header">
    <h1>Queue Jobs</h1>
    <div>
        <a class="btn btn-danger" href="/delete-queue-jobs">Delete All Queue jobs</a>
    </div>
</div>

<ul class="jobs">
    @forelse ($jobs as $job)
        <li>
            <strong>Job ID:</strong> {{ $job->id }}<br>
            <strong>Queue:</strong> {{ $job->queue }}<br>
            <strong>Payload:</strong>
            <pre>{{ json_encode(json_decode($job->payload), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            <strong>Attempts:</strong> {{ $job->attempts }}<br>
            <strong>Reserved At:</strong> {{ $job->reserved_at }}<br>
            <strong>Available At:</strong> {{ $job->available_at }}<br>
            <strong>Created At:</strong> {{ $job->created_at }}
        </li>
    @empty
        <p>No queue job found.</p>
    @endforelse
</ul>

@endsection
