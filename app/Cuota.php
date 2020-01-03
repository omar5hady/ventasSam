<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['user_id','premium','smart','premium_real','smart_real',
                            'qty_premium','qty_smart','month','year',
                            'qty_premium_real','qty_smart_real'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\User');
    }
}
