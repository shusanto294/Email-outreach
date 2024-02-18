<?php

namespace App\Http\Controllers;

use App\Models\Apikey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreApikeyRequest;
use App\Http\Requests\UpdateApikeyRequest;

class ApikeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('openai', [
            'apikeys' => DB::table('apikeys')->orderBy('id', 'desc')->paginate(50)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreApikeyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function add_new_key(Request $request)
    {
        Apikey::create(array(
            'key' => $request->apikey
        ));
        return redirect()->back()->with('success', 'Api key added successfully. ');
    }

    public function delete($id)
    {
        $apiKey = Apikey::find($id);
        
        if (!$apiKey) {
            return redirect()->back()->with('error', 'Api key not found!');
        }
    
        $apiKey->delete();
    
        return redirect()->back()->with('warning', 'Api key deleted successfully!');
    }
    
}
