<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['modelo','precio','condicion','tipo'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores
    
    public function desc_venta(){
        return $this->hasMany('App\Desc_venta');
    }

    public function desc_corte(){
        return $this->hasMany('App\Desc_venta');
    }

    public function destalle_inventario(){
        return $this->hasMany('App\Detalle_inventario');
    }

}
