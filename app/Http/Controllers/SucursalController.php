<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sucursal;

class SucursalController extends Controller
{
    public function index(Request $request){
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if($buscar==''){
            $sucursales = Sucursal::orderBy('condicion','DESC')->orderBy('cadena','ASC')->paginate(15);
        }
        else{
            $sucursales = Sucursal::where($criterio,'like','%'.$buscar.'%')
                ->orderBy('condicion','DESC')->orderBy('cadena','ASC')->paginate(15);
        }

        return [
            'pagination' => [
                'total'         => $sucursales->total(),
                'current_page'  => $sucursales->currentPage(),
                'per_page'      => $sucursales->perPage(),
                'last_page'     => $sucursales->lastPage(),
                'from'          => $sucursales->firstItem(),
                'to'            => $sucursales->lastItem(),
            ],
            'sucursales' => $sucursales
        ];
    }

    public function select(Request $request){
        $sucursales = Sucursal::where('condicion','=',1)->orderBy('cadena','ASC')->get();

        return['sucursales'=>$sucursales];
    }


    public function store(Request $request){
        $sucursal = new Sucursal();
        $sucursal->pv = $request->pv;
        $sucursal->cadena = $request->cadena;
        $sucursal->tipo = $request->tipo;
        $sucursal->save();
    }

    public function update(Request $request){
        $sucursal = Sucursal::findOrFail($request->id);
        $sucursal->pv = $request->pv;
        $sucursal->cadena = $request->cadena;
        $sucursal->tipo = $request->tipo;
        $sucursal->save();
    }

    public function activar(Request $request){
        $sucursal = Sucursal::findOrFail($request->id);
        $sucursal->condicion = 1;
        $sucursal->save();
    }

    public function desactivar(Request $request){
        $sucursal = Sucursal::findOrFail($request->id);
        $sucursal->condicion = 0;
        $sucursal->save();
    }

    public function updateVentas(Request $request){
        $sucursal = Sucursal::findOrFail($request->id);
        $sucursal->venta_total = $request->venta_total;
        $sucursal->save();
    }

}
