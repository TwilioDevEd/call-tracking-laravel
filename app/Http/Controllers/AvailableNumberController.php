<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AvailableNumberController extends Controller
{
    /**
     * Display numbers available for purchase. Fetched from the API
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $twilio = \App::make('Twilio');

        $areaCode = $request->input('areaCode');

        $numbers = $twilio
            ->account
            ->available_phone_numbers
            ->getList('US', 'Local', ['AreaCode' => $areaCode])
            ->available_phone_numbers;

        return response()->json(
            'available_numbers.index',
            ['numbers' => $numbers,
             'areaCode' => $areaCode]
        );
    }
}
