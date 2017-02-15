<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asistencias extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'asistencias';
    protected $fillable = [
        'id_vendedor', 'tipo', 'entrada', 'latitud', 'longitud'
    ];

}
