<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventarios'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['sucursal_id','fecha','vendedor','total',
                            'total_premium','total_smart','activo','hora'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function sucursal(){
        return $this->belongsTo('App\Sucursal');
    }

    public function detalle_inventario(){
        return $this->hasMany('App\Detalle_inventario');
    }

}
