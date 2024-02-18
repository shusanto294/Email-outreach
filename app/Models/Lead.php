<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function list(){
        return $this->hasOne(LeadList::class, 'id', 'leadlist_id');
    }
}
