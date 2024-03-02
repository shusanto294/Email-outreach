<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
use App\Models\Reply;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){
        // $startDate = Carbon::now()->subDays(30)->toDateString();
        // $endDate = Carbon::now()->toDateString();

        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();

        $totalLeadsAdded = Lead::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalLeadsPersonalized = Lead::where('personalized_line', '!=', "")->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalEmailSentCount = Email::where('sent', '!=' , null)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalEmailsOpened = Email::where('opened', '!=', 0)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalReplyCount = Reply::whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();
        $totalNewReplyCount = Reply::where('seen', '0')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->count();

        return view('dashboard', [
            'totalLeadsAdded' => $totalLeadsAdded,
            'totalLeadsPersonalized' => $totalLeadsPersonalized,
            'totalEmailSentCount' => $totalEmailSentCount,
            'totalEmailsOpened' => $totalEmailsOpened,
            'totalReplyCount' => $totalReplyCount,
            'totalNewReplyCount' => $totalNewReplyCount,
        ]);
    }
}
