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
        <th scope="col">Has PS</th>
        <th scope="col">NO PS</th>
        <th scope="col" style="text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($lists as $list)
          @php
              $noPsleadsCount = App\Models\Lead::where('leadlist_id', $list->id)->where('personalized_line', null)->count();
              $hasPsleadsCount = App\Models\Lead::where('leadlist_id', $list->id)->where('personalized_line', '!=' , null)->count();
              $notAddedToCampaignCount = App\Models\Lead::where('leadlist_id', $list->id)->where('campaign_id', '=' , 0)->count();
          @endphp
            <tr>
                <td>{{ $list->id }}</td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $list->name }}</a></td>
                @php
                  $leadsCount = App\Models\Lead::where('leadlist_id', $list->id)->count();
                @endphp
                <td><a href="{{ route('show.list', $list->id) }}">{{ $leadsCount }}</a></td>

                <td><a href="{{ route('show.has_ps.list', $list->id) }}">{{ $hasPsleadsCount }}</a></td>
                <td><a href="{{ route('show.no_ps.list', $list->id) }}">{{ $noPsleadsCount }}</a></td>
                <td style="text-align: right;"><a class="btn btn-secondary" href="{{ route('add-to-campaign.list', $list->id) }}">Add to campaign ({{ $notAddedToCampaignCount }})</a></td>
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
