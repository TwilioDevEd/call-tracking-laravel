<?php

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

Route::get('lead/summary', 'LeadController@summary');
Route::resource(
    'lead', 'LeadController', ['only' => ['index', 'store']]
);