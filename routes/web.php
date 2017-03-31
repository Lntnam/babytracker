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

Route::post('ajax/save_measurements', 'AjaxController@saveMeasurements')->name('Ajax.SaveMeasurements');

Route::post('ajax/add_meal', 'AjaxController@addMeal')->name('Ajax.AddMeal');

Route::post('ajax/toggle_sleep', 'AjaxController@toggleSleep')->name('Ajax.ToggleSleep');

Route::post('ajax/cancel_sleep', 'AjaxController@cancelSleep')->name('Ajax.CancelSleep');

Route::get('ajax/load_sleep_status_view', 'AjaxController@loadSleepStatusView')->name('Ajax.LoadSleepStatusView');
Route::get('ajax/load_awh_view', 'AjaxController@loadAgeWeightHeightView')->name('Ajax.LoadAgeWeightHeightView');
Route::get('ajax/load_today_meals_view', 'AjaxController@loadTodayMealsView')->name('Ajax.LoadTodayMealsView');
Route::get('ajax/load_meal_snapshot_view', 'AjaxController@loadMealSnapshotView')->name('Ajax.LoadMealSnapshotView');
Route::get('ajax/load_today_sleeps_view', 'AjaxController@loadTodaySleepsView')->name('Ajax.LoadTodaySleepsView');
Route::get('ajax/load_sleep_snapshot_view', 'AjaxController@loadSleepSnapshotView')->name('Ajax.LoadSleepSnapshotView');
Route::get('ajax/load_notifications', 'AjaxController@loadNotifications')->name('Ajax.LoadNotifications');

Route::get('/meal', 'MealReportController@index')->name('MealReport');

Route::get('/weight', 'WeightReportController@index')->name('WeightReport');

Route::get('/sleep', 'SleepReportController@index')->name('SleepReport');

Route::get('/config', function() {
    return view('config');
});
