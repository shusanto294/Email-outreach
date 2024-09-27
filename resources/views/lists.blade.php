@extends('theme')

@section('head')
{{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> --}}
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

@if(count($lists) > 0)

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Upload</th>
        <th scope="col">Download</th>
        <th scope="col">Leads</th>
        <th scope="col">Verified</th>
        <th scope="col">WC</th>
        <th scope="col">Personalized</th>
        <th scope="col" style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($lists as $list)
          @php
              // Using direct count queries to reduce memory consumption
              $leadsCount = $list->leads()->count();
              $verified = $list->leads()->where('verified', 1)->count();
              $verifiedPercentage = $leadsCount > 0 ? ($verified / $leadsCount) * 100 : 0;

              $fetchedWebsiteContent = $list->leads()->whereNotNull('website_content')->count();
              $fetchedWebsiteContentPercentage = $leadsCount > 0 ? ($fetchedWebsiteContent / $leadsCount) * 100 : 0;

              $personalized = $list->leads()->where('personalization', '!=' , '')->count();
              $personalizedPercentage = $leadsCount > 0 ? ($personalized / $leadsCount) * 100 : 0;


              $notAddedToCampaign = $list->leads()
                ->whereNull('campaign_id')
                ->where('verified', 1)
                ->whereNotNull('personalization')
                ->count();
          @endphp

            <tr>
                <td>{{ $list->id }}</td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $list->name }}</a></td>
                
                <td><a href="{{ route('upload.list', $list->id) }}">Upload</a></td>
                <td><a href="{{ route('download.list', $list->id) }}">Download</a></td>
                
                <td><a href="{{ route('show.list', $list->id) }}">{{ $leadsCount }}</a></td>
                <td>{{ number_format($verifiedPercentage, 2) }}%</td>
                <td>{{ number_format($fetchedWebsiteContentPercentage, 2) }}%</td>
                <td>{{ number_format($personalizedPercentage, 2) }}%</td>

                <td style="text-align: right;">
                  <a class="btn btn-secondary action-ajax" href="{{ route('verify.list', $list->id) }}">Verify</a>
                  <a class="btn btn-secondary action-ajax" href="{{ route('fetch.content', $list->id) }}">Fetch content</a>
                  <a class="btn btn-secondary action-ajax" href="{{ route('personalize.list', $list->id) }}">Personalize</a>
                  <a class="btn btn-secondary" href="{{ route('add-to-campaign.list', $list->id) }}">Add to campaign</a>
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



{{-- <script>
  $(document).ready(function(){

      $('.action-ajax').click(function(e){
          e.preventDefault();
          
          // Get the total number of leads from the button data attribute
          let total = $(this).data('total');
          let processed = 0;
          let href = $(this).attr('href');
          
          // Set an interval to periodically poll the server
          let interval = setInterval(() => {
              $.get(href, function(response){
                  
                  console.log(response);

                  // If the status is success, update the progress
                  if(total > processed){
                      $('#alert').hide();
                      $('.progress').show();
                      
                      // Accumulate the number of processed leads
                      processed = response.processed;
                      
                      // Calculate the percentage of progress and round it
                      let percentage = (processed / total) * 100;
                      percentage = Math.round(percentage);
                      
                      // Update the progress bar width and text
                      $('.progress-bar').css('width', percentage + '%');
                      $('.progress-bar').html(percentage + '%');

                      console.log('Processed: ' + processed + '/' + total);
                  }else{
                      clearInterval(interval);  // Stop polling the server
                      
                      // Hide the progress bar and show a success alert
                      $('.progress').hide();
                      $('#alert').show();
                      $('#alert').html('<div class="alert alert-success">All leads have been processed and added to the queue.</div>');
                  }
                  
              });
          }, 2000); // Poll the server every 1 second
      });

  });
</script> --}}



@endsection('content')
