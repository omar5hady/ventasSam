<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    protected $table = 'avisos'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['user_id','aviso','visto'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public function user(){
        return $this->belongsTo('App\User');
    }

}
