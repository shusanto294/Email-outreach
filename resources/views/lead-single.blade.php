@extends('theme')

@section('head')
    <style>
      a.icon-link {
          text-decoration: none;
          margin-right: 10px;
          margin-top: 10px;
          font-size: 20px;
      }
      a.icon-link.website{
          text-decoration: none;
          margin-right: 0px;
          font-size: 16px;
      }
    </style>
@endsection

@section('content')
    <h3>{{ $lead->name }}</h3>
    <a class="icon-link" href="{{ $lead->linkedin_profile }}" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
    <a class="icon-link website" href="{{ $lead->company_website }}" target="_black"><i class="fa-solid fa-up-right-from-square"></i></a>

    <p class="mt-3"><b>Title : </b>{{ $lead->title }}</p>
    <p><b>Company : </b>{{ $lead->company }}</p>
    <p><b>Location :</b> {{ $lead->location }}</p>
    <p><b>Email :</b> {{ $lead->email }}</p>

    <form action="{{ route('lead.update', $lead->id) }}" method="POST">
        @csrf
        <select name="campaignId" class="form-control mb-3">
            @php
                $campaigns = App\Models\Campaign::get();
            @endphp
            @foreach ($campaigns as $campaign)
                <option value="{{ $campaign->id }}" {{ $lead->campaign_id == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
            @endforeach
        </select>
        <select name="subscribe" class="form-control mb-3">
            <option value="1" {{ $lead->subscribe == 1 ? 'selected' : '' }}>Subscribe</option>
            <option value="0" {{ $lead->subscribe == 0 ? 'selected' : '' }}>Un Subscribe</option>
        </select>
        <textarea name="personalizedLine" class="form-control mb-3" placeholder="Personalized Line">{{ $lead->personalized_line }}</textarea>

        <button type="submit" class="btn btn-secondary">Update Lead</button>
    </form>
@endsection
