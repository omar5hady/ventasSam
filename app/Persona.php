<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['nombre','apellidos','celular'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

    public function user(){
        return $this->hasOne('App\User');
    }
}