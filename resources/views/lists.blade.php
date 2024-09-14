@extends('theme')

@section('head')
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

@if(count($lists) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Total Leads</th>
        {{-- <th scope="col">Has WC</th>
        <th scope="col">No WC</th> --}}
        <th scope="col">Has PS</th>
        <th scope="col">NO PS</th>
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
              $noPsleadsCount = $list->leads->where('personalized_line', '')->count();
              $hasPsleadsCount = $list->leads->where('personalized_line', '!=', '')->count();
              
              $verified = $list->leads->where('verified', 1)->count();
              if($verified) {
                $verifiedPersentage = ($verified / $leadsCount) * 100;
              } else {
                $verifiedPersentage = 0;
              }

              $notAddedToCampaignCount = $list->leads->where('campaign_id', 0)->where('verified', 1)->count();
              $hasNoWebsiteContent = $list->leads->where('website_content', null)->count();



          @endphp

            <tr>
                <td>{{ $list->id }}</td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $list->name }}</a></td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $leadsCount }}</a></td>
                <td><a href="{{ route('show.has_ps.list', $list->id) }}">{{ $hasPsleadsCount }}</a></td>
                <td><a href="{{ route('show.no_ps.list', $list->id) }}">{{ $noPsleadsCount }}</a></td>
                <td><a href="{{ route('download.list', $list->id) }}">Download</a></td>
                <td><a href="{{ route('upload.list', $list->id) }}">Upload</a></td>
               
                <td>{{ number_format($verifiedPersentage, 2) }}%</td>
                <td style="text-align: right;">
                  <a class="btn btn-secondary" href="{{ route('verify.list', $list->id) }}">Verify {{ $leadsCount - $verified}}</a>
                  <a class="btn btn-secondary" href="{{ route('fetch.content', $list->id) }}">Fetch content {{ $hasNoWebsiteContent }}</a>
                  <a class="btn btn-secondary" href="{{ route('personalize.list', $list->id) }}">Personalize</a>
                  <a class="btn btn-secondary" href="{{ route('add-to-campaign.list', $list->id) }}">Add to campaign ({{ $notAddedToCampaignCount }})</a>
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



@endsection('content')
