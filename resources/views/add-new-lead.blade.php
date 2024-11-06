@extends('theme')

@section('content')
    @include('alerts')

    <form action="{{ route('store.new.lead', $leadListId) }}" method="POST">
        @csrf

        <label for="name">Name</label>
        <input type="text" name="name" id="name" placeholder="Name" class="form-control mb-3">

        <label for="linkedin_profile">LinkedIn Profile</label>
        <input type="text" name="linkedin_profile" id="linkedin_profile" placeholder="LinkedIn Profile" class="form-control mb-3">

        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Title" class="form-control mb-3">

        <label for="company">Company</label>
        <input type="text" name="company" id="company" placeholder="Company" class="form-control mb-3">

        <label for="company_website">Company Website</label>
        <input type="text" name="company_website" id="company_website" placeholder="Company Website" class="form-control mb-3">

        <label for="location">Location</label>
        <input type="text" name="location" id="location" placeholder="Location" class="form-control mb-3">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" class="form-control mb-3" required>

        <input type="hidden" name="leadlist_id" id="leadlist_id" placeholder="Lead List ID" class="form-control mb-3" value="{{ $leadListId }}">
        
        <button type="submit" class="btn btn-secondary mt-3">Create Lead</button>
    </form>
    
@endsection
