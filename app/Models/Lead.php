<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'linkedin_profile',
        'title',
        'company',
        'company_website',
        'location',
        'email',
        'campaign_id',
        'unsubscribe'
    ];
}
