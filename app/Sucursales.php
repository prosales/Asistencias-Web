<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sucursales';
    protected $fillable = [
        'codigo', 'cadena', 'tienda', 'direccion', 'departamento', 'municipio', 'region', 'supervisor', 'hora_entrada', 'hora_almuerzo', 'hora_salida', 'latitud', 'longitud'
    ];

}
