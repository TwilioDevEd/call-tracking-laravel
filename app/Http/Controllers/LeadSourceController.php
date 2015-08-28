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

        $twilio->account->incoming_phone_numbers->create(
            ['PhoneNumber' => $request->input('phoneNumber')]
        );

        $leadSource = new LeadSource(['number' => $request->input('phoneNumber')]);
        $leadSource->save();

        return $request->input('phoneNumber');
    }

    /**
     * Show the form for editing a lead source
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the lead source from storage and release the number
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
