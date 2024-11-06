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

@if(count($apikeys) > 0)

  @foreach ($apikeys as $key)
    <div class="col-12 mb-3">
      <div class="card">
        <div class="card-body">
          <p class="card-text"><strong>Key:</strong> {{ $key->key }}</p>
          <p class="card-text"><strong>Input Tokens:</strong> {{ $key->input_tocken }}</p>
          <p class="card-text"><strong>Output Tokens:</strong> {{ $key->output_tocken }}</p>
          <p class="card-text">
            <strong>Spent:</strong> 
            @php
              $inputCost = ($key->input_tocken / 1000) * 0.000150;
              $outputCost = ($key->output_tocken / 1000) * 0.000600;
              $cost = $inputCost + $outputCost;
              echo '$' . number_format($cost, 2);
            @endphp
          </p>
          <div class="d-flex justify-content-end">
            <a class="btn btn-danger" href="{{ route('delete_api_key', $key->id) }}">Delete API Key</a>
          </div>
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
