<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Email;
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
        
        $listID = $request->list_id;
        $file = $request->file('file');
        
        Excel::import(new LeadsImport($listID), $file);
        return redirect()->back()->with('success', 'Leads imported successfully !');

    }

    public function index(){
        return view('leads', [
            'leads' => DB::table('leads')->orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function show($id){
        // Find the lead by its ID
        $lead = Lead::find($id);
    
        if (!$lead) {
            abort(404, 'Lead not found');
        }
    
        // Retrieve emails related to the lead's campaign and lead ID
        $emails = Email::where('lead_id', $lead->id)
                       ->latest()
                       ->get();
    
        return view('lead-single', [
            'lead' => $lead,
            'emails' => $emails
        ]);
    }

    public function update(Request $request, $id){
        $lead = Lead::find($id);

        $fullName = $lead->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0] ? $nameParts[0] : '';

        $lead->subscribe = $request->subscribe;
        $lead->leadlist_id = $request->leadListID;
        #$lead->personalized_line = $request->personalizedLine;
        $lead->personalized_line = str_replace(["[firstname]", "[company]", "[website]"], [$firstName, $lead->company, $lead->company_website], $request->personalizedLine);


        $lead->save();
        return redirect()->back();
    }

    public function search(Request $request) {
        $leads = Lead::where(function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->searchText . '%')
            ->orWhere('linkedin_profile', 'like', '%' . $request->searchText . '%')
            ->orWhere('title', 'like', '%' . $request->searchText . '%')
            ->orWhere('company', 'like', '%' . $request->searchText . '%')
            ->orWhere('company', 'like', '%' . $request->searchText . '%')
            ->orWhere('company_website', 'like', '%' . $request->searchText . '%')
            ->orWhere('location', 'like', '%' . $request->searchText . '%')
            ->orWhere('email', 'like', '%' . $request->searchText . '%')
            ->orWhere('technology', 'like', '%' . $request->searchText . '%');
        })->orderBy('created_at', 'desc')->paginate(10);
    
        return view('leads', [
            'leads' => $leads
        ]);
    }

    public function verify_lead(){
        $lead = Lead::where('verified', null)->first();

        if(!$lead){
            echo 'No lead found';
            return;
        }

        $email = $lead->email;
        $domain = substr($email, strpos($email, '@') + 1);
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $lead->verified = 'false';
            $lead->save();
            echo $email. 'Email format is not valid <br>';
            return;
        }

        if(!checkdnsrr($domain)) {
            $lead->verified = 'false';
            $lead->save();
            echo $email . ' - Could not be verified *****';
            return;
       }

       echo $email . ' - Verified';
       $lead->verified = 'true';
       $lead->save();
       
    }
}