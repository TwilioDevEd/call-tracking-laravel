<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class AvailableNumberController extends Controller
{

    /**
     * Twilio Client
     */
    protected $_twilioClient;

    public function __construct(Client $twilioClient)
    {
        $this->_twilioClient = $twilioClient;
    }
    
    /**
     * Display numbers available for purchase. Fetched from the API
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $areaCode = $request->input('areaCode');

        $numbers = $this->_twilioClient->availablePhoneNumbers->getContext("US")
            ->local->stream(
            [
                'areaCode' => $areaCode
            ]
        );

        return response()->view(
            'available_numbers.index',
            [
                'numbers' => $numbers,
                'areaCode' => $areaCode
            ]
        );
    }
}
