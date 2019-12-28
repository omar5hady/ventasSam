<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Equipo;

class EquipoController extends Controller
{
    public function index(Request $request){
        $buscar = $request->buscar;
        if($buscar==''){
            $equipos = Equipo::orderBy('condicion','DESC')->orderBy('modelo','ASC')->paginate(15);
        }
        else{
            $equipos = Equipo::where('modelo','like','%'.$buscar.'%')
                ->orderBy('condicion','DESC')->orderBy('modelo','ASC')->paginate(15);
        }

        return [
            'pagination' => [
                'total'         => $equipos->total(),
                'current_page'  => $equipos->currentPage(),
                'per_page'      => $equipos->perPage(),
                'last_page'     => $equipos->lastPage(),
                'from'          => $equipos->firstItem(),
                'to'            => $equipos->lastItem(),
            ],
            'equipos' => $equipos
        ];
    }


    public function store(Request $request){
        $equipo = new Equipo();
        $equipo->modelo = $request->modelo;
        $equipo->precio = $request->precio;
        $equipo->tipo = $request->tipo;
        $equipo->save();
    }

    public function update(Request $request){
        $equipo = Equipo::findOrFail($request->id);
        $equipo->modelo = $request->modelo;
        $equipo->precio = $request->precio;
        $equipo->tipo = $request->tipo;
        $equipo->save();
    }

    public function activar(Request $request){
        $equipo = Equipo::findOrFail($request->id);
        $equipo->condicion = 1;
        $equipo->save();
    }

    public function desactivar(Request $request){
        $equipo = Equipo::findOrFail($request->id);
        $equipo->condicion = 0;
        $equipo->save();
    }
}
