<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function()
{
    return Redirect::to("src");
});

//Route::get("view_asistencias", function(){ return view("reportes.asistencias"); });

Route::group(["prefix"=>"ws"], function()
{
	Route::post("telefonos",			"VendedoresController@agregar_telefono");
	Route::get("telefonos/{id}",		"VendedoresController@eliminar_telefono");
	Route::any("asistencias/crear",		"AsistenciasController@crear_asistencia");
	Route::any("mensajes/crear",		"AsistenciasController@crear_mensaje");
	Route::any("asistencias/lista",		"AsistenciasController@asistencias");
	Route::any("mensajes/lista",		"AsistenciasController@mensajes");
	Route::any("login",					"UsuariosController@login");
	Route::any("loginmovil",			"VendedoresController@login");

	Route::get("reporte_ventas",		"AsistenciasController@reporte_ventas");
	Route::get("reporte_asistencias", 	"AsistenciasController@reporte_asistencias");
	Route::get("exportar_ventas",		"AsistenciasController@exportar_ventas");
	Route::get("exportar_asistencias",	"AsistenciasController@exportar_asistencias");

	Route::resource("usuarios",			"UsuariosController");
	Route::resource("vendedores",		"VendedoresController");
	Route::resource("sucursales",		"SucursalesController");
});