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

  @foreach ($lists as $list)
    <div class="col-12 mb-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><a href="{{ route('show.list', $list->id) }}">{{ $list->name }}</a></h5>

          @php
              $leadsCount = $list->leads()->count();
          @endphp

          <p class="card-text"><strong>Leads:</strong> {{ number_format($leadsCount) }}</p>
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
