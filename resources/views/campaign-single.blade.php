@extends('theme')

@section('head')
<script src="https://cdn.tiny.cloud/1/snt0hcywpilrk80vrf5bcnfhs5gozuf0mt22xz8mawt5bf6x/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endsection

@section('content')
    <form action="{{ route('update-campaign.post', $campaign->id) }}" method="POST">
        @csrf
        <input type="text" name="campaignName" placeholder="Campaign Name" class="form-control mb-3" required>
        <p>Dynamic variables: [firstname] [company]</p>
        <input type="text" name="emailSubject" placeholder="Subject" class="form-control mb-3" value="{{ $campaign->subject }}" required>
        <textarea name="emailBody" id="" cols="30" rows="10" class="form-control mb-3">{!! $campaign->body !!}</textarea>
        <button type="submit" class="btn btn-secondary mt-3">Update campaign</button>
    </form>
@endsection

@section('footer')
<script>
    tinymce.init({
      selector: 'textarea',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ]
    });
  </script>
@endsection