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
    'lead_source', 'LeadSourceController', ['except' => ['index', 'create', 'show']]
);
Route::get(
    'lead/summary-by-lead-source',
    ['as' => 'lead.summary_by_lead_source',
     'uses' => 'LeadController@summaryByLeadSource'
    ]
);
Route::get(
    'lead/summary-by-city',
    ['as' => 'lead.summary_by_city',
     'uses' => 'LeadController@summaryByCity'
    ]
);
Route::resource(
    'lead', 'LeadController', ['only' => ['index', 'store']]
);