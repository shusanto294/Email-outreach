@extends('theme')

@section('content')
    @include('alerts')

    <form action="{{ route('mailbox.update', $mailbox->id) }}" method="POST">
        @csrf
        <label for="mail_username">Mail username</label>
        <input required type="text" name="mail_username" id="mail_username" value="{{ $mailbox->mail_username }}" placeholder="user@example.com" class="form-control mb-3">

        <label for="mail_password">Mail Password</label>
        <input required type="text" name="mail_password" id="mail_password" value="{{ $mailbox->mail_password }}" placeholder="*****" class="form-control mb-3">

        <label for="mail_host">Mail Host</label>
        <input required type="text" name="mail_host" id="mail_host" value="{{ $mailbox->mail_host }}" placeholder="example.com" class="form-control mb-3">

        <label for="mail_port">Mail Port</label>
        <input required type="text" name="mail_port" id="mail_port" value="{{ $mailbox->mail_port }}" placeholder="465" class="form-control mb-3">

        <label for="mail_from_address">Mail From Address</label>
        <input required type="text" name="mail_from_address" id="mail_from_address" value="{{ $mailbox->mail_from_address }}" placeholder="user@example.com" class="form-control mb-3">
        
        <label for="mail_from_address">Mail From Name</label>
        <input required type="text" name="mail_from_name" id="mail_from_address" value="{{ $mailbox->mail_from_name }}" placeholder="John Doe" class="form-control mb-3">
        
        <button type="submit" class="btn btn-secondary mt-3">Update Mailbox</button>
        <a class="btn btn-secondary mt-3" href="{{ route('test.email', $mailbox->id) }}">Send Test Email</a>
    </form>
    
@endsection



