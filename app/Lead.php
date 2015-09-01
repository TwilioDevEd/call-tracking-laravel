<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['caller_number', 'city', 'state', 'caller_name', 'call_sid'];

    public function source()
    {
        return $this->belongsTo('LeadSource');
    }
}
