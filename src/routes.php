<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// admin

// Authentication Routes...
Route::group(['middleware' => 'web'], function () {
    Route::get('admin/login/', 'Wbe\Crud\Controllers\User\Auth\LoginController@showLoginForm');
    Route::post('admin/login/', 'Wbe\Crud\Controllers\User\Auth\LoginController@login');
    Route::get('admin/logout/', 'Wbe\Crud\Controllers\User\Auth\LoginController@logout');
// Registration Routes...
    Route::get('admin/register/', 'Wbe\Crud\Controllers\User\Auth\RegisterController@showRegistrationForm');
    Route::post('admin/register/', 'Wbe\Crud\Controllers\User\Auth\RegisterController@register');
});

Route::group(['middleware' => 'admin'], function () {

    Route::get('admin/', 'Wbe\Crud\Controllers\BackendHomeController@index');

    Route::get('admin/autocomplete/{model}/{fields}/{limit}/', 'Wbe\Crud\Controllers\Rapyd\AjaxController@getAutocomplete');

    //Route::post('admin/crud/delete/{content_id}', 'Wbe\Crud\Controllers\BackendHomeController@delete');
    Route::post('admin/crud/delete/', 'Wbe\Crud\Controllers\BackendHomeController@delete');

    Route::get('admin/account/', 'Wbe\Crud\Controllers\User\AccountController@index')->middleware('admin');
    Route::post('admin/account/edit/', 'Wbe\Crud\Controllers\User\AccountController@edit')->middleware('admin');

    //Route::group(['as' => 'admin', 'action' => 'MenuController@index'], function () {

    Route::get('admin/hints', 'Wbe\Crud\Controllers\Hints\HintsFormerController@index');
    Route::get('admin/odds', 'Wbe\Crud\Controllers\Odds\OddsFormerController@index');
    Route::get('admin/crud/grid/{content_type}', 'Wbe\Crud\Controllers\Rapyd\GridController@index');
    Route::any('admin/crud/edit/{content_type}/', 'Wbe\Crud\Controllers\Rapyd\EditController@index');
    Route::any('admin/crud/edit/{content_type}/lang/{lang_id}/', 'Wbe\Crud\Controllers\Rapyd\EditController@index');
    Route::any('admin/type_content', 'Wbe\Crud\Controllers\TypeContentController@index');
    Route::any('admin/fields_descriptor', 'Wbe\Crud\Controllers\FieldsDescriptorController@index');
    Route::any('admin/fields_descriptor/content/{content_type}', 'Wbe\Crud\Controllers\FieldsDescriptorController@content_types');
    Route::any('admin/settings', 'Wbe\Crud\Controllers\SettingsController@index');
    //Route::any('admin/settings/generate', 'Wbe\Crud\Controllers\SettingsController@generate');
    Route::any('admin/adminer', 'Wbe\Crud\Controllers\Adminer\AdminerAutologinController@index');

    Route::any('admin/filemanager', 'Wbe\Crud\Controllers\BackendHomeController@file_manager');

    //test
    Route::any('admin/test', 'Wbe\Crud\Controllers\EditTestController@index');
    //});


});

/*Route::group(['prefix' => 'admin'], function () {
    Route::any('type_content', 'Wbe\Crud\Controllers\TypeContentController@index');
});*/

//lang

Route::get('admin/setlocale/{locale}', function ($locale) {
    if (in_array($locale, \Wbe\Crud\Models\ContentTypes\Languages::pluck('code')->toArray())) {
        Session::put('admin_locale', $locale);
        Session::put('admin_lang_id', \Wbe\Crud\Models\ContentTypes\Languages::where('code', $locale)->value('id'));
    }
    return redirect()->back();
});
/* 11.1.17
 * if(!session('admin_lang_id')) {
    Session::put('admin_lang_id', \Wbe\Crud\Models\ContentTypes\Languages::where('code', Config::get('app.locale'))->value('id'));
}*/

/*Route::group(['domain' => '{lang}.bethintua'], function()
{
    Route::get('/', function($lang)
    {
        App::setLocale($lang);
    });
});*/


View::creator('crud::common.menu', 'Wbe\Crud\Controllers\MenuController@index');
View::creator('crud::layout', 'Wbe\Crud\Controllers\BackendHomeController@language_select');

//View::creator('backend.layout', 'App\Http\Controllers\Wbe\Crud\Controllers\BackendHomeController@language_select');


