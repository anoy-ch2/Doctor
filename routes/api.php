<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Patient Lists
    Route::post('patient-lists/media', 'PatientListApiController@storeMedia')->name('patient-lists.storeMedia');
    Route::apiResource('patient-lists', 'PatientListApiController');

    // Prescriptions
    Route::post('prescriptions/media', 'PrescriptionApiController@storeMedia')->name('prescriptions.storeMedia');
    Route::apiResource('prescriptions', 'PrescriptionApiController');
});
