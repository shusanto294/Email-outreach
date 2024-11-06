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

.action-icons{
    text-align: right;
}
.action-icons a{
    margin: 5px;
}

</style>
@endsection

@section('content')

@include('alerts')

<p style="text-align: right">
  <!-- Button trigger modal -->
  <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
      Add New Campaign
  </button>
</p>

@if(count($campaigns) > 0)

  @foreach ($campaigns as $campaign)
    @php
      $leadsCount = App\Models\Lead::where('campaign_id', $campaign->id)->count();
      $sentCount = App\Models\Lead::where('campaign_id', $campaign->id)->where('sent', 1)->count();
      $replyCount = App\Models\Reply::where('campaign_id', $campaign->id)->count();
    @endphp

    <div class="col-12 mb-3">
      <div class="card">
        <div class="card-body">
          <a href="{{ route('campaign.single', $campaign->id) }}"><h5 class="card-title">{{ $campaign->name }}</h5></a>

          <p class="card-text">
            <strong>Leads:</strong> 
            <a href="{{ route('campaign.show.leads', $campaign->id) }}">
              {{ $leadsCount == 0 ? 'n/a' : $leadsCount }}
            </a>
          </p>
          <p class="card-text">
            <strong>Sent:</strong> 
            <a href="{{ route('campaign.sent', $campaign->id) }}">
              {{ $sentCount }}
            </a>
          </p>
          <p class="card-text">
            <strong>Replied:</strong> 
            <a href="{{ route('campaign.replied', $campaign->id) }}">
              {{ $replyCount }}
            </a>
          </p>
          <p class="card-text">
            <strong>Reply Rate:</strong> 
            @php  
              if($replyCount && $sentCount){
                $replyRate = ($replyCount / $sentCount) * 100;
                echo number_format($replyRate, 2) . ' %';
              } else {
                echo 'n/a';
              }
            @endphp
          </p>
          <div class="d-flex justify-content-end">
            <a href="{{ route('campaign.duplicate', $campaign->id) }}" class="btn btn-sm btn-secondary mr-2" style="margin-right: 10px;">
              <i class="fa-regular fa-paste"></i> Duplicate
            </a>
            <a href="{{ route('campaign.delete',  $campaign->id) }}" class="btn btn-sm btn-danger">
              <i class="fa-regular fa-trash-can"></i> Delete
            </a>
          </div>
        </div>
      </div>
    </div>
  @endforeach

@endif



  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
      <div class="modal-content">
        <div class="modal-body">
          <form action="{{ route('add-campaign.post') }}" method="POST">
            @csrf
            <input type="text" name="campaignName" placeholder="Campaign Name" class="form-control mb-3" required>
            <p class="dynamic-variables">Dynamic variables: <span>[firstname]</span> <span>[company]</span>  <span>[personalizedLine]</span> <span>[website]</span></p>
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
