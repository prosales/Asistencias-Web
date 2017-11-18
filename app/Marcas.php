<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marcas extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'marcas';
    protected $fillable = [
        'nombre', 'es_vpr'
    ];
}
