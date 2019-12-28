<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipos'; // se referencia a que tabla pertenece el modelo
    protected $primaryKey = 'id'; //Referenciar la llave primaria
    protected $fillable = ['modelo','precio','condicion','tipo'];//asignacion en masa, definir las columnas de la tabla a la que se les mandaran valores

}
