<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telefonos extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'telefonos';
    protected $fillable = [
        'id_vendedor', 'numero'
    ];

}
