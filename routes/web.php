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
route::get('test', function(){
	$usuario = new App\User;
	$usuario->name = 'Admin';
	$usuario->email = 'admin@admin.com';
	$usuario->password = bcrypt('123123');
	$usuario->save();
	return App\User::all();
});
Route::get('/', function () {
    return view('layout');
})->middleware('auth');///Si el usuario no esta autenticado redirecciona al login
/////Rutas para Login///
Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout');
///Rutas para administador de usuarios//
route::resource('user', 'userController');

//ruas para tareas///
route::resource('tareas', 'tareasController');
