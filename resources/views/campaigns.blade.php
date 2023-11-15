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

@if(count($campaigns) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Emails</th>
        <th scope="col">Sent</th>
        <th scope="col">Not Sent</th>
        <th scope="col">Opened</th>
        <th scope="col">Not Opened</th>
        <th scope="col">Opene Rate</th>
        <th scope="col" style="text-align: right">Actions</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($campaigns as $campaign)
          @php
            $emailCount = App\Models\Email::where('campaign_id', $campaign->id)->count();
            $sentCount = App\Models\Email::where('campaign_id', $campaign->id)->where('sent', '>' , 0)->count();
            $notSentCount = App\Models\Email::where('campaign_id', $campaign->id)->where('sent', '=' , null)->count();
            $openedCount = App\Models\Email::where('campaign_id', $campaign->id)->where('opened','>', 0)->count();
            $notOpenedCount = App\Models\Email::where('campaign_id', $campaign->id)->where('sent', '!=', null)->where('opened', '=', null)->count();
          @endphp
            <tr>
                <td>{{ $campaign->id }}</td>
                <td><a href="{{ route('campaign.single', $campaign->id) }}">{{ $campaign->name }}</a></td>
                <td><a href="{{ route('campaign.show.emails', $campaign->id) }}">{{ $emailCount == 0 ? 'n/a' : $emailCount; }}</a></td>

                <td>
                  <a href="{{ route('campaign.sent', $campaign->id) }}">{{ $sentCount == 0 ? 'n/a' : $sentCount; }}</a>
                </td>

                <td>
                  <a href="{{ route('campaign.not_sent', $campaign->id) }}">{{ $notSentCount == 0 ? 'n/a' : $notSentCount; }}</a>
                </td>

                <td>
                  <a href="{{ route('campaign.opened', $campaign->id) }}">{{ $openedCount == 0 ? 'n/a' : $openedCount; }}</a>
                </td>

                <td>
                  <a href="{{ route('campaign.not_opened', $campaign->id) }}">{{ $notOpenedCount == 0 ? 'n/a' : $notOpenedCount; }}</a>
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
                <td>
                  <div class="action-icons">
                    <a href="{{ route('campaign.duplicate', $campaign->id) }}"><i class="fa-regular fa-paste"></i></a>
                    <a href="{{ route('campaign.regerate_emails', $campaign->id) }}"><i class="fa-regular fa-envelope"></i></a>
                    <a href="{{ route('campaign.delete',  $campaign->id) }}"><i class="fa-regular fa-trash-can"></i></a>
                  </div>
                  
                  
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
