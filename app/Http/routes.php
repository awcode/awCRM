<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => 'auth'), function()
{
	Route::controller('customer', 'CustomerController');
	Route::controller('customercontact', 'CustomerContactController');
	Route::controller('customeraddress', 'CustomerAddressController');

	Route::controller('event', 'EventController');

	Route::controller('staff', 'StaffController');
	
	Route::controller('settings', 'SettingsController');
});

Route::controller('/', 'HomeController');

