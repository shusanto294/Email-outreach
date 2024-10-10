@extends('theme')

@section('head')
<style>
    table tr td a.btn.btn-secondary {
        text-decoration: none !Important;
    }
</style>
@endsection

@section('content')

@php
    $campaigns = App\Models\Campaign::orderBy('id', 'desc')->get();
@endphp

@include('alerts')

<form action="{{ route('add-to-campaign.post', $list->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <p>{{ $list->name }}</p>
    <input type="hidden" value="{{ $list->id }}" name="list_id">
    <select type="select" class="form-control mb-3" name="campaign_id">
        @foreach($campaigns as $campaign)
            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
        @endforeach
    </select>

    <input type="submit" value="Add to Campaign" name="Import" class="btn btn-secondary">
</form>

@endsection
