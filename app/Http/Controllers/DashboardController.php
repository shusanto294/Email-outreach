<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Click;
use App\Models\Email;
use App\Models\Reply;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        // $startDate = Carbon::now()->subDays(30)->toDateString();
        // $endDate = Carbon::now()->toDateString();

        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();

        $totalJobs = DB::table('jobs')->count();
        $totalFailedJobs = DB::table('failed_jobs')->count();
        $failedJobs = DB::table('failed_jobs')->orderBy('id', 'desc')->get();

        $leadsAdded = Lead::where('created_at', '>=', now()->subDays(30))->count();
        $emailsSent = Email::where('created_at', '>=', now()->subDays(30))->count();
        $clicked = Click::where('created_at', '>=', now()->subDays(30))->count();
        $replied = Reply::where('created_at', '>=', now()->subDays(30))->count();

    
        return view('dashboard', [
            'totalJobs' => $totalJobs,
            'totalFailedJobs' => $totalFailedJobs,
            'leadsAdded' => $leadsAdded,
            'emailsSent' => $emailsSent,
            'clicked' => $clicked,
            'replied' => $replied
        ]);
    }

    public function downloadLogFiles(){
        //Show content of the laravel.log file
        $log = file_get_contents(storage_path('logs/laravel.log'));
        return response($log)->header('Content-Type', 'text/plain');
    }

    public function deleteQueueJobs(){
        DB::table('jobs')->delete();
        return redirect()->back()->with('success', 'All Queue jobs deleted successfully');
    }

    public function deleteFailedJobs(){
        DB::table('failed_jobs')->delete();
        return redirect()->back()->with('success', 'All Failed jobs deleted successfully');
    }

    public function get_queue_jobs()
    {
        // Query the jobs from the database and paginate with 10 per page
        $jobs = DB::table('jobs')->paginate(10);
    
        // Return the view with the paginated jobs data
        return view('jobs', [
            'jobs' => $jobs
        ]);
    }

    public function get_failed_jobs()
    {
        // Query the failed jobs from the database and paginate with 10 per page
        $failedJobs = DB::table('failed_jobs')->paginate(10);
    
        // Return the view with the paginated failed jobs data
        return view('failed-jobs', [
            'jobs' => $failedJobs
        ]);
    }
    

}



