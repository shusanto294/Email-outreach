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
        <a class="btn btn-secondary" href="{{ route('upload.list', $id) }}">Upload</a>
        <a class="btn btn-secondary" href="{{ route('download.list', $id) }}">Downlaod</a>
        <a class="btn btn-secondary" href="{{ route('verify.list', $id) }}">Verify</a>
        <a class="btn btn-secondary" href="{{ route('fetch.content', $id) }}">Fetch content</a>
        <a class="btn btn-secondary" href="{{ route('personalize.list', $id) }}">Personalize</a>
        <a class="btn btn-secondary" href="{{ route('add-to-campaign.list', $id) }}">Add to campaign</a>
    <div>

    <div class="row infoboxes mt-5">
        <div class="column">
            <div class="info-box">
                <div class="number">{{ number_format($totalLeads) }}</div>
                <div class="text">Total leads</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">

                <div class="number">{{ number_format($verified) }}</div>
                <div class="text">Verified</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">{{ number_format($fetchedWebsiteContent) }}</div>
                <div class="text">Fetched Content</div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">{{ number_format($personalized) }}</div>
                <div class="text">
                    Personalized
                </div>
            </div>
        </div>
        <div class="column">
            <div class="info-box">
                <div class="number">{{ number_format($addedToCampaign) }}</div>
                <div class="text">
                    Added to campaign
                </div>
                
            </div>
        </div>
    </div>

    



    

@endsection('content')
