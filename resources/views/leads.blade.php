@extends('theme')

@section('head')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
      a.icon-link {
          text-decoration: none;
          margin-right: 10px;
          font-size: 20px;
      }
      a.icon-link.website{
          text-decoration: none;
          margin-right: 0px;
          font-size: 16px;
      }
      .leads-header {
        display: flex;
        margin-bottom: 30px;
      }
      .leads-header .search-form, .leads-header .import-button{
        flex: 1
      }
      .search-form {
          display: felx;
      }

      form.lead-search-form {
          display: flex;
          max-width: 300px;
      }

      form.lead-search-form button {
          margin-left: 20px;
      }
      .search-form {
          display: felx;
      }

      form.lead-search-form {
          display: flex;
          max-width: 400px;
      }

      form.lead-search-form button {
          margin-left: 20px;
      }

      .import-button {
          display: flex;
          justify-content: flex-end;
      }
      table a{
        text-decoration: none;
        color: #000;
      }
      table a:hover{
        text-decoration: underline !Important;
      }
      table a:visited{
        color: green;
      }
      table a.active-link{
        color: green;
        text-decoration: underline;
      }
      table a:active{
        text-decoration: underline;
      }
    </style>
@endsection

@section('content')

<div class="leads-header">
  <div class="search-form">
    <form action="{{ route('lead.search') }}" method="POST" class="lead-search-form">
      @csrf
      <input type="text" placeholder="Search text" class="form-control" name="searchText">
      <button type="submit" class="btn btn-secondary">Search</button>
    </form>
  </div>
  <div class="import-button">
    <a href="/import" class="btn btn-secondary">Import leads</a>
    <button class="btn btn-secondary" id="openLinks" style="margin-left: 20px;">Open all sites</button>
  </div>
</div>

@if(count($leads) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#ID</i></th>
        <th scope="col">Company</th>
        <th scope="col">Name</th>
        <th scope="col">Website</th>
        <th scope="col">Email</th>
        {{-- <th scope="col"><i class="fa-solid fa-circle-check"></i></th> --}}
      </tr>
    </thead>
    <tbody>
        @foreach ($leads as $lead)
            <tr>
              <td>{{ $lead->id }}</td>
              <td><a class="website" href="{{ $lead->company_website }}" target="_blank">{{ $lead->company }}</a></td>
              <td>
                <a class="name" target="_blank" href="{{ route('lead.show', $lead->id) }}">{{ $lead->name }}</a>
              </td>
              <td>{{ $lead->company_website }}</td>
              <td>{{ $lead->email }}</td>
              {{-- <td>{!! $lead->verified == 'true' ? '<i style="color: green;" class="fa-solid fa-circle-check"></i>' : '<i style="color: red;" class="fa-regular fa-circle-xmark"></i>' !!}</td> --}}
            </tr>
        @endforeach
    </tbody>
  </table>

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
            $("a.website").each(function() {
                // Get the href attribute of the anchor
                var link = $(this).attr("href");
                
                // Open the link in a new tab
                window.open(link, '_blank');
            });
        });
    </script>
@endsection