<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LeadSource;
use DB;

class Lead extends Model
{
    protected $fillable = ['caller_number', 'city', 'state', 'caller_name', 'call_sid'];

    public function leadSource()
    {
        return $this->belongsTo('App\LeadSource');
    }

    public static function byLeadSource()
    {
        $leadsBySource
            = DB::table('leads')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->select(
                DB::raw('count(1) as lead_count'),
                'lead_sources.description',
                'lead_sources.number'
            )
            ->groupBy(
                'lead_source_id',
                'lead_sources.description',
                'lead_sources.number'
            )
            ->orderBy('lead_count', 'DESC')
            ->orderBy('lead_sources.description', 'DESC')
            ->get();

        return $leadsBySource;
    }

    public static function byCity()
    {
        $leadsByCity
            = DB::table('leads')
            ->join('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->select(DB::raw('count(1) as lead_count'), 'leads.city')
            ->groupBy('leads.city')
            ->get();

        return $leadsByCity;
    }
}
