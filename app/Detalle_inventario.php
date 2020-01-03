<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_inventario extends Model
{
    protected $table = 'detalle_inventarios'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['inventario_id','equipo_id','cantidad'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function inventario(){
        return $this->belongsTo('App\Inventario');
    }

    public function equipo(){
        return $this->belongsTo('App\Equipo');
    }
}
