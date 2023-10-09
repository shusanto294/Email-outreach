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
        Add New Mailbox
    </button>
</p>

@if(count($mailboxes) > 0 )

<table class="table table-striped">
    <thead>
      <tr>
        <th scope="col">#id</th>
        <th scope="col">Mail</th>
        <th scope="col">Sent</th>
        <th scope="col">Opened</th>
        <th scope="col">Open rate</th>
        {{-- <th scope="col">Replies</th> --}}
        <th scope="col">Test</th>
        <th scope="col">Action</th>
    </thead>
    <tbody>

    @php
      $lastCampaign = App\Models\Campaign::orderby('id', 'desc')->first();
      echo $lastCampaign->id;
    @endphp

        @foreach ($mailboxes as $mailbox)
            <tr>
                <td>{{ $mailbox->id }}</td>
                <td><a href="{{ route('mailbox.show', $mailbox->id) }}">{{ $mailbox->mail_username }}</a> </td>
                @php
                    $totalEmailSent = App\Models\Email::where('campaign_id', $lastCampaign->id)->where('mailbox_id', $mailbox->id)->count();
                @endphp
                <td>{{ $totalEmailSent }}</td>
                
                @php
                    $totalEmailOpened = App\Models\Email::where('campaign_id', $lastCampaign->id)->where('mailbox_id', $mailbox->id)->where('opened', '>' , 0)->count();
                @endphp
                <td>{{ $totalEmailOpened }}</td>
                <td>
                  @php
                    if($totalEmailSent && $totalEmailOpened){
                      $openRate = ($totalEmailOpened / $totalEmailSent) * 100;
                      echo number_format($openRate, 2).' %';
                    }else{
                      echo 'n/a';
                    }
                  @endphp
                </td>
                {{-- <td><a target="_blank" href="{{ route('replies.check', $mailbox->id) }}">Check Replies</a></td> --}}
                <td><a href="{{ route('test.email', $mailbox->id) }}">Send Test Email</a></td>
                <td><a href="{{ route('mailbox.delete', $mailbox->id) }}">Delete</a></td>
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
          <form action="{{ route('mailbox.create') }}" method="POST">
            @csrf
            <label for="mail_username">Mail username</label>
            <input required type="text" name="mail_username" id="mail_username" placeholder="user@example.com" class="form-control mb-3">

            <label for="mail_password">Mail Password</label>
            <input required type="text" name="mail_password" id="mail_password" value="apple727354" class="form-control mb-3">

            <label for="mail_smtp_host">Mail SMTP Host</label>
            <input required type="text" name="mail_smtp_host" id="mail_smtp_host" value="smtp.gmail.com" class="form-control mb-3">
            
            <label for="mail_imap_host">Mail IAMP Host</label>
            <input required type="text" name="mail_imap_host" id="mail_imap_host" value="imap.gmail.com" class="form-control mb-3">

            <label for="mail_smtp_port">Mail SMTP Port</label>
            <input required type="text" name="mail_smtp_port" id="mail_smtp_port" value="465" class="form-control mb-3">

            <label for="mail_imap_port">Mail IMAP Port</label>
            <input required type="text" name="mail_imap_port" id="mamail_imap_portil_port" value="993" class="form-control mb-3">

            <label for="mail_from_address">Mail From Name</label>
            <input required type="text" name="mail_from_name" id="mail_from_address" value="Shusanto Modak" class="form-control mb-3">
            
            <button type="submit" class="btn btn-secondary mt-3">Add Mailbox</button>
        </form>
        </div>
      </div>
    </div>
  </div>

<div class="mt-5">
    {{ $mailboxes->links() }}
</div>



@endsection('content')