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

        $totalJobs = DB::table('jobs')->count();
        $totalFailedJobs = DB::table('failed_jobs')->count();
    
        return view('dashboard', [
            'totalJobs' => $totalJobs,
            'totalFailedJobs' => $totalFailedJobs
        ]);
    }

    public function downloadLogFiles(){
        //Show content of the laravel.log file
        $log = file_get_contents(storage_path('logs/laravel.log'));
        return response($log)->header('Content-Type', 'text/plain');
    }
}


