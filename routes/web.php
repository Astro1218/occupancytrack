<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/report/community_view_chronjob', 'ReportController@community_view_chronjob')->name('community_view_chronjob');
Route::get('/report/chronjob', 'ReportController@chronjob')->name('chronjob');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/' , 'HomeController@index')->name('home');

    Route::post('/getchartinfodata', 'ReportController@getchartinfodata');
    Route::post('/getdatatableinfodata', 'ReportController@getdatatableinfodata');

    Route::post('/report/community_view', 'ReportController@community_view');
    Route::get('/report/community_view_goto', 'ReportController@community_view_goto')->name('community_view_goto');

    Route::get('/report/community_view_add', 'ReportController@community_view_add')->name('community_view_add');
    Route::get('/report/community_view_edit', 'ReportController@community_view_edit')->name('community_view_edit');

    Route::post('/report/add_report', 'ReportController@add_report')->name('add_report');
    Route::post('/report/update_report', 'ReportController@update_report')->name('update_report');

    Route::post('/report/community_trend', 'ReportController@community_trend');
    Route::get('/report/community_trend_goto', 'ReportController@community_trend_goto')->name('community_trend_goto');

    Route::post('/report/company_view', 'ReportController@company_view');
    Route::get('/report/company_view_goto', 'ReportController@company_view_goto')->name('company_view_goto');

    Route::post('/report/company_trend', 'ReportController@company_trend');
    Route::get('/report/company_trend_goto', 'ReportController@company_trend_goto')->name('company_trend_goto');

    //corunus start
    Route::get('/usermanage', 'HomeController@usermanage')->name('usermanage');
    Route::post('/usermanage', 'HomeController@usermanage');
    Route::get('/reportmanage', 'HomeController@reportmanage');
    Route::post('/reportmanage', 'HomeController@reportmanage');

    Route::get('/profile', 'ProfileController@profile');
    Route::post('/profile', 'ProfileController@profile');
    // Route::post('/editaction', 'ReportController@editaction');
    // Route::get('/editaction', 'ReportController@editaction');
    // Route::post('/removeinquries', 'ReportController@removeinquries');
    // Route::post('/savedata', 'ReportController@savedata');
    // Route::post('/removecc', 'ReportController@removecc');

    Route::post('/signup' , 'UserController@signup')->middleware('CheckPassword');

    Route::post('/update' , 'UserController@update');
    Route::post('/changepass' , 'UserController@changepass');
    Route::post('/updatePassword' , 'UserController@updatePass');
    Route::post('/changeStatus' , 'UserController@changeStatus');

    // Route::post('/reportSummary', 'ReportController@getinfo');
    // Route::post('/reportSummarySecond', 'ReportController@getinfoSecond');
    //corunus end

    // Create file upload form
    Route::get('/upload-file', 'FileUploadController@createForm');
    Route::post('/upload-file', 'FileUploadController@fileUpload')->name('fileUpload');
});
