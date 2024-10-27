@extends('theme')

@section('head')
<style>
    .alert {
        margin-bottom: 40px;
    }
</style>
@endsection

@section('content')
    @include('alerts')

    <div class="buttons">
        <a class="btn btn-secondary" href="{{ route('upload.instant.data.scrapper', $id) }}">Instant Web scrapper upload</a>
        <a class="btn btn-secondary" href="{{ route('verify.list', $id) }}">Verify</a>
        <a class="btn btn-secondary" href="{{ route('fetch.content', $id) }}">Fetch content</a>
        <a class="btn btn-secondary" href="{{ route('personalize.list', $id) }}">Personalize</a>
        <a class="btn btn-secondary" href="{{ route('upload.list', $id) }}">Upload</a>
        <a class="btn btn-secondary" href="{{ route('download.list', $id) }}">Downlaod</a>
        <a class="btn btn-secondary" href="{{ route('add-to-campaign.list', $id) }}">Add to campaign</a>
        
    <div>

    <div class="row infoboxes mt-5">
        <div class="column">
            <a class="info-box" href="{{ route('show.leads.list', $id) }}">
                <div class="number">{{ number_format($totalLeads) }}</div>
                <div class="text">Total leads</div>
            </a>
        </div>
        <div class="column">
            <a class="info-box" href="{{ route('show.verified.list', $id) }}">
                <div class="number">{{ number_format($verified) }}</div>
                <div class="text">Verified</div>
            </a>
        </div>
        <div class="column">
            <a class="info-box" href="{{ route('show.fetched_content.list', $id) }}">
                <div class="number">{{ number_format($fetchedWebsiteContent) }}</div>
                <div class="text">Fetched Content</div>
            </a>
        </div>
        <div class="column">
            <a class="info-box" href="{{ route('show.personalized.list', $id) }}">
                <div class="number">{{ number_format($personalized) }}</div>
                <div class="text"> Personalized</div>
            </a>
        </div>
        <div class="column">
            <a class="info-box" href="{{ route('show.added_to_campaign.list', $id) }}">
                <div class="number">{{ number_format($addedToCampaign) }}</div>
                <div class="text">Added to campaign</div>
            </a>
        </div>
    </div>

    <div class="buttons mt-5">
        <a style="margin-left: auto;" class="btn btn-danger" href="{{ route('delete.list', $id) }}">Delete List</a>
    <div>
    


@endsection('content')
