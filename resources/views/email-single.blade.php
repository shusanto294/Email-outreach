@extends('theme')

@section('head')
    <style>
        h4 {
            background: #ddd;
            padding: 5px;
            font-size: 20px;
        }
    </style>
@endsection

@section('content')
    <h4>Subject:</h4>
    <p>{!! $email->opened == 0 ? '<i style="color: 333; opacity: .2;" class="fa-solid fa-eye-slash"></i>' : '<i style="color: green;" class="fa-solid fa-eye"></i>' !!} {{ $email->subject }}</p>

    <h4>Body:</h4>
    {!! $email->body !!}
@endsection
