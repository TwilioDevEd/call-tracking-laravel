<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\LeadSource;

class LeadSourceController extends Controller
{
    /**
     * Show all lead sources
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $leadSources = LeadSource::all();
        return response()->view(
            'lead_sources.index',
            ['leadSources' => $leadSources]
        );
    }

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

        $twilio->account->incoming_phone_numbers->create(
            ['PhoneNumber' => $request->input('phoneNumber')]
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

        return redirect()->route('lead_source.index');
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
}
