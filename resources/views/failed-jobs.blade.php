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
    .jobs-header {
        display: flex;
        justify-content: space-between;
    }
</style>
@endsection

@section('content')

<div class="jobs-header">
    <h1>Failed Jobs</h1>
    <div>
        <a class="btn btn-danger" href="/delete-failed-jobs">Delete All Failed jobs</a>
    </div>
</div>

@if (session('status'))
    <p>{{ session('status') }}</p>
@endif

<ul class="jobs">
    @forelse ($jobs as $job)
        @php
            // Decode the job payload and extract the command if present
            $payload = json_decode($job->payload, true);
            $command = isset($payload['data']['command']) ? unserialize($payload['data']['command']) : null;

        @endphp
        <li>
            <strong>Command:</strong>
            <pre>{{ json_encode($payload['data']['command'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

            <strong>Exception:</strong>
            <pre>{{ $job->exception }}</pre>

        </li>
    @empty
        <p>No failed jobs found.</p>
    @endforelse
</ul>

{{ $jobs->links() }}


@endsection
