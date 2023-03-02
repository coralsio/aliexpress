<?php

Route::group(['prefix' => 'aliexpress'], function () {
    Route::resource('imports', 'ImportsController');
    Route::get('process-imports', 'ImportsController@processImports');

    Route::get('settings', 'AliexpressController@settings');
    Route::post('settings/{store?}', 'AliexpressController@saveSettings');
    Route::post('load-categories/{store?}', 'AliexpressController@loadCategories');
});

