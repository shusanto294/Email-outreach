<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Setting;
use App\Http\Requests\StoreClickRequest;
use App\Http\Requests\UpdateClickRequest;

class ClickController extends Controller
{

    public function calenderLink($campaignID, $leadID)
    {
        $calenderLinkSetting = Setting::where('key', 'calender_link')->first();

        if ($calenderLinkSetting) {
            $click = Click::where('campaign_id', $campaignID)->where('lead_id', $leadID)->first();
        
            if ($click) {
                // Update the existing click's timestamp
                $click->touch();
            } else {
                // Create a new Click entry
                Click::create([
                    'campaign_id' => $campaignID,
                    'lead_id' => $leadID,
                ]);
            }
        
            $calenderLink = $calenderLinkSetting->value;
        
            // Redirect user to the calendar link
            return redirect()->away($calenderLink);
        }
        
        // Handle case where calendar link is not set
        return redirect()->back()->with('error', 'Calendar link not found.');
        
    }
}
