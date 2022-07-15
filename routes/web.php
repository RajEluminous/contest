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

/*
|----------------------------------
| Error Handling
|----------------------------------
*/
// 404 Page Not Found
Route::fallback(function () {
    return redirect('/');
});

Route::resource('/', 'LeaderboardController');
Route::post('/ajaxupdateconteststatus', 'LeaderboardController@ajaxUpdateContestStatus')->name('leaderboard.ajaxupdateconteststatus');

Route::get('/temp', 'TempController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin'], function () {
    Auth::routes();

    Route::group(['namespace' => 'Admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        //Route::resource('affiliates', 'AffiliateController');
        Route::resource('roles', 'RoleController');
        Route::resource('users', 'UserController');
        Route::resource('permissions', 'PermissionController');
        Route::resource('clickbank_accounts', 'ClickbankAccountController');
        Route::resource('contests', 'ContestController');
        Route::resource('teams', 'TeamController');
        Route::resource('prizes', 'PrizeController');
        Route::post('prizes/addprize/{id?}', 'PrizeController@addPrize')->name('prizes.addprize');
        Route::post('prizes/deletepricedetail/{id}', 'PrizeController@deletePriceDetail')->name('prizes.deletepricedetail');
        Route::post('prizes/ajaxupdateorder', 'PrizeController@ajaxUpdateOrder')->name('prizes.ajaxupdateorder');

        // for CB Masterlist
        Route::get('/affiliates', 'AffiliateController@index')->name('affiliates.index');
        Route::post('/affiliates', 'AffiliateController@index')->name('affiliates.index');
        Route::get('/affiliates/create_affiliate', 'AffiliateController@create_affiliate')->name('affiliates.create_affiliate');
        Route::post('/affiliates/create_affiliate', 'AffiliateController@create_affiliate')->name('affiliates.create_affiliate');
        Route::get('/affiliates/create_partner', 'AffiliateController@create_partner')->name('affiliates.create_partner');
        Route::post('/affiliates/create_partner', 'AffiliateController@create_partner')->name('affiliates.create_partner');
        Route::get('/affiliates/block_affiliate', 'AffiliateController@block_affiliate')->name('affiliates.block_affiliate');
        Route::post('/affiliates/block_affiliate', 'AffiliateController@block_affiliate')->name('affiliates.block_affiliate');
        Route::get('/affiliates/unblock_affiliate/{id}', 'AffiliateController@unblock_affiliate')->name('affiliates.unblock_affiliate');
        // Route::post('/affiliate', 'AffiliateController@store');
        Route::get('admin/affiliates/delete/{id}', 'AffiliateController@destroy')->name('affiliates.destroy');
        Route::get('admin/affiliates/deleteaaffiliate/{id}', 'AffiliateController@deleteaaffiliate')->name('affiliates.deleteaaffiliate');
        Route::get('admin/affiliates/deleteapartner/{id}', 'AffiliateController@deleteapartner')->name('affiliates.deleteapartner');
        Route::post('admin/affiliates/save-image', 'AffiliateController@saveimage')->name('affiliates.saveimage');
        Route::post('admin/contests/savecbprods/{id}', 'ContestController@savecbprods')->name('contests.savecbprods');
        Route::post('admin/contests/savecbcontest/{id}', 'ContestController@saveClickbankContestType')->name('contests.savecbcontest');
        Route::get('contests/edit/{id}/{tid?}', 'ContestController@edit')->name('contests.edit');
        Route::get('contests/view/{id}', 'ContestController@view')->name('contests.view');
        Route::get('admin/contests/updateviewstatus', 'ContestController@updateViewStatus')->name('contests.updateviewstatus');
        Route::post('admin/contests/deletecbprods/{id}', 'ContestController@deletecbprods')->name('contests.deletecbprods');
        Route::post('admin/contests/updatedisplaystatus', 'ContestController@updateDisplayStatus')->name('contests.updatedisplaystatus');
    });
});
