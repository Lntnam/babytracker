<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('dashboard');

Route::get('/close', 'HomeController@close')->name('CloseDay');

Route::post('ajax/close_notification', 'AjaxController@closeNotification')->name('Ajax.CloseNotification');

Route::post('ajax/save_weight', 'AjaxController@saveWeight')->name('Ajax.SaveWeight');

Route::post('ajax/add_meal', 'AjaxController@addMeal')->name('Ajax.AddMeal');

Route::post('ajax/toggle_sleep', 'AjaxController@toggleSleep')->name('Ajax.ToggleSleep');

Route::post('ajax/cancel_sleep', 'AjaxController@cancelSleep')->name('Ajax.CancelSleep');

Route::get('/meal', 'MealReportController@index')->name('MealReport');

Route::get('/weight', 'WeightReportController@index')->name('WeightReport');

Route::get('/sleep', 'SleepReportController@index')->name('SleepReport');

Route::get('/config', function() {
    return view('config');
});
