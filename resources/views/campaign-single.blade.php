@extends('theme')

@section('head')
<!-- include jquery and summernote css/js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<style>
  p.dynamic-variables span {
      padding: 5px;
  }
  </style>
@endsection

@section('content')

    @if(session()->has('success'))
      <p class="mt-5" style="color: green;">{{ session('success') }}l</p>
    @endif

    <form action="{{ route('update-campaign.post', $campaign->id) }}" method="POST">
        @csrf
        <input type="text" name="campaignName" placeholder="Campaign Name" class="form-control mb-3" value="{{ $campaign->name }}" required>
        <p class="dynamic-variables">Dynamic variables: <span>[firstname]</span> <span>[company]</span> <span>[personalizedSubjectLine]</span> <span>[personalization]</span> <span>[calenderLink]</span></p>
        <input type="text" name="emailSubject" placeholder="Subject" class="form-control mb-3" value="{{ $campaign->subject }}" required>
        <textarea id="summernote" name="emailBody" id="" cols="30" rows="10" class="form-control mb-3">{!! $campaign->body !!}</textarea>
        
        <a class="btn btn-secondary mt-3" href="{{ route('campaign.send.test.email', $campaign->id) }}">Send Test email</a>
        <button type="submit" class="btn btn-secondary mt-3">Update campaign</button>
    </form>

@endsection

@section('footer')
<script>
  $(document).ready(function() {
    $('#summernote').summernote({
      minHeight: 200
    });
  });
</script>
@endsection