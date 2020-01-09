<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Persona;
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
        $buscar = $request->buscar;

        if(Auth::user()->rol_id != 1){
            $cuota = Cuota::where('user_id','=',Auth::user()->id)
            ->where('month','=',Carbon::now()->month)->get();
        }
        else{
            if($buscar ==''){
                $cuota = Cuota::where('month','=',Carbon::now()->month)->get();
            }
            else{
                $cuota = Cuota::where('user_id','=',$buscar)
                ->where('month','=',Carbon::now()->month)->get();
            }
        }
        

        return['cuota'=>$cuota,'hoy'=>$fecha->day,'diasMes'=>$fecha->daysInMonth];
    }

    public function ventaDia(Request $request){
        $fecha = Carbon::now();
        $hoy =  $fecha->toDateString();
        $buscar = $request->buscar;

        

        if(Auth::user()->rol_id != 1){
            $corte = Corte::where('sucursal_id','=',Auth::user()->sucursal_id)->where('fecha','=',$hoy)->sum('total');
            $venta = Venta::where('sucursal_id','=',Auth::user()->sucursal_id)->where('fecha','=',$hoy)->sum('total');
        }
        else{
            if($buscar==''){
                $corte = Corte::where('fecha','=',$hoy)->sum('total');
                $venta = Venta::where('fecha','=',$hoy)->sum('total');
            }
            else{
                $user = User::findOrFail($buscar);
                $corte = Corte::where('sucursal_id','=',$user->sucursal_id)->where('fecha','=',$hoy)->sum('total');
                $venta = Venta::where('sucursal_id','=',$user->sucursal_id)->where('fecha','=',$hoy)->sum('total');
            }
            
        }
        

        return['corte'=>$corte,'venta'=>$venta];
    }

    
}
