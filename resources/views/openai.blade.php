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
        Add API Key
    </button>
</p>

@if(count($apikeys) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Key</th>
        <th scope="col">Input tocken</th>
        <th scope="col">Output tocken</th>
        <th scope="col">Spent</th>
        <th scope="col" style="text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($apikeys as $key)
          @php
            $dynamicValues = 'Value';
          @endphp
            <tr>
                <td>{{ $key->id }}</td>
                <td>{{ $key->key }}</td>
                <td>{{ $key->input_tocken }}</td>
                <td>{{ $key->output_tocken }}</td>
                <td>
                  @php
                      $inputCost = ($key->input_tocken / 1000) * 0.0010;
                      $outputCost = ($key->output_tocken / 1000) * 0.0020;

                      $cost = $inputCost + $outputCost;
                      echo '$'. number_format($cost, 2);
                  @endphp
                </td>
                <td style="text-align: right;"><a class="btn btn-danger" href="{{ route('delete_api_key', $key->id ) }}">Delete API Key</a></td>
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
          <form action="{{ route('add_new_key') }}" method="POST">
            @csrf

            <input type="text" name="apikey" placeholder="List Name" class="form-control mb-3" required>
            <button type="submit" class="btn btn-secondary mt-3">Add API Key</button>
        </form>
        </div>
      </div>
    </div>
  </div>

<div class="mt-5">
    {{ $apikeys->links() }}
</div>



@endsection('content')
