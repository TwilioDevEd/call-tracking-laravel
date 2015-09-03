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
    public function dashboard(Request $request)
    {
        $context = [
            'leadSources' => LeadSource::all(),
            'availableNumbers' => $this->_availableNumbers($request),
            'appSid' => $this->_appSid()
        ];

        return response()->view('leads.index', $context);
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

        if (is_null($request->input('FromCity'))) {
            $lead->city = '';
        }
        $lead->city = $request->input('FromCity');

        if (is_null($request->input('FromState'))) {
            $lead->city = '';
        }
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
     * Display all lead sources as JSON, grouped by lead source
     *
     * @param  Request  $request
     * @return Response
     */
    public function summaryByLeadSource()
    {
        return response()->json(Lead::byLeadSource());
    }

    /**
     * Display all lead sources as JSON, grouped by city
     *
     * @param  Request  $request
     * @return Response
     */
    public function summaryByCity()
    {
        return response()->json(Lead::byCity());
    }

    /**
     * The Twilio TwiML App SID to use
     * @return string
     */
    private function _appSid()
    {
        $twilio = \App::make('Twilio');
        $appSid = config('app.twilio')['TWILIO_APP_SID'];

        if (isset($appSid)) {
            return $appSid;
        }

        $matchingAppsIter = $twilio
            ->account
            ->applications
            ->getIterator(0, 50, ['FriendlyName' => 'Call tracking app']);

        $matchingApps = iterator_to_array($matchingAppsIter);

        if (empty($matchingApps)) {
            return $twilio->account->applications->create(
                ['friendly_name' => 'Call tracking app']
            )->sid;
        } else {
            return $matchingApps[0]->sid;
        }
    }

    private function _availableNumbers(Request $request)
    {
        $twilio = \App::make('Twilio');

        $areaCode = $request->input('areaCode');

        $numbers = $twilio
            ->account
            ->available_phone_numbers
            ->getList('US', 'Local', ['AreaCode' => $areaCode])
            ->available_phone_numbers;

        return array_slice($numbers, 0, 5);
    }
}
