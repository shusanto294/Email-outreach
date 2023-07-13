@extends('theme')

@section('head')
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
  </div>
</div>

@if(count($leads) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Title</th>
        <th scope="col">Company</th>
        <th scope="col">Email</th>
        <th scope="col"><i class="fa-solid fa-link"></i></th>
      </tr>
    </thead>
    <tbody>
        @foreach ($leads as $lead)
            <tr>
                <td>{{ $lead->id }}</td>
                <td>
                  @if ($lead->linkedin_profile != 'n/a')
                  <a class="icon-link" href="{{ $lead->linkedin_profile }}" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
                  @endif
                  <a href="{{ route('lead.show', $lead->id) }}">{{ $lead->name }}</a>
                </td>
                <td>{{ $lead->title }}</td>
                <td>{{ $lead->company }}</td>
                <td>{{ $lead->email }}</td>
                <td>
                  <a class="icon-link website" href="{{ $lead->company_website }}" target="_black"><i class="fa-solid fa-up-right-from-square"></i></a>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>

@endif


<div class="mt-5">
    {{ $leads->links() }}
</div>


  

@endsection
