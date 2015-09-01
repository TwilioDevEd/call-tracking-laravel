<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LeadSource;

class Lead extends Model
{
    protected $fillable = ['caller_number', 'city', 'state', 'caller_name', 'call_sid'];

    public function leadSource()
    {
        return $this->belongsTo('App\LeadSource');
    }
}
