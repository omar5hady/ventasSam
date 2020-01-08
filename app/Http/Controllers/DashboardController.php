<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Venta;
use App\Corte;
use App\Desc_venta;
use App\Cuota;
use App\Sucursal;
use App\Inventario;
use App\Detalle_inventario;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function alcance(Request $request){
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();

        $cuota = Cuota::where('user_id','=',Auth::user()->id)
        ->where('month','=',Carbon::now()->month)->get();

        return['cuota'=>$cuota,'hoy'=>$fecha->day,'diasMes'=>$fecha->daysInMonth];
    }

    public function ventaDia(Request $request){
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();

        $corte = Corte::where('sucursal_id','=',Auth::user()->sucursal_id)->where('fecha','=',$hoy)->sum('total');
        $venta = Venta::where('sucursal_id','=',Auth::user()->sucursal_id)->where('fecha','=',$hoy)->sum('total');

        return['corte'=>$corte,'venta'=>$venta];
    }

    
}
