<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desc_venta extends Model
{
    protected $table = 'desc_ventas'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['venta_id','equipo_id','cantidad','total'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function venta(){
        return $this->belongsTo('App\Venta');
    }

    public function equipo(){
        return $this->belongsTo('App\Equipo');
    }


}
