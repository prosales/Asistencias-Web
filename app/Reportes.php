<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reportes extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'reportes';
    protected $fillable = [
        'id_vendedor', 'numero', 'monto', 'incidencia', 'foto'
    ];

    public function vendedor()
    {
    	return $this->hasOne("App\ViewVendedores", "id_vendedor", "id_vendedor");
    }
}
