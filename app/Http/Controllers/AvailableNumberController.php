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

        $numbers = $twilio->account->available_phone_numbers->getList('US', 'Local');
        error_log(var_dump($numbers));

        return response()->view('available_numbers.index');
    }

    /**
     * Buy a number and store it locally
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

}
