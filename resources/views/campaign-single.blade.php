@extends('theme')

@section('head')
<!-- include jquery and summernote css/js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
@endsection

@section('content')
    <form action="{{ route('update-campaign.post', $campaign->id) }}" method="POST">
        @csrf
        <input type="text" name="campaignName" placeholder="Campaign Name" class="form-control mb-3" value="{{ $campaign->name }}" required>
        <p>Dynamic variables: [firstname] [company]</p>
        <input type="text" name="emailSubject" placeholder="Subject" class="form-control mb-3" value="{{ $campaign->subject }}" required>
        <textarea id="summernote" name="emailBody" id="" cols="30" rows="10" class="form-control mb-3">{!! $campaign->body !!}</textarea>
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