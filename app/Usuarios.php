<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuarios extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'usuarios';
    protected $fillable = [
        'nombre', 'usuario', 'password', 'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
