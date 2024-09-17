@extends('theme')

@section('head')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<style>
p.dynamic-variables span {
    padding: 5px;
}
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

@include('alerts')

<p style="text-align: right">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add New List
    </button>
</p>

<div class="progress mt-5 mb-5" style="display: none">
  <div class="progress-bar" role="progressbar" style="width: 1%" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div>
</div>

<div id="alert"></div>

@if(count($lists) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Leads</th>
        {{-- <th scope="col">Has WC</th>
        <th scope="col">No WC</th> --}}
        {{-- <th scope="col">Has PS</th>
        <th scope="col">NO PS</th> --}}
        <th scope="col">Downlaod</th>
        <th scope="col">Upload</th>
        <th scope="col">Verified</th>
        <th scope="col" style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($lists as $list)
          @php
 
              $leadsCount = $list->leads->count();
              // $noPsleadsCount = $list->leads->where('personalization', '')->count();
              // $hasPsleadsCount = $list->leads->where('personalization', '!=', '')->count();
              
              $verified = $list->leads->where('verified', 1)->count();
              $notVerified = $leadsCount - $verified;

              if($verified) {
                $verifiedPersentage = ($verified / $leadsCount) * 100;
              } else {
                $verifiedPersentage = 0;
              }

              $hasNoWebsiteContent = $list->leads->where('website_content', null)->where('verified', 1)->count();
              $isNotPersonalized = $list->leads->where('personalization', null)->where('verified', 1)->count();
              
              $notAddedToCampaignCount = $list->leads->where('campaign_id', null)->where('verified', 1)->count();

          @endphp

            <tr>
                <td>{{ $list->id }}</td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $list->name }}</a></td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $leadsCount }}</a></td>
                {{-- <td><a href="{{ route('show.has_ps.list', $list->id) }}">{{ $hasPsleadsCount }}</a></td>
                <td><a href="{{ route('show.no_ps.list', $list->id) }}">{{ $noPsleadsCount }}</a></td> --}}
                <td><a href="{{ route('download.list', $list->id) }}">Download</a></td>
                <td><a href="{{ route('upload.list', $list->id) }}">Upload</a></td>
               
                <td>{{ number_format($verifiedPersentage, 2) }}%</td>
                <td style="text-align: right;">
                  <a class="btn btn-secondary action-ajax" data-total="{{ $notVerified }}" href="{{ route('verify.list', $list->id) }}">Verify {{ $notVerified ? $notVerified : ""}}</a>
                  <a class="btn btn-secondary action-ajax" data-total="{{ $hasNoWebsiteContent }}" href="{{ route('fetch.content', $list->id) }}">Fetch content {{ $hasNoWebsiteContent ? $hasNoWebsiteContent : "" }}</a>
                  <a class="btn btn-secondary action-ajax" data-total="{{ $isNotPersonalized }}" href="{{ route('personalize.list', $list->id) }}">Personalize {{ $isNotPersonalized ? $isNotPersonalized : "" }}</a>
                  <a class="btn btn-secondary" data-total="{{ $notAddedToCampaignCount }}" href="{{ route('add-to-campaign.list', $list->id) }}">Add to campaign {{ $notAddedToCampaignCount ? $notAddedToCampaignCount : "" }}</a>
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
          <form action="{{ route('add-list.post') }}" method="POST">
            @csrf

            <input type="text" name="listName" placeholder="List Name" class="form-control mb-3" required>
            <button type="submit" class="btn btn-secondary mt-3">Create List</button>
        </form>
        </div>
      </div>
    </div>
  </div>

<div class="mt-5">
    {{ $lists->links() }}
</div>


<script>
  $(document).ready(function(){

      $('.action-ajax').click(function(e){
          e.preventDefault();
          let total = $(this).data('total');
          let processed = 0;
          let href = $(this).attr('href');
          let interval = setInterval(() => {
              $.get(href, function(response){
                  console.log(response.status);
                  if(response.status == 'success'){
                      $('#alert').hide();
                      $('.progress').show();
                      processed += response.processed;
                      let percentage = (processed / total) * 100;
                      //make the persentage varable a round figure
                      percentage = Math.round(percentage);
                      $('.progress-bar').css('width', percentage + '%');
                      $('.progress-bar').html(percentage + '%');

                  }
                  if(response.status == 'stop'){
                      clearInterval(interval);
                      $('.progress').hide();
                      $('#alert').show();
                      $('#alert').html('<div class="alert alert-success">Added to queue</div>');
                  }
              });
          }, 1000);
      });

  });
</script>


@endsection('content')
