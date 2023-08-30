@extends('theme')

@section('head')
<!-- include jquery and summernote css/js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<style>
    a.icon-link {
        text-decoration: none;
        margin-right: 10px;
        margin-top: 10px;
        font-size: 20px;
    }
    a.icon-link.website{
        text-decoration: none;
        margin-right: 0px;
        font-size: 16px;
    }
    .email {
        padding: 20px;
        background: #ddd;
        margin-top: 30px;
    }
    </style>
@endsection

@section('content')
    <h3>{{ $lead->name }}</h3>
    <p class="mt-3"><b>Website : </b>{{ $lead->company_website }}</p>
    <p class="mt-3"><b>Linkedin Prodile : </b>{{ $lead->linkedin_profile }}</p>
    <p class="mt-3"><b>Title : </b>{{ $lead->title }}</p>
    <p><b>Company : </b>{{ $lead->company }}</p>
    <p><b>Location :</b> {{ $lead->location }}</p>
    <p><b>Email :</b> {{ $lead->email }}</p>

    <form action="{{ route('lead.update', $lead->id) }}" method="POST">
        @csrf
        <select name="leadListID" class="form-control mb-3">
            @php
                $lists = App\Models\Leadlist::get();
            @endphp
            @foreach ($lists as $list)
                <option value="{{ $list->id }}" {{ $lead->leadlist_id == $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
            @endforeach
        </select>
        <select name="subscribe" class="form-control mb-3">
            <option value="1" {{ $lead->subscribe == 1 ? 'selected' : '' }}>Subscribe</option>
            <option value="0" {{ $lead->subscribe == 0 ? 'selected' : '' }}>Un Subscribe</option>
        </select>
        <textarea id="summernote" name="personalizedLine" class="form-control mb-3" placeholder="Personalized Line">{{ $lead->personalized_line }}</textarea>

        <button type="submit" class="btn btn-secondary mt-3">Update Lead</button>
    </form>

    @if (count($emails) > 0)
        <p style="font-weight: bold; margin-top: 50px;">Emails sent</p>
    @endif

    @foreach ($emails as $email)
        <div class="email">
            <p><b>Subject:</b> {{ $email->subject }}</p>
            {!! $email->body !!}
            <!-- Add more fields as needed -->
        </div>
    @endforeach

@section('footer')
<script>
    $(document).ready(function() {
    $('#summernote').summernote({
        minHeight: 200
    });
    });
</script>
@endsection

@endsection
