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
        <th scope="col"><i style="color: #333;" class="fa-solid fa-eye"></i></th>
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
                {!! $email->opened == 0 ? '<i style="color: 333; opacity: .2;" class="fa-solid fa-eye-slash"></i>' : '<i style="color: green;" class="fa-solid fa-eye"></i>' !!}
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
