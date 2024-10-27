@extends('theme')

@section('head')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
    .lead{
      font-size: 18px;
    }
    </style>
@endsection

@section('content')

@include('alerts')

{{-- <div class="leads-header">
  <div class="search-form">
    <form action="{{ route('lead.search') }}" method="POST" class="lead-search-form">
      @csrf
      @php
          $searchTest = "";
          if(isset($_POST['searchText'])){
            $searchTest = $_POST['searchText'];
          }
      @endphp
      <input type="text" placeholder="Search text" class="form-control" name="searchText" value="{{ $searchTest ? $searchTest : "" }}">
      <button type="submit" class="btn btn-secondary">Search</button>
    </form>
  </div>
  <div class="import-button">
    <a href="/import" class="btn btn-secondary">Import leads</a>
    <button class="btn btn-secondary" id="openLinks" style="margin-left: 20px;">Edit all Leads</button>
  </div>
</div> --}}

@if(count($leads) > 0 )

@foreach ($leads as $lead)
    <div class="lead card p-3 mb-4">
        <div class="lead-details">



          <p><b>{{ $lead->personalizedSubjectLine }}</b></p>
          <p>{!! $lead->personalization !!}</p>

          <hr>

          <p>
            Name: <a href="{{ route('lead.show', $lead->id) }}">{{ $lead->name }}</a> <br>
            Company: {{ $lead->company }} <br>
            Email: {{ $lead->email }} <br>
          </p>
        </div>
            
    </div>

@endforeach

@endif

<div class="mt-5">
    {{ $leads->links() }}
</div>


@endsection

@section('footer')
    <script>
        $(document).ready(function(){
            $('a').mousedown(function(){
              $('a').not($(this)).removeClass('active-link');
              $(this).addClass('active-link');
            });
        });

        $("#openLinks").click(function() {
            // Select all anchor elements within an unordered list
            $("a.leadlink").each(function() {
                // Get the href attribute of the anchor
                var link = $(this).attr("href");
                
                // Open the link in a new tab
                window.open(link, '_blank');
            });
        });
    </script>
@endsection