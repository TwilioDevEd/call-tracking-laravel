<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Lead;
use App\LeadSource;

class LeadController extends Controller
{
    /**
     * Display a listing of leads
     *
     * @return Response
     */
    public function index()
    {
        return response()->view('leads.index');
    }

    /**
     * Store a new lead with its lead source and forward the call
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $leadSource = LeadSource::where(['number' => $request->input('To')])->first();
        $lead = new Lead();
        $lead->leadSource()->associate($leadSource->id);

        $lead->city = $request->input('FromCity');
        $lead->state = $request->input('FromState');
        $lead->caller_number = $request->input('From');
        $lead->caller_name = $request->input('CallerName');
        $lead->call_sid = $request->input('CallSid');

        $lead->save();

        $forwardMessage = new \Services_Twilio_Twiml();
        $forwardMessage->dial($leadSource->forwarding_number);

        return response($forwardMessage, 201)->header('Content-Type', 'application/xml');
    }
    /**
     * Store a new lead with its lead source and forward the call
     *
     * @param  Request  $request
     * @return Response
     */
    public function summary(Request $request)
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
            ->get();

        return response()->json($leadsBySource);
    }
}
