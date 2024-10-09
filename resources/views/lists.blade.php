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
        <th scope="col">Leads</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($lists as $list)
          @php
              $leadsCount = $list->leads()->count();
          @endphp

            <tr>
                <td>{{ $list->id }}</td>
                <td><a href="{{ route('show.list', $list->id) }}">{{ $list->name }}</a></td>
                <td>{{ number_format($leadsCount) }}</td>
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
