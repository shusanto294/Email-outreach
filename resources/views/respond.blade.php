@extends('theme')

@section('head')
<!-- include jquery and summernote css/js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
@endsection

@section('content')

@php
$string = $reply->body;

// Regex patterns to match <meta> tags and <style> tags along with their content
$patterns = [
    '/<meta[^>]+>/i', // Match <meta> tags
    '/<style[^>]*>.*?<\/style>/is' // Match <style> tags and their content
];

// Replace matched tags with an empty string
foreach ($patterns as $pattern) {
    $string = preg_replace($pattern, '', $string);
}

//echo $string;
@endphp

{{-- <h3>{{  $reply->subject }}</h3>
<div>{!!  $reply->body !!}</div> --}}

<form action="{{ route('send.reply', $reply->id) }}" method="POST">
    @csrf
    <input name="subject" type="text" class="form-control mb-3" value="{{  $reply->subject }}">
    <textarea name="body" class="form-control">
        Thanks for your response.
        <blockquote class="gmail_quote" style="margin:50px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex">{!!  $string !!}</blockquote>
    </textarea>

    <button type="submit" class="btn btn-primary mt-3">Send Reply</button>
</form>


@endsection


@section('footer')
<script>
    $(document).ready(function() {
    $('textarea').summernote({
        minHeight: 200
    });
    });
</script>
@endsection
