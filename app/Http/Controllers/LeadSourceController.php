<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\LeadSource;

class LeadSourceController extends Controller
{
    /**
     * Store a new lead source (i.e phone number) and redirect to edit
     * page
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $twilio = \App::make('Twilio');
        $appSid = $this->_appSid();

        $twilio->account->incoming_phone_numbers->create(
            ['PhoneNumber' => $request->input('phoneNumber'),
             'VoiceCallerIdLookup' => true,
             'VoiceApplicationSid' => $appSid]
        );

        $leadSource = new LeadSource(['number' => $request->input('phoneNumber')]);
        $leadSource->save();

        return redirect()->route('lead_source.edit', [$leadSource]);
    }

    /**
     * Show the form for editing a lead source
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $leadSourceToEdit = LeadSource::find($id);

        return response()->view(
            'lead_sources.edit',
            ['leadSource' => $leadSourceToEdit]
        );
    }

    /**
     * Update the lead source in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $leadSourceToUpdate = LeadSource::find($id);
        $leadSourceToUpdate->fill($request->all());
        $leadSourceToUpdate->save();

        return redirect()->route('lead.index');
    }

    /**
     * Remove the lead source from storage and release the number
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $twilio = \App::make('Twilio');
        $leadSourceToDelete = LeadSource::find($id);
        $number = $twilio
            ->account
            ->incoming_phone_numbers
            ->getNumber($leadSourceToDelete->number);

        $twilio->account->incoming_phone_numbers->delete($number->sid);
        $leadSourceToDelete->delete();

        return redirect()->route('lead_source.index');
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
}
