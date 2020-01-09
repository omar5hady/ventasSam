<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'usuario', 'password', 'rol_id', 'condicion','sucursal_id'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function rol(){
        return $this->belongsTo('App\Rol');
    }

    public function sucursal(){
        return $this->belongsTo('App\Sucursal');
    }
 
    public function persona(){
        return $this->belongsTo('App\Persona');
    }

    public function venta(){
        return $this->hasMany('App\Venta');
    }

    public function corte(){
        return $this->hasMany('App\Corte');
    }

    public function cuota(){
        return $this->hasMany('App\Venta');
    }

    public function aviso(){
        return $this->hasMany('App\Aviso');
    }
}
