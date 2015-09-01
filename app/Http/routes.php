<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get(
    '/', function () {
        return redirect(route('available_number.index'));
    }
);

Route::resource(
    'available_number', 'AvailableNumberController', ['only' => ['index']]
);

Route::resource(
    'lead_source', 'LeadSourceController', ['except' => ['create', 'show']]
);

Route::resource(
    'lead', 'LeadController', ['only' => ['index', 'store']]
);