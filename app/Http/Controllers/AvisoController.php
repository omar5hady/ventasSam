<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Aviso;
use Auth;

class AvisoController extends Controller
{
    public function store(Request $request){
        $aviso = new Aviso();
        $aviso->aviso = $request->aviso;
        $aviso->user_id = $request->id;
        $aviso->save();
    }

    public function getNoVistos(Request $request){
        $aviso = Aviso::where('visto','=',0)
        //->where('user_id','=',Auth::user()->id)
        ->orderBy('created_at','des')->get();

        return['avisos'=>$aviso];
    }

    public function ocultar(Request $request){
        $equipo = Aviso::findOrFail($request->id);
        $equipo->visto = 1;
        $equipo->save();
    }
}
