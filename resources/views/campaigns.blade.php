@extends('theme')

@section('head')
<!-- include jquery and summernote css/js -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<style>


table a{
  text-decoration: none;
  color: #000;
}
table a:hover{
  text-decoration: underline;
}

</style>
@endsection

@section('content')

@if(count($campaigns) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Emails</th>
        <th scope="col">Sent</th>
        <th scope="col">Opened</th>
        <th scope="col">Opene Rate</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->id }}</td>
                <td><a href="{{ route('campaign.single', $campaign->id) }}">{{ $campaign->name }}</a></td>
                <td>
                  @php
                    $emailCount = App\Models\Email::where('campaign_id', $campaign->id)->count();
                  @endphp
                  <a href="{{ route('campaign.show.emails', $campaign->id) }}">{{ $emailCount }}</a>
                </td>
                <td>
                  @php
                    $sentCount = App\Models\Email::where('campaign_id', $campaign->id)->where('sent', '>' , 0)->count();
                  @endphp
                  <a href="{{ route('campaign.sent', $campaign->id) }}">{{ $sentCount }}</a>
                </td>
                <td>
                  @php
                    $openedCount = App\Models\Email::where('campaign_id', $campaign->id)->where('opened','>', 0)->count();
                  @endphp
                  <a href="{{ route('campaign.opened', $campaign->id) }}">{{ $openedCount }}</a>
                </td>
                <td>
                  @php  
                  
                    if($openedCount && $sentCount){
                      $openRate = ($openedCount / $sentCount) * 100;
                      echo number_format($openRate, 2).' %';
                    }else{
                      echo 'n/a';
                    }
                    
                  @endphp
                </td>
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
          <form action="{{ route('add-campaign.post') }}" method="POST">
            @csrf
            <input type="text" name="campaignName" placeholder="Campaign Name" class="form-control mb-3" required>
            <p class="dynamic-variables">Dynamic variables: <span>[firstname]</span> <span>[company]</span>  <span>[personalizedLine]</span> </p>
            <input type="text" name="subject" placeholder="Subject" class="form-control mb-3" required>
            <textarea id="summernote" name="body" id="" cols="50" rows="10" class="form-control mb-3"></textarea>
            <button type="submit" class="btn btn-secondary mt-3">Create Campaign</button>
        </form>
        </div>
      </div>
    </div>
  </div>

<div class="mt-5">
    {{ $campaigns->links() }}
</div>

@section('footer')
<script>
  $(document).ready(function() {
    $('#summernote').summernote({
      minHeight: 200
    });
  });
</script>
@endsection

@endsection('content')
