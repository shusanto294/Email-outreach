<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToModel, WithHeadingRow

{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $campaignID;

    public function __construct($campaignID)
    {
         $this->campaignID = $campaignID;
    }

    public function model(array $row)
    {
        $lead = Lead::where('email', $row['email'])->where('campaign_id', '=', $this->campaignID)->first();

        if ($lead === null) {
            return new Lead([
                'name'     => $row['name'],
                'linkedin_profile' => $row['linkedin_profile'],
                'title' => $row['title'],
                'company' => $row['company'],
                'company_website' => $row['company_website'],
                'location' => $row['location'],
                'email' => $row['email'],
                'campaign_id' => $this->campaignID
            ]);
        }else{
            return null;
        }

    }

}
