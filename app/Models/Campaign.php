<?php

namespace App\Models;

use App\Models\Lead;
use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject',
        'body'
    ];


    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'campaign_id', 'id');
    }
    
}
