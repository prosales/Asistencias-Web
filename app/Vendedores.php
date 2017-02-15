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
        'id_sucursal', 'nombre', 'codigo', 'telefono', 'usuario', 'password'
    ];

    protected $casts = [
        'id_sucursal' => 'integer'
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

}
