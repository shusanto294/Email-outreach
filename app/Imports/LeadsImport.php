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

    protected $listID;

    public function __construct($listID)
    {
         $this->listID = $listID;
    }

    public function model(array $row)
    {
        $lead = Lead::where('email', $row['email'])->first();

        if ($lead === null) {
            return new Lead([
                'name'     => $row['name'],
                'linkedin_profile' => $row['linkedin_profile'],
                'title' => $row['title'],
                'company' => $row['company'],
                'company_website' => $row['company_website'],
                'location' => $row['location'],
                'email' => $row['email'],
                'leadlist_id' => $this->listID
            ]);
        }else{
            return null;
        }

    }

}
