@extends('theme')

@section('head')
<!-- include jquery and summernote css/js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
@endsection

@section('content')
    <form action="{{ route('email.update', $email->id) }}" method="POST">
      @csrf
      <input type="text" name="subject" class="form-control mb-3" value="{{ $email->subject }}" placeholder="Subject">
      <textarea name="body" class="form-control" id="summernote" placeholder="Body">
        {{ $email->body }}
      </textarea>

      <button type="submit" class="btn btn-secondary mt-4">Update Email</button>
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
