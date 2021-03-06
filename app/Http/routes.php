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
	Route::any("loginsupervisor",		"SupervisoresController@login");
	Route::any("marcajes/vendedores",	"SupervisoresController@entradas_vendedores");
	Route::any("generar_password",		"VendedoresController@generar_password");
	Route::any("validar_password",		"VendedoresController@validar_password");
	Route::any("vendedor/ventas",		"AsistenciasController@reporte_ventas_vendedor");

	Route::get("reporte_ventas",		"AsistenciasController@reporte_ventas");
	Route::get("reporte_asistencias", 	"AsistenciasController@reporte_asistencias");
	Route::get("reporte_marcajes",		"AsistenciasController@reporte_marcajes");
	Route::get("exportar_ventas",		"AsistenciasController@exportar_ventas");
	Route::get("exportar_asistencias",	"AsistenciasController@exportar_asistencias");
	Route::get("exportar_marcajes",		"AsistenciasController@exportar_marcajes");

	Route::resource("usuarios",			"UsuariosController");
	Route::resource("vendedores",		"VendedoresController");
	Route::resource("sucursales",		"SucursalesController");
	Route::resource("supervisores",		"SupervisoresController");
	Route::resource("marcas",			"MarcasController");
});