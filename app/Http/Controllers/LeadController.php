<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Leadlist;
use App\Imports\LeadsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function import(Request $request){
        
        $campaignID = $request->campaign_id;
        $file = $request->file('file');
        
        Excel::import(new LeadsImport($campaignID), $file);
        return redirect()->back()->with('success', 'yes');

    }

    public function index(){
        return view('leads', [
            'leads' => DB::table('leads')->paginate(10)
        ]);
    }

    public function show($id){
        $lead = Lead::find($id);
        return view('lead-single', [
            'lead' => $lead
        ]);
    }

    public function update(Request $request, $id){
        $lead = Lead::find($id);
        $lead->subscribe = $request->subscribe;
        $lead->personalized_line = $request->personalizedLine;
        $lead->save();
        return redirect()->back();
    }
}