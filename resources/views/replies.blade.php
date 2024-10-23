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

.inline-buttons{
  display: flex;
  justify-content: flex-end;
  column-gap: 20px;
  margin-bottom: 30px;
}
</style>
@endsection

@section('content')

@include('alerts')

<div class="inline-buttons">
  <a href="{{ route('replies.refresh') }}" class="btn btn-secondary">Refresh Inbox</a>
  <a href="{{ route('replies.mark.all.as.read') }}" class="btn btn-secondary">Mark All As Read</a>
</div>



@if(count($replies) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#ID</i></th>
        <th scope="col">Subject</i></th>
        <th scope="col">From</i></th>
        <th scope="col">To</i></th>
        <th scope="col">Action</i></th>
      </tr>
    </thead>
    <tbody>
        @foreach ($replies as $reply)
            <tr>
              <td>{{ $reply->id }}</td>
              
              <td><a href="{{ route('show.reply', $reply->id) }}">{!! $reply->seen < 1 ? '<i style="color: red;margin-right: 10px;" class="fa-regular fa-bell"></i>' : '' !!}  {{ $reply->subject }}</a></td>
              <td>{{ $reply->from_address }}</td>
              <td>{{ $reply->to }}</td> 
              <td> <a href="{{ route('delete.reply', $reply->id) }}">Delete</a>  </td>
            </tr>
        @endforeach
    </tbody>
  </table>

@endif

<div class="mt-5">
    {{ $replies->links() }}
</div>

@endsection

