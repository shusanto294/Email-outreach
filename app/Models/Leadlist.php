<?php

namespace App\Models;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leadlist extends Model
{
    use HasFactory;
    protected $guarded = [];

    // public function leads(): HasMany
    // {
    //     return $this->hasMany(Lead::class, 'leadlist_id', 'id');
    // }
}
