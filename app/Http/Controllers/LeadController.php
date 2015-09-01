<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        //
    }

    /**
     * Store a new lead with its lead source and forward the call
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $leadSource = LeadSource::where(['number' => $request->input('To')]);
        $lead = new Lead();
        $lead->source()->associate($leadSource);

        $lead->city = $request->input('FromCity');
        $lead->state = $request->input('FromState');
        $lead->caller_number = $request->input('From');
        $lead->caller_name = $request->input('CallerName');
        $lead->call_sid = $request->input('CallSid');

        $lead->save();

        $forwardMessage = Services_Twilio_Twiml();
        $forwardMessage->dial($leadSource->forwarding_number);

        return response($forwardMessage, 201)->header('Content-Type', 'application/xml');
    }
}
