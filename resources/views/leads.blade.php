@extends('theme')

@section('content')

<p style="text-align: right"><a href="/import" class="btn btn-secondary">Import leads</a></p>


@if(count($leads) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Name</th>
        <th scope="col">Title</th>
        <th scope="col">Company</th>
        <th scope="col">Website</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($leads as $lead)
            <tr>
                <td>{{ $lead->id }}</td>
                <td>{{ $lead->name }}</td>
                <td>{{ $lead->title }}</td>
                <td>{{ $lead->company }}</td>
                <td>{{ $lead->company_website }}</td>
            </tr>
        @endforeach
    </tbody>
  </table>

@endif


<div class="mt-5">
    {{ $leads->links() }}
</div>


  

@endsection
