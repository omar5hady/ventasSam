<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['sucursal_id','fecha','user_id','total','total_premium','total_smart','promocion'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function sucursal(){
        return $this->belongsTo('App\Sucursal');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function desc_venta(){
        return $this->hasMany('App\Desc_venta');
    }
}
