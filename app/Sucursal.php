<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['pv','cadena','tipo','condicion','venta_total'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function users(){
        return $this->hasMany('App\User');
    }

    public function venta(){
        return $this->hasMany('App\Venta');
    }

    public function inventario(){
        return $this->hasMany('App\Inventario');
    }

    public function corte(){
        return $this->hasMany('App\Corte');
    }

}
