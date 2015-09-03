<?php

Route::get(
    '/', function () {
        return redirect(route('dashboard'));
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
Route::get(
    'dashboard',
    ['as' => 'dashboard',
     'uses' => 'LeadController@dashboard'
    ]
);
Route::resource(
    'lead', 'LeadController', ['only' => ['store']]
);