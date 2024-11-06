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
    <a href="{{ route('upload.mailboxes') }}" class="btn btn-secondary">Uplaod Mailboxes</a>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add New Mailbox
    </button>
</p>

@if(count($mailboxes) > 0)


  @php
    $lastCampaign = App\Models\Campaign::orderby('id', 'desc')->first();
    $lastCampaignId = $lastCampaign ? $lastCampaign->id : 0;
  @endphp

  @foreach ($mailboxes as $mailbox)
    <div class="col-12 mb-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><a href="{{ route('mailbox.show', $mailbox->id) }}">{{ $mailbox->mail_username }}</a></h5>

          @php
            $totalEmailSent = App\Models\Email::where('campaign_id', $lastCampaignId)->where('mailbox_id', $mailbox->id)->count();
            $totalEmailOpened = App\Models\Email::where('campaign_id', $lastCampaignId)->where('mailbox_id', $mailbox->id)->where('opened', '>', 0)->count();
          @endphp

          <p class="card-text"><strong>Sent:</strong> {{ $totalEmailSent }}</p>
          <p class="card-text"><strong>Opened:</strong> {{ $totalEmailOpened }}</p>
          <p class="card-text"><strong>Open Rate:</strong> 
            @php
              if($totalEmailSent && $totalEmailOpened) {
                $openRate = ($totalEmailOpened / $totalEmailSent) * 100;
                echo number_format($openRate, 2) . ' %';
              } else {
                echo 'n/a';
              }
            @endphp
          </p>
          <p class="card-text"><strong>Status:</strong> 
            @if ($mailbox->status == 'on')
              <span style="background: green; color: #fff; padding: 2px 5px;">On</span>
            @else
              <span style="background: red; color: #fff; padding: 2px 5px;">Off</span>
            @endif
          </p>

          <div class="inline-buttons">
            <a target="_blank" href="{{ route('send.test.email', $mailbox->id) }}" class="btn btn-primary btn-sm">Send Test Email</a>
            <a target="_blank" href="{{ route('check.mailbox', $mailbox->id) }}" class="btn btn-secondary btn-sm">Check Inbox</a>
            <a href="{{ route('mailbox.delete', $mailbox->id) }}" class="btn btn-danger btn-sm">Delete</a>
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

            <label for="status">Status</label>
            <div class="form-check">
              <input type="radio" required name="status" id="status_on" value="on" class="form-check-input" checked>
              <label for="status_on" class="form-check-label">On</label>
            </div>
            
            <div class="form-check">
              <input type="radio" required name="status" id="status_off" value="off" class="form-check-input">
              <label for="status_off" class="form-check-label">Off</label>
            </div>
                
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