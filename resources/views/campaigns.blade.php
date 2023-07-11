@extends('theme')

@section('head')
<script src="https://cdn.tiny.cloud/1/snt0hcywpilrk80vrf5bcnfhs5gozuf0mt22xz8mawt5bf6x/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endsection

@section('content')

<p style="text-align: right">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add New Campaign
    </button>
</p>

@if(count($campaigns) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Subject</th>
        <th scope="col">Leads</th>
        <th scope="col">Sent</th>
        <th scope="col">Opened</th>
        <th scope="col">Replied</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->id }}</td>
                <td><a href="{{ route('campaign.single', $campaign->id) }}">{{ $campaign->subject }}</a></td>
                <td>
                  @php
                    $leadsCount = App\Models\Lead::where('campaign_id', $campaign->id)->count();
                    echo $leadsCount;
                  @endphp
                </td>
                <td>
                  @php
                    $sentCount = App\Models\Lead::where('campaign_id', $campaign->id)->where('sent', 1)->count();
                    echo $sentCount;
                  @endphp
                </td>
                <td>
                  @php
                    $openedCount = App\Models\Lead::where('campaign_id', $campaign->id)->where('opened', 1)->count();
                    echo $openedCount;
                  @endphp
                </td>
                <td>0</td>
            </tr>
        @endforeach
    </tbody>
  </table>

@endif

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
      <div class="modal-content">
        <div class="modal-body">
          <p>Dynamic variables: [firstname] [company]</p>
          <form action="{{ route('add-campaign.post') }}" method="POST">
            @csrf
            <input type="text" name="subject" placeholder="Subject" class="form-control mb-3" required>
            <textarea name="body" id="" cols="30" rows="10" class="form-control mb-3"></textarea>
            <button type="submit" class="btn btn-secondary mt-3">Create tempalte</button>
        </form>
        </div>
      </div>
    </div>
  </div>

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

@endsection('content')
