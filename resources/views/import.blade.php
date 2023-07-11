@extends('theme')

@section('content')


@php
$leads = App\Models\Lead::all();
$leadCount = $leads->count();
@endphp

<p>Total Leads: {{ $leadCount }}</p>

@php
    $campaigns = App\Models\Campaign::get();
@endphp

<form action="/import" method="post" enctype="multipart/form-data">
    @csrf

    <select type="select" class="form-control mb-3" name="campaign_id">
        @foreach($campaigns as $campaign)
            <option value="{{ $campaign->id }}">{{ $campaign->subject }}</option>
        @endforeach
    </select>

    <input type="file" name="file" id="file" accept=".xls, .xlsx" class="form-control mb-3">
    <input type="submit" value="Import Now" name="Import" class="btn btn-secondary">
</form>

@if(session()->has('success'))
    <p class="mt-5">Import successfull</p>
@endif

@endsection
