<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desc_corte extends Model
{
    protected $table = 'desc_cortes'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['corte_id','equipo_id','cantidad','total'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function corte(){
        return $this->belongsTo('App\Corte');
    }

    public function equipo(){
        return $this->belongsTo('App\Equipo');
    }
}
