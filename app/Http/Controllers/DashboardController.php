<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Email;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        // $startDate = Carbon::now()->subDays(30)->toDateString();
        // $endDate = Carbon::now()->toDateString();

        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();

        $totalLeadsAdded = Lead::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalLeadsPersonalized = Lead::where('personalization', '!=', "")->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalLeadsNotPersonalized = Lead::where('website_content', "")->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $personalizationFailed = Lead::where('website_content', "n/a")->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalEmailSentCount = Email::where('sent', '!=' , null)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalEmailNotSentCount = Lead::where('sent', '=' , 0)->where('campaign_id', '!=' , 0)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalEmailsOpened = Email::where('opened', '!=', 0)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalReplyCount = Reply::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalNewReplyCount = Reply::where('seen', '0')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();


        $totalJobs = DB::table('jobs')->count();
        $totalFailedJobs = DB::table('failed_jobs')->count();
    

        return view('dashboard', [
            'totalLeadsAdded' => $totalLeadsAdded,
            'totalLeadsPersonalized' => $totalLeadsPersonalized,
            'totalLeadsNotPersonalized' => $totalLeadsNotPersonalized,
            'personalizationFailed' => $personalizationFailed,
            'totalEmailSentCount' => $totalEmailSentCount,
            'totalEmailNotSentCount' => $totalEmailNotSentCount,
            'totalEmailsOpened' => $totalEmailsOpened,
            'totalReplyCount' => $totalReplyCount,
            'totalNewReplyCount' => $totalNewReplyCount,
            'totalJobs' => $totalJobs,
            'totalFailedJobs' => $totalFailedJobs
            
        ]);
    }
}
