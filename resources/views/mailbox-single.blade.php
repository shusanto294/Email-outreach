@extends('theme')

@section('content')
    @include('alerts')

    <form action="{{ route('mailbox.update', $mailbox->id) }}" method="POST">
        @csrf
        <label for="mail_username">Mail username</label>
        <input required type="text" name="mail_username" id="mail_username" value="{{ $mailbox->mail_username }}" placeholder="user@example.com" class="form-control mb-3">

        <label for="mail_password">Mail Password</label>
        <input required type="text" name="mail_password" id="mail_password" value="{{ $mailbox->mail_password }}" placeholder="*****" class="form-control mb-3">

        <label for="mail_smtp_host">Mail SMTP Host</label>
        <input required type="text" name="mail_smtp_host" id="mail_smtp_host" value="{{ $mailbox->mail_smtp_host }}" placeholder="example.com" class="form-control mb-3">

        <label for="mail_smtp_port">Mail SMTP Port</label>
        <input required type="text" name="mail_smtp_port" id="mail_smtp_port" value="{{ $mailbox->mail_smtp_port }}" placeholder="465" class="form-control mb-3">
        
        <label for="mail_host">Mail IMAP Host</label>
        <input required type="text" name="mail_imap_host" id="mail_imap_host" value="{{ $mailbox->mail_imap_host }}" placeholder="example.com" class="form-control mb-3">

        <label for="mail_port">Mail IMAP Port</label>
        <input required type="text" name="mail_imap_port" id="mail_imap_port" value="{{ $mailbox->mail_imap_port }}" placeholder="993" class="form-control mb-3">

        <label for="mail_from_name">Mail From Name</label>
        <input required type="text" name="mail_from_name" id="mail_from_name" value="{{ $mailbox->mail_from_name }}" placeholder="993" class="form-control mb-3">


        <button type="submit" class="btn btn-secondary mt-3">Update Mailbox</button>
        <a class="btn btn-secondary mt-3" href="{{ route('test.email', $mailbox->id) }}">Send Test Email</a>
    </form>
    
@endsection



