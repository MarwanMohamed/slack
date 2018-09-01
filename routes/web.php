<?php

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix'=>'slack'],function()
{
	Route::get('redirect','SlackController@redirect');
	Route::get('callback','SlackController@callback');
	
});


Auth::routes();


Route::get('/home', 'ChannelsController@index')->name('home')->middleware('auth:web');

Route::group(['prefix'=>'dashboard','middleware'=>'auth:web'],function()
{
	Route::group(['prefix'=>'slack'],function()
	{
		Route::post('create_channel','ChannelsController@create')->name('create.channel');

	});
});
