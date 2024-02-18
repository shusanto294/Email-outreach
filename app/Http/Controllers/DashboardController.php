<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Reply;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $totalEmailNotSentCount = Lead::where('sent', 0)->where('campaign_id', '!=' , 0)->count();
        $totalEmailSentCount = Email::where('sent', '!=' , null)->count();
        $totalEmailsOpened = Email::where('opened', '!=', 0)->count();
        $totalReplyCount = Reply::count();
        $totalNewReplyCount = Reply::where('seen', '0')->count();

        return view('dashboard', [
            'totalEmailNotSentCount' => $totalEmailNotSentCount,
            'totalEmailSentCount' => $totalEmailSentCount,
            'totalEmailsOpened' => $totalEmailsOpened,
            'totalReplyCount' => $totalReplyCount,
            'totalNewReplyCount' => $totalNewReplyCount,
        ]);
    }
}
