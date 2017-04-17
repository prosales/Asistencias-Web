<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supervisores extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'supervisores';
    protected $fillable = [
        'nombre', 'telefono', 'usuario', 'password', 'estado'
    ];

    protected $hidden = [
        'password'
    ];

}
