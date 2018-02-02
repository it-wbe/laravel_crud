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
    Route::get('admin/login/', 'Wbe\Crud\Controllers\User\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('admin/login/', 'Wbe\Crud\Controllers\User\Auth\LoginController@login');
    Route::get('admin/logout/', 'Wbe\Crud\Controllers\User\Auth\LoginController@logout');
// Registration Routes...
   // Route::get('admin/register/', 'Wbe\Crud\Controllers\User\Auth\RegisterController@showRegistrationForm');
   // Route::post('admin/register/', 'Wbe\Crud\Controllers\User\Auth\RegisterController@register');


    Route::get('admin/password/reset/{token}', 'Wbe\Crud\Controllers\User\Auth\ResetPasswordController@showResetForm')->name('password.token');
   // Reset password Form email
    Route::get('admin/password/reset', 'Wbe\Crud\Controllers\User\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
    Route::post('admin/password/email', 'Wbe\Crud\Controllers\User\Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('admin/password/reset', 'Wbe\Crud\Controllers\User\Auth\ResetPasswordController@reset');
});

Route::group(['middleware' => 'admin'], function () {

    Route::get('admin/', 'Wbe\Crud\Controllers\BackendHomeController@index')->middleware('admin')->name('admin.index');

    Route::get('admin/autocomplete/{model}/{fields}/{limit}/', 'Wbe\Crud\Controllers\Rapyd\AjaxController@getAutocomplete');

    //Route::post('admin/crud/delete/{content_id}', 'Wbe\Crud\Controllers\BackendHomeController@delete');
    Route::post('admin/crud/delete/', 'Wbe\Crud\Controllers\BackendHomeController@delete');

    Route::get('admin/account/', 'Wbe\Crud\Controllers\User\AccountController@index')->middleware('admin');
    Route::any('admin/account/settings', 'Wbe\Crud\Controllers\User\AccountController@settings')->middleware('admin');
    Route::post('admin/account/edit', 'Wbe\Crud\Controllers\User\AccountController@edit')->middleware('admin');

    //Route::group(['as' => 'admin', 'action' => 'MenuController@index'], function () {

//    Route::get('admin/hints', 'Wbe\Crud\Controllers\Hints\HintsFormerController@index');
//    Route::get('admin/odds', 'Wbe\Crud\Controllers\Odds\OddsFormerController@index');
    Route::get('admin/crud/grid/{content_type}', 'Wbe\Crud\Controllers\Rapyd\GridController@index')->name('crud grid');
    Route::any('admin/crud/edit/{content_type}/', 'Wbe\Crud\Controllers\Rapyd\EditController@index')->name('crud edit');
    Route::any('admin/crud/edit/{content_type}/lang/{lang_id}/', 'Wbe\Crud\Controllers\Rapyd\EditController@index');
//    Route::any('admin/type_content', 'Wbe\Crud\Controllers\TypeContentController@index');
    Route::any('admin/fields_descriptor/content/{content_type}', 'Wbe\Crud\Controllers\FieldsDescriptorController@content_types');
//    Route::any('admin/settings', 'Wbe\Crud\Controllers\SettingsController@index');
    //Route::any('admin/settings/generate', 'Wbe\Crud\Controllers\SettingsController@generate');
//    Route::any('admin/adminer', 'Wbe\Crud\Controllers\Adminer\AdminerAutologinController@index');
		/// EDIT LANGUAGES
	 Route::any('admin/lang_edit/{menu_item}/{file_name}','Wbe\Crud\Controllers\LangEditController@edit');

	
    Route::any('admin/filemanager', 'Wbe\Crud\Controllers\BackendHomeController@file_manager');

    //test
//    Route::any('admin/test', 'Wbe\Crud\Controllers\EditTestController@index');
    //});


    // MENU TREE
    Route::any('admin/additional/menu', 'Wbe\Crud\Controllers\MenuTreeController@index')->name('Menu Edit');
    Route::any('admin/additional/menu/edit', 'Wbe\Crud\Controllers\MenuTreeController@anyMenuedit')->name('menu.editNode');
    Route::post('admin/additional/menu/edit/AddCustomNode', 'Wbe\Crud\Controllers\MenuTreeController@addCustomNode')->name('menu.addCustomNode');
    Route::get('admin/additional/menu/edit/generate', 'Wbe\Crud\Controllers\MenuTreeController@tree_generate')->name('menu.generate');
    Route::post('admin/additional/menu/edit/anyMenueditPost', 'Wbe\Crud\Controllers\MenuTreeController@anyMenueditPost')->name('menu.editNodepost');


    ////// ROLES
    Route::get('admin/additional/roles', 'Wbe\Crud\Controllers\Roles\RolesController@roleIndex')->name('Role');
    Route::get('admin/additional/roles/edit/generate', 'Wbe\Crud\Controllers\Roles\RolesController@generatePermissions')->name('generatePermissions');
    Route::any('admin/additional/roles/edit', 'Wbe\Crud\Controllers\Roles\RolesController@roleEdit')->name('role.edit');
    Route::any('admin/additional/roles/edit/add', 'Wbe\Crud\Controllers\Roles\RolesController@addRole')->name('role.add');
    Route::post('admin/additional/roles/edit/del', 'Wbe\Crud\Controllers\Roles\RolesController@deleteRole')->name('role.del');

	// set default lang in admin panel
    if(!session('admin_lang_id')) {
		if(\Schema::hasTable('users')){			
			Session::put('admin_locale', Config::get('app.locale'));
			Session::put('admin_lang_id', \Wbe\Crud\Models\ContentTypes\Languages::where('code',"\"".Config::get('app.locale')."\"")->value('id'));
		}
	}
	
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

View::creator('crud::common.menu', 'Wbe\Crud\Controllers\MenuController@index');
View::creator('crud::common.vertical_menu', 'Wbe\Crud\Controllers\VerticalMenuController@index');
View::creator('crud::layout', 'Wbe\Crud\Controllers\BackendHomeController@language_select');

//View::creator('backend.layout', 'App\Http\Controllers\Wbe\Crud\Controllers\BackendHomeController@language_select');
