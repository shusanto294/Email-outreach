<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;

class SettingController extends Controller
{
    public function updateSendEmailsSetting(Request $request){
        $setting = Setting::where('key', 'send_emails')->first();
        $sendEmails =  $request->send_emails;

        if(!$sendEmails){
            $sendEmails = 'off';
        }

        if(!$setting){
            Setting::create(array(
                'key' => 'send_emails',
                'value' => $sendEmails
            ));
        }else{
            $setting->value = $sendEmails;
            $setting->save();
        }

        return redirect()->back();
    }

}
