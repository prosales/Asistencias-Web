<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedores extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'vendedores';
    protected $fillable = [
        'id_sucursal', 'nombre', 'codigo', 'telefono', 'usuario', 'password', 'estado', 'id_supervisor'
    ];

    protected $casts = [
        'id_sucursal' => 'integer',
        'id_supervisor' => 'integer'
    ];

    protected $hidden = [
        'password'
    ];

    public function sucursal()
    {
        return $this->hasOne("App\Sucursales", "id", "id_sucursal");
    }

    public function telefonos()
    {
    	return $this->hasMany("App\Telefonos", "id_vendedor", "id");
    }

    public function supervisor()
    {
        return $this->hasOne("App\Supervisores", "id", "id_supervisor");
    }

}
