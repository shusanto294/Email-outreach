@extends('theme')

@section('content')


@php
$leads = App\Models\Lead::all();
$leadCount = $leads->count();
@endphp

<p>Total Leads: {{ $leadCount }}</p>

@php
    $lists = App\Models\Leadlist::orderBy('id', 'desc')->get();
@endphp

<form action="/import" method="post" enctype="multipart/form-data">
    @csrf

    <select type="select" class="form-control mb-3" name="list_id">
        @foreach($lists as $list)
            <option value="{{ $list->id }}">{{ $list->name }}</option>
        @endforeach
    </select>

    <input type="file" name="file" id="file" accept=".xls, .xlsx" class="form-control mb-3">
    <input type="submit" value="Import Now" name="Import" class="btn btn-secondary">
</form>

@if(session()->has('success'))
    <p class="mt-5">Import successfull</p>
@endif

@endsection
