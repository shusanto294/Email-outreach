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
      table a{
        text-decoration: none;
        color: #333;
      }
      table a:hover{
        text-decoration: underline !Important;
      }
      table a:visited{
        color: green;
      }
      .opened {
          background: green;
          width: 25px;
          color: #fff;
          text-align: center;
          border-radius: 50%;
          height: 25px;
          font-size: 16px;
          display: flex;
          justify-content: center;
          align-items: center;
          margin: 0 auto;
      }
    </style>
@endsection

@section('content')



@if(count($emails) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#ID</i></th>
        <th scope="col">To</i></th>
        <th scope="col">Subject</i></th>
        <th scope="col">Sent</i></th>
        <th scope="col" style="text-align: center;">Opened</i></th>
      </tr>
    </thead>
    <tbody>
        @foreach ($emails as $email)
            <tr>
              <td>{{ $email->id }}</td>
              <td>
                @php
                    $lead = App\Models\Lead::find($email->lead_id);
                @endphp
                <a href="{{ route('lead.show', $email->lead_id) }}">{{ $lead->name }}</a>
              </td>
              <td>
                <a href="{{ route('email.single', $email->id) }}">{{ $email->subject }}</a>
              </td>
              <td>
                {!! $email->sent == 0 ? '' : '<i class="fa-solid fa-check"></i>' !!}
              </td>
              <td style="text-align: center;">
                {!! $email->opened == 0 ? '' : '<div class="opened">'.$email->opened.'</div>' !!}
              </td>
            </tr>
        @endforeach
    </tbody>
  </table>

@endif


<div class="mt-5">
    {{ $emails->links() }}
</div>


  

@endsection
